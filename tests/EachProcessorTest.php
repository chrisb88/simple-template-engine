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

    public function testInvalidEach()
    {
        $template = <<<EOT
Here is text
{{#each array}}{{/each}}
and more text here
EOT;

        $processor = new EachProcessor([
            'array' => []
        ]);

        $output = $processor->process($template);
        $this->assertEquals($template, $output);
    }

    public function testExtractUnlessParams()
    {
        $processor = new EachProcessor();

        $params = $this->invokeMethod($processor, 'extractUnlessParams', ['A{{else}}B']);
        $this->assertEquals(['left' => 'A', 'right' => 'B'], $params);

        $params = $this->invokeMethod($processor, 'extractUnlessParams', ['A{{else}}']);
        $this->assertEquals(['left' => 'A', 'right' => ''], $params);

        $params = $this->invokeMethod($processor, 'extractUnlessParams', ['{{else}}B']);
        $this->assertEquals(['left' => '', 'right' => 'B'], $params);

        $params = $this->invokeMethod($processor, 'extractUnlessParams', ['A']);
        $this->assertEquals(['left' => 'A', 'right' => ''], $params);
    }

    public function testUnless()
    {
        $template = <<<EOT
Here is text
{{#each array}}
  text {{var1}} text and {{var2}}{{#unless @last}},{{else}}!{{/unless}}
{{/each}}
and more text here
EOT;

        $expected = <<<EOT
Here is text
  text and more text and even more text,
  text text text and more text!
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

    public function testUnlessWithoutElse()
    {
        $template = <<<EOT
Here is text
{{#each array}}
  text {{var1}} text and {{var2}}{{#unless @last}},{{/unless}}
{{/each}}
and more text here
EOT;

        $expected = <<<EOT
Here is text
  text and more text and even more text,
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

    public function testUnlessWithoutElse2()
    {
        $template = <<<EOT
Here is text
{{#each array}}
  text {{var1}} text and {{var2}}{{#unless @last}},{{else}}{{/unless}}
{{/each}}
and more text here
EOT;

        $expected = <<<EOT
Here is text
  text and more text and even more text,
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

    public function testUnlessWithoutElse3()
    {
        $template = <<<EOT
Here is text
{{#each array}}
  text {{var1}} text and {{var2}}{{#unless @last}}{{else}}!{{/unless}}
{{/each}}
and more text here
EOT;

        $expected = <<<EOT
Here is text
  text and more text and even more text
  text text text and more text!
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

    public function testUnlessFirst()
    {
        $template = <<<EOT
Here is text
{{#each array}}
  text {{var1}} text and {{var2}}{{#unless @first}},{{else}}!{{/unless}}
{{/each}}
and more text here
EOT;

        $expected = <<<EOT
Here is text
  text and more text and even more text!
  text and more and more text and even more text,
  text text text and more text,
and more text here
EOT;

        $processor = new EachProcessor([
            'array' => [
                [
                    'var1' => 'and more',
                    'var2' => 'even more text',
                ],
                [
                    'var1' => 'and more and more',
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

    public function testUnlessInvalid()
    {
        $template = <<<EOT
Here is text
{{#each array}}
  text {{var1}} text and {{var2}}{{#unless @invalid}},{{else}}!{{/unless}}
{{/each}}
and more text here
EOT;

        $expected = <<<EOT
Here is text
  text and more text and even more text{{#unless @invalid}},{{else}}!{{/unless}}
  text text text and more text{{#unless @invalid}},{{else}}!{{/unless}}
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

    /**
     * Call protected/private method of a class.
     * Stolen from: https://jtreminio.com/2013/03/unit-testing-tutorial-part-3-testing-protected-private-methods-coverage-reports-and-crap/
     *
     * @param object &$object    Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    private function invokeMethod(&$object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
