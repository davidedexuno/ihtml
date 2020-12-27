<?php

namespace iHTML\Project;

use iHTML\Document\Document;
use iHTML\Ccs\Ccs;
use Exception;

class Project
{
    private $root;

    private $project;

    public function __construct($project)
    {
        $project_dir = realpath($project);

        // PROJECT VALIDATION
        if (!$project_dir) {
            throw new Exception("Project $project_dir not found.");
        }
        if (!is_dir($project_dir)) {
            throw new Exception("Project $project_dir is not a dir.");
        }
        if (!file_exists($project_dir.'/project.json')) {
            throw new Exception("Project file $project_dir/project.json not found.");
        }

        $project = json_decode(file_get_contents($project_dir.'/project.json'));
        if (!$project) {
            throw new Exception("Malformed project file $project_dir/project.json.");
        }
        if (!is_object($project)) {
            throw new Exception("Malformed project file $project_dir/project.json.");
        }

        $this->root = $project_dir;

        $this->project = $project;
    }
    
    public function getRoot(): string
    {
        return $this->root;
    }

    public function getProject(): array
    {
        return $this->project;
    }

    public function render($out, ?string $index = null)
    {
        $root = $this->root;
        $out_dir = working_dir(getcwd(), $out);

        if (file_exists($out_dir) && (!is_dir($out_dir) || !is_writable($out_dir))) {
            throw new Exception('Error creating output directory.');
        }
        if (!file_exists($out_dir) && !mkdir($out, 0777, true)) {
            throw new Exception('Error creating output directory.');
        }

        // COMPILE ALL FILES
        foreach ($this->project as $output => list($html, $apply)) {
            $document = new Document(working_dir($root, $html));
            $ccs = new Ccs(working_dir($root, $apply));
            $ccs->applyTo($document);
            $document->render(working_dir($out_dir, $output ?: './'), $index);
        }
    }
}
