<?php

namespace iHTML\Project;

use iHTML\Document\Document;
use iHTML\Ccs\Ccs;

class Project
{
    private $root;

    private $implicit;

    private $templates;

    public function __construct($project)
    {
        $project_dir = realpath($project);

        // PROJECT VALIDATION
        if (!$project_dir) {
            throw new \Exception('Project not found.');
        }
        if (!is_dir($project_dir)) {
            throw new \Exception('Project is not a dir.');
        }
        if (!file_exists($project_dir.'/project.json')) {
            throw new \Exception('Project file not found.');
        }

        $project = json_decode(file_get_contents($project_dir.'/project.json'));
        if (!$project) {
            throw new \Exception('Malformed project file.');
        }
        if (empty($project->implicit)) {
            throw new \Exception('Malformed project file.');
        }
        if (!isset($project->templates)) {
            throw new \Exception('Malformed project file.');
        }
        if (!is_array($project->templates)) {
            throw new \Exception('Malformed project file.');
        }

        $this->root = $project_dir;

        $this->implicit = $project->implicit;

        $this->templates = $project->templates;
    }
    
    public function getRoot(): string
    {
        return $this->root;
    }

    public function getTemplates(): array
    {
        return $this->templates;
    }

    public function render($out)
    {
        $root = $this->root;
        $out_dir = working_dir(getcwd(), $out);

        if (file_exists($out_dir) && (!is_dir($out_dir) || !is_writable($out_dir))) {
            throw new \Exception('Error creating output folder.');
        }
        if (!file_exists($out_dir) && !mkdir($out, 0777, true)) {
            throw new \Exception('Error creating output folder.');
        }
        
        // SOLVE IMPLICIT FILES
        foreach ($this->templates as &$template) {
            if (substr($template->output, -1) == '/') {
                $template->output .= $this->implicit;
            }
        }
        unset($template);

        // COMPILE ALL FILES
        foreach ($this->templates as $template) {
            $document = new Document(working_dir($root, $template->html));

            $ccs = new Ccs(working_dir($root, $template->apply));
            $ccs->applyTo($document);

            $document->render(working_dir($out_dir, $template->output));
        }
    }
}
