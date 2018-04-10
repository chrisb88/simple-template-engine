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
                    foreach ($var as $index => $row) {
                        $out .= self::substitute($subTemplate, $row, (int) $index, count($var));
                    }

                    return $out;
                }
            }

            return $matches[0];
        }, $template);

        return $template;
    }

    private static function substitute(string $text, array $vars, int $currentIndex, int $maxLength)
    {
        $text = self::processUnless($text, $currentIndex, $maxLength);

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

    private static function processUnless(string $text, int $currentIndex, int $maxLength): string
    {
        $text = preg_replace_callback('/{{\#unless (.+?)}}(.+?){{\/unless}}/', function($matches) use ($currentIndex, $maxLength) {
            $params = self::extractUnlessParams($matches[2]);
            switch ($matches[1]) {
                case '@last':
                    return $currentIndex < $maxLength - 1 ? $params['left'] : $params['right'];
                case '@first':
                    return $currentIndex > 0 ? $params['left'] : $params['right'];
                default:
                    return $matches[0];
            }
        }, $text);

        return $text;
    }

    private static function extractUnlessParams($text)
    {
        $pattern = '/
            (.+)({{else}})(.+?)| #matches ,{{else}}!
            ({{else}})(.+)|      #matches {{else}}!
            (.+)({{else}})|      #matches ,{{else}}
            (.+)$                #matches ,
        /x';

        if (preg_match($pattern, $text, $matches) === false) {
            throw new \Exception();
        }

        // ,{{else}}!
        if (!empty($matches[1]) && !empty($matches[2]) && !empty($matches[3])) {
            return [
                'left' => $matches[1],
                'right' => $matches[3]
            ];
        }

        // {{else}}!
        if (!empty($matches[4]) && !empty($matches[5])) {
            return [
                'left' => '',
                'right' => $matches[5]
            ];
        }

        // ,{{else}} or just ,
        if ((!empty($matches[6]) && !empty($matches[7])) || !empty($matches[8]) ) {
            return [
                'left' => isset($matches[8]) ? $matches[8] : $matches[6],
                'right' => ''
            ];
        }

        return [
            'left' => '',
            'right' => ''
        ];
    }
}
