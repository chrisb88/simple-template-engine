<?php
declare(strict_types=1);

namespace simpleTemplate;

class Template
{
    private $templateFile;
    private $processorConfig;
    private $templateContent;
    private $variables = [];

    public function __construct($templateFile, TemplateProcessorConfig $processorConfig)
    {
        if (!file_exists($templateFile) || !is_file($templateFile) || !is_readable($templateFile)) {
            throw new \InvalidArgumentException(sprintf('Template is not a valid file: "%s".', $templateFile));
        }

        $this->templateFile = $templateFile;
        $this->processorConfig = $processorConfig;
    }

    public function setVar($name, $value)
    {
        $this->variables[$name] = $value;
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
        return $this->processorConfig->processAll($this->templateContent, $this->variables);
    }
}
