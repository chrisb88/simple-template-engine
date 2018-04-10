<?php
declare(strict_types=1);

namespace simpleTemplate\processors;

/**
 * Class BaseProcessor
 * Every processor should extend this class.
 * @package simpleTemplate\processors
 */
abstract class BaseProcessor
{
    /** @var array */
    private $variables = [];

    /**
     * Constructor
     * @param array $variables
     */
    public function __construct(array $variables = [])
    {
        $this->variables = $variables;
    }

    /**
     * Must be implemented in subclass.
     * @param string $template Text to process
     * @return string
     */
    public abstract function process(string $template): string;

    /**
     * Gets a variable by it's name.
     * @param string $name
     * @return mixed
     */
    protected function getVar(string $name)
    {
        if (isset($this->variables[$name])) {
            return $this->variables[$name];
        }

        return null;
    }
}
