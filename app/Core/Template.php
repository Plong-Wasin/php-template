<?php

namespace App\Core;

use Exception;

class Template
{
    protected $view = null;
    protected $data;
    protected $stacks = [];
    protected $unique;
    protected $sections = [];
    protected $yields = [];
    protected $currentSection;
    protected static $defaultViewDirectory = '';

    public static function setDefaultViewDirectory($path)
    {
        $realpath = realpath($path);
        if (!$realpath) {
            throw new Exception('Directory not found: ' . $realpath);
        }
        self::$defaultViewDirectory = $realpath;
    }

    public function __construct($view, $data = [])
    {
        $this->view = $view;
        $this->data = $data;
        $this->unique = uniqid();
    }

    public static function make($view, $data = [])
    {
        return new static($view, $data);
    }

    public function extend($parent)
    {
        extract($this->data, EXTR_SKIP);
        require $this->getViewFullPath($parent);
    }

    public function component($path,  $data = [])
    {
        extract($data, EXTR_SKIP);
        require $this->getViewFullPath($path);
    }

    public function stack($name)
    {
        echo $this->unique . 'stack-' . $name;
    }

    public function push($section)
    {
        $this->currentSection = $section;
        ob_start();
        return $this;
    }

    public function endPush()
    {
        $content = ob_get_clean();
        $this->stacks[$this->currentSection][] = $content;
    }

    public function showSection($yield,  $defaultValue = null)
    {
        echo $this->unique . 'yield-' . $yield;
        $this->yields[] = $yield;
        $this->sections[$yield] = isset($this->sections[$yield]) ? $this->sections[$yield] : $defaultValue;
    }

    public function startSection($section)
    {
        $this->currentSection = $section;
        ob_start();
    }

    public function endSection()
    {
        $this->sections[$this->currentSection] = ob_get_clean();
    }

    public function render()
    {
        extract($this->data);
        $level = ob_get_level();
        ob_start();
        require $this->getViewFullPath($this->view);
        $content = ob_get_clean();
        if ($content === false) {
            throw new Exception('Output buffering failed');
        }
        if (ob_get_level() != $level) {
            throw new Exception('Output buffering level changed');
        }
        $content = $this->replaceYields($content);
        $content = $this->replaceStacks($content);

        return $content;
    }
    public function getViewFullPath($path)
    {
        $realpath = false;
        if (!self::$defaultViewDirectory) {
            $realpath = realpath($path);
        } else {
            $realpath = realpath(self::$defaultViewDirectory . DIRECTORY_SEPARATOR . $path);
        }
        if (!$realpath) {
            throw new Exception('View not found: ' . $realpath);
        }

        return $realpath;
    }
    protected function replaceYields($content)
    {
        foreach ($this->yields as $yieldName) {
            $placeholder = $this->unique . 'yield-' . $yieldName;

            $sectionContent = $this->sections[$yieldName] ? $this->sections[$yieldName] : '';

            $content = str_replace($placeholder, $sectionContent, $content);
        }

        return $content;
    }

    protected function replaceStacks($content)
    {
        foreach ($this->stacks as $stackName => $stackContent) {
            $placeholder = $this->unique . 'stack-' . $stackName;
            $content = str_replace($placeholder, implode(PHP_EOL, $stackContent), $content);
        }

        return $content;
    }
}
