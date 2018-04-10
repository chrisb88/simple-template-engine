<?php
declare(strict_types=1);

namespace simpleTemplate\processors;

/**
 * SimpleVarProcessor processes a simple variable.
 * ```
 * Text {{variable1}} more template {{variable2}} even more template
 * ```
 * @package simpleTemplate\processors
 */
class SimpleVarProcessor extends BaseProcessor
{
    /**
     * @param string $template Text to process
     * @return string
     */
    public function process(string $template): string
    {
        $template = preg_replace_callback('/{{(\w+)}}/', function($matches) {
            if (!empty($matches[1])) {
                $var = $this->getVar($matches[1]);
                if ($var !== null) {
                    return $var;
                }
            }

            return $matches[0];
        }, $template);

        return $template;
    }
}
