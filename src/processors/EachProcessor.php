<?php
declare(strict_types=1);

namespace simpleTemplate\processors;

/**
 * EachProcessor processes each loops.
 * ```
 * {{#each Stuff}}
 *   <...>
 * {{/each}}
 * ```
 * @package simpleTemplate\processors
 */
class EachProcessor extends BaseProcessor
{
    private static $regex = '/({{\#each (.+?)}}(\r\n|\r|\n)?)(.+)({{\/each}}(\r\n|\r|\n)?)/s';

    /**
     * @param string $template Text to process
     * @return string
     */
    public function process(string $template): string
    {
        $template = preg_replace_callback(self::$regex, function($matches) {
            if (isset($matches[2]) && isset($matches[4])) {
                /** @var array $var */
                $var = $this->getVar($matches[2]);
                if ($var !== null) {
                    $out = '';
                    $subTemplate = $matches[4];
                    foreach ($var as $row) {
                        $out .= $this->substitute($subTemplate, $row);
                    }

                    return $out;
                }
            }

            return $matches[0];
        }, $template);

        return $template;
    }

    private function substitute(string $text, array $vars)
    {
        $text = preg_replace_callback('/{{(\w+)}}/', function($matches) use ($vars) {
            if (isset($matches[1])) {
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
