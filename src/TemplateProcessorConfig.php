<?php
declare(strict_types = 1);

namespace simpleTemplate;

use simpleTemplate\processors\BaseProcessor;

final class TemplateProcessorConfig
{
    /** @var BaseProcessor[] */
    private $processors = [];

    /**
     * @param string[] $processors
     */
    public function __construct(array $processors = [])
    {
        foreach ($processors as $name) {
            if (!class_exists($name)) {
                throw new \InvalidArgumentException(sprintf('%s is not a valid processor.', $name));
            }
            /** @var BaseProcessor $processor */
            $processor = new $name();
            if (!$processor instanceof BaseProcessor) {
                throw new \InvalidArgumentException(sprintf('%s is not a valid processor.', $name));
            }

            $this->processors[] = $processor;
        }
    }

    public function processAll($template, $variables)
    {
        $output = $template;
        foreach ($this->processors as $processor) {
            /** @var BaseProcessor $processor */
            $output = $processor->setVariables($variables)
                ->process($output);
        }

        return $output;
    }
}
