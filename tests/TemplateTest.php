<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use simpleTemplate\Template;
use simpleTemplate\TemplateProcessorConfig;

final class TemplateTest extends TestCase
{
    public static $templatePath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR;

    public function testCanBeCreatedFromValidTemplate(): void
    {
        $templateFile = self::$templatePath .  'template.tmpl';
        $template = new Template($templateFile, new TemplateProcessorConfig());
        $this->assertInstanceOf(Template::class, $template);
    }

    public function testCannotBeCreatedFromInvalidTemplate(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Template('not-existing-file.tmpl', new TemplateProcessorConfig());
    }

    public function testCorrectOutput(): void
    {
        $expected = "Hey chris, here's a poem for you:\n"
            . "\n"
            . "  roses are red\n"
            . "  violets are blue\n"
            . "  you are able to solve this\n"
            . "  we are interested in you\n";

        $config = new TemplateProcessorConfig([
            \simpleTemplate\processors\EachProcessor::class,
            \simpleTemplate\processors\SimpleVarProcessor::class
        ]);

        $templateFile = self::$templatePath .  'template.tmpl';
        $template = new Template($templateFile, $config);
        $template->setVar('Name', 'chris');
        $template->setVar('Stuff', [
            [
                'Thing' => 'roses',
                'Desc' => 'red'
            ],
            [
                'Thing' => 'violets',
                'Desc' => 'blue'
            ],
            [
                'Thing' => 'you',
                'Desc' => 'able to solve this'
            ],
            [
                'Thing' => 'we',
                'Desc' => 'interested in you'
            ]
        ]);

        $output = $template->render();
        $this->assertEquals($expected, $output);
    }

    public function testCorrectOutputForExtraTemplate(): void
    {
        $expected = "Hey chris, here's a slightly better formatted poem for you:\n"
            . "\n"
            . "  roses are red,\n"
            . "  violets are blue,\n"
            . "  you are able to solve this,\n"
            . "  we are interested in you!\n";

        $config = new TemplateProcessorConfig([
            \simpleTemplate\processors\EachProcessor::class,
            \simpleTemplate\processors\SimpleVarProcessor::class
        ]);

        $templateFile = self::$templatePath .  'extra.tmpl';
        $template = new Template($templateFile, $config);
        $template->setVar('Name', 'chris');
        $template->setVar('Stuff', [
            [
                'Thing' => 'roses',
                'Desc' => 'red'
            ],
            [
                'Thing' => 'violets',
                'Desc' => 'blue'
            ],
            [
                'Thing' => 'you',
                'Desc' => 'able to solve this'
            ],
            [
                'Thing' => 'we',
                'Desc' => 'interested in you'
            ]
        ]);

        $output = $template->render();
        $this->assertEquals($expected, $output);
    }
}
