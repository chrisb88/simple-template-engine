<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use simpleTemplate\Template;

final class TemplateTest extends TestCase
{
    public static $templateFile = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'template.tmpl';

    public function testCanBeCreatedFromValidTemplate(): void
    {
        $template = new Template(self::$templateFile);
        $this->assertInstanceOf(Template::class, $template);
    }

    public function testCannotBeCreatedFromInvalidTemplate(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Template('not-existing-file.tmpl');
    }

    public function testCorrectOutput(): void
    {
        $expected = "Hey chris, here's a poem for you:\n"
            . "\n"
            . "  roses are red\n"
            . "  violets are blue\n"
            . "  you are able to solve this\n"
            . "  we are interested in you\n";

        $template = new Template(self::$templateFile);
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
