<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use simpleTemplate\processors\NullProcessor;

final class BaseProcessorTest extends TestCase
{
    public function getVar()
    {
        $processor = new NullProcessor();
        $this->assertNull($processor->getVar('notExistingVariable'));
    }

    public function testSetVars()
    {
        // setVars() should overwrite any existing variables
        $processor = new NullProcessor([
            'var1' => 'testA',
            'var2' => 'testB',
            'var3' => 'testC',
        ]);

        $this->assertEquals('testA', $processor->getVar('var1'));
        $this->assertEquals('testB', $processor->getVar('var2'));
        $this->assertEquals('testC', $processor->getVar('var3'));

        $processor->setVariables([
            'var4' => 'testD'
        ]);

        $this->assertEquals('testD', $processor->getVar('var4'));
        $this->assertNull($processor->getVar('var1'));
        $this->assertNull($processor->getVar('var2'));
        $this->assertNull($processor->getVar('var3'));

        // also it should return itself
        $return = $processor->setVariables([]);
        $this->assertEquals($processor, $return);
    }
}
