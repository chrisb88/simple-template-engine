<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use simpleTemplate\processors\EachProcessor;

final class EachProcessorTest extends TestCase
{
    public function testEachProcessorMultiLine()
    {
        $template = <<<EOT
Here is text
{{#each array}}
  text {{var1}} text and {{var2}}
{{/each}}
and more text here
EOT;

        $expected = <<<EOT
Here is text
  text and more text and even more text
  text text text and more text
and more text here
EOT;

        $processor = new EachProcessor([
            'array' => [
                [
                    'var1' => 'and more',
                    'var2' => 'even more text',
                ],
                [
                    'var1' => 'text',
                    'var2' => 'more text',
                ]
            ]
        ]);

        $output = $processor->process($template);
        $this->assertEquals($expected, $output);
    }

    public function testEachProcessorSingleLine()
    {
        $template = 'Here is text{{#each array}} text {{var1}} text and {{var2}}{{/each}} and more text here';
        $expected = 'Here is text text and more text and even more text text text text and more text and more text here';

        $processor = new EachProcessor([
            'array' => [
                [
                    'var1' => 'and more',
                    'var2' => 'even more text',
                ],
                [
                    'var1' => 'text',
                    'var2' => 'more text',
                ]
            ]
        ]);

        $output = $processor->process($template);
        $this->assertEquals($expected, $output);
    }

    public function testMissingVar()
    {
        $template = <<<EOT
Here is text
{{#each array}}
  text {{var1}} text and {{var2}}
{{/each}}
and more text here
EOT;

        $expected = <<<EOT
Here is text
  text {{var1}} text and even more text
  text text text and {{var2}}
and more text here
EOT;

        $processor = new EachProcessor([
            'array' => [
                [
                    'var2' => 'even more text',
                ],
                [
                    'var1' => 'text',
                ]
            ]
        ]);

        $output = $processor->process($template);
        $this->assertEquals($expected, $output);
    }
}
