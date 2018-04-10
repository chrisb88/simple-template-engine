<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class SimpleVarProcessorTest extends TestCase
{
    public function testSimpleVarProcessorMultiLine()
    {
        $template = <<<EOT
Text {{var1}} and more text
even more text {{var2}} here
{{var3}} at the beginning of line
and at the end of the line {{var4}}
EOT;

        $expected = <<<EOT
Text number one and more text
even more text stands here
I am at the beginning of line
and at the end of the line is me
EOT;

        $processor = new \simpleTemplate\processors\SimpleVarProcessor([
            'var1' => 'number one',
            'var2' => 'stands',
            'var3' => 'I am',
            'var4' => 'is me',
        ]);

        $output = $processor->process($template);
        $this->assertEquals($expected, $output);
    }

    public function testSimpleVarProcessorSingleLine()
    {
        $template = <<<EOT
{{var1}} here {{var2}} more text {{var3}}.
EOT;

        $expected = <<<EOT
Text here and more text here.
EOT;

        $processor = new \simpleTemplate\processors\SimpleVarProcessor([
            'var1' => 'Text',
            'var2' => 'and',
            'var3' => 'here',
        ]);

        $output = $processor->process($template);
        $this->assertEquals($expected, $output);
    }

}
