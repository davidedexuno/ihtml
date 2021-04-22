<?php

namespace iHTML\Project;

use iHTML\Document\Document;
use iHTML\Ccs\CcsFile;
use Exception;
use SplFileInfo;
use SplFileObject;
use Directory;
use IhtmlFile;

class Project
{
    private $root;

    private $project;

    public function __construct(Directory $project)
    {
        $this->root = $project->path;
        if (!file_exists("{$this->root}/project.json")) {
            throw new Exception("Project file {$this->root}/project.json not found.");
        }
        $this->project = json_decode(file_get_contents("{$this->root}/project.json"));
        if (!is_object($this->project)) {
            throw new Exception("Malformed project file {$this->root}/project.json.");
        }
    }
    
    public function render(SplFileInfo $out_dir, string $index = null)
    {
        $this->createDir($out_dir);
        if (!$out_dir->isDir()) {
            throw new Exception('Error creating output directory.');
        }
        if (!$out_dir->isWritable()) {
            throw new Exception('Error creating output directory.');
        }
        // COMPILE ALL FILES
        foreach ($this->project as $output => [$html, $apply]) {
            $document = new Document(new SplFileObject(working_dir($this->root, $html)));
            $ccs      = new CcsFile(new SplFileObject(working_dir($this->root, $apply)));
            $ccs->applyTo($document);
            $document->render();
            if ($index) {
                $document->save(new IhtmlFile(working_dir($out_dir, $output ?: './')), $index);
            } else {
                $document->save(new IhtmlFile(working_dir($out_dir, $output ?: './')));
            }
        }
    }
    private function createDir(SplFileInfo $dir)
    {
        if (file_exists($dir)) {
            return;
        }
        if (!mkdir($dir, 0777, true)) {
            throw new Exception("Error creating {$dir} directory.");
        }
    }
}
