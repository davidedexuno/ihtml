<?php

namespace iHTML\Project;

use iHTML\Document\Document;
use iHTML\Ccs\CcsFile;
use Exception;
use SplFileInfo;
use SplFileObject;
use Directory;
use IhtmlFile;
use Illuminate\Support\Collection;
use Symfony\Component\Yaml\Yaml;

class Project
{
    private $root;

    private Collection $project;

    public function __construct(Directory $project)
    {
        $this->root = $project->path;
        if (!file_exists("{$this->root}/project.yaml")) {
            throw new Exception("Project file {$this->root}/project.yaml not found.");
        }
        $project = Yaml::parseFile("{$this->root}/project.yaml");
        if (!is_array($project)) {
            throw new Exception("Malformed project file {$this->root}/project.yaml.");
        }
        $this->project = collect($project)
                        ->map(
                            fn ($a, $output) =>
                            (object)[
                                'document'  => new Document(new SplFileObject(working_dir($this->root, $a[0]))),
                                'ccs'       => new CcsFile(new SplFileObject(working_dir($this->root, $a[1]))),
                                'html'      => $a[0],
                                'apply'     => $a[1],
                                'output'    => $output,
                            ]
                        );
    }
    
    public function get()
    {
        return $this->project;
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
        $this->project->map(
            function ($res) use ($out_dir, $index) {
                $res->ccs->applyTo($res->document);
                $res->document->render();
                $res->document->save(new IhtmlFile(working_dir($out_dir, $res->output ?: './')), ...($index ? [ $index ] : [ ]));
            }
        );
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
