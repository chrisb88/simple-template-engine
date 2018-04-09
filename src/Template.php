<?php
declare(strict_types=1);

namespace simpleTemplate;

class Template
{
    private $templateFile;
    private $templateContent;
    private $variables = [];

    public function __construct($templateFile)
    {
        if (!file_exists($templateFile) || !is_file($templateFile) || !is_readable($templateFile)) {
            throw new \InvalidArgumentException(sprintf('Template is not a valid file: "%s".', $templateFile));
        }

        $this->templateFile = $templateFile;
    }

    public function setVar($name, $value)
    {
        $this->variables[$name] = $value;
    }

    public function getVar($name)
    {
        if (isset($this->variables[$name])) {
            return $this->variables[$name];
        }

        return false;
    }

    public function render(): string
    {
        $this->readTemplate();
        return $this->processTemplate();
    }

    protected function readTemplate(): void
    {
        $this->templateContent = file_get_contents($this->templateFile);
        if ($this->templateContent === false) {
            throw new \RuntimeException(sprintf('Could not read template file "%s".', $this->templateFile));
        }
    }

    protected function processTemplate(): string
    {
        $output = $this->templateContent;
        $output = $this->processEach($output);
        $output = $this->substituteSimpleVars($output);

        return $output;
    }

    protected function substituteSimpleVars($text): string
    {
        $text = preg_replace_callback('/{{(\w+)}}/', function($matches) {
            if (isset($matches[1])) {
                $var = $this->getVar($matches[1]);
                if ($var !== false) {
                    return $var;
                }
            }

            return $matches[0];
        }, $text);

        return $text;
    }

    protected function processEach($text)
    {
        $text = preg_replace_callback('/({{\#each (.+?)}}\n?)(.+)({{\/each}}\n?)/s', function($matches) {
            if (isset($matches[2]) && isset($matches[3])) {
                $var = $this->getVar($matches[2]);
                if ($var !== false) {
                    $out = '';
                    $subTemplate = $matches[3];
                    foreach ($var as $row) {
                        $out .= $this->substitute($subTemplate, $row);
                    }

                    return $out;
                }
            }

            return $matches[0];
        }, $text);

        return $text;
    }

    private function substitute($text, $vars)
    {
        $text = preg_replace_callback('/{{(\w+)}}/', function($matches) use ($vars) {
            if ($matches[1]) {
                foreach ($vars as $var => $value) {
                    if ($matches[1] === $var) {
                        return $value;
                    }
                }
            }

            return $matches[0];
        }, $text);

        return $text;
    }
}
