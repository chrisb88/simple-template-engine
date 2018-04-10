<?php
declare(strict_types = 1);

namespace simpleTemplate\processors;

/**
 * NullProcessor is mainly used for testing purposes.
 * It does nothing.
 * @package simpleTemplate\processors
 */
class NullProcessor extends BaseProcessor
{
    /**
     * @param string $template Text to process
     * @return string
     */
    public function process(string $template): string
    {
        return $template;
    }

    /**
     * Gets a variable by it's name.
     * @param string $name
     * @return mixed
     */
    public function getVar(string $name)
    {
        return parent::getVar($name);
    }
}
