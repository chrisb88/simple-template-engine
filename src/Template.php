<?php
declare(strict_types=1);

namespace simpleTemplate;

use simpleTemplate\processors\EachProcessor;
use simpleTemplate\processors\SimpleVarProcessor;

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
        $eachProcessor = new EachProcessor($this->variables);
        $simpleVarProcessor = new SimpleVarProcessor($this->variables);

        $output = $this->templateContent;
        $output = $eachProcessor->process($output);
        $output = $simpleVarProcessor->process($output);

        return $output;
    }
}
