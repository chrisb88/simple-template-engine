<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use simpleTemplate\TemplateProcessorConfig;

final class TemplateProcessorConfigTest extends TestCase
{
    public function testCanBeCreatedWithWithoutAnyProcessors(): void
    {
        $config = new TemplateProcessorConfig();
        $this->assertInstanceOf(TemplateProcessorConfig::class, $config);
    }

    public function testCanBeCreatedWithValidProcessors(): void
    {
        $config = new TemplateProcessorConfig([
            \simpleTemplate\processors\EachProcessor::class,
            \simpleTemplate\processors\SimpleVarProcessor::class
        ]);
        $this->assertInstanceOf(TemplateProcessorConfig::class, $config);
    }

    public function testCannotBeCreatedWithNotExistingProcessor(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new TemplateProcessorConfig([
            \simpleTemplate\processors\EachProcessor::class,
            'invalidClassName'
        ]);
    }

    public function testCannotBeCreatedWithInvalidProcessor(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new TemplateProcessorConfig([
            \simpleTemplate\processors\EachProcessor::class,
            self::class
        ]);
    }
}
