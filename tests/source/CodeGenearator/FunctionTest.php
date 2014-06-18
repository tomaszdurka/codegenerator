<?php

namespace TestsCodeGenerator;

use CodeGenerator\FunctionBlock;

class CG_FunctionTest extends \PHPUnit_Framework_TestCase {

    public function testExtractFromClosure() {
        $closure = function ($a, $b) {
            return $a * $b;
        };
        $function = new FunctionBlock($closure);
        eval('$multiply = ' . $function->dump() . ';');
        /** @var $multiply \Closure */
        $this->assertSame(12, $multiply(3, 4));
    }

    public function testSetCodeString() {
        $function = new FunctionBlock('return true;');
        eval('$true = ' . $function->dump() . ';');
        /** @var $true \Closure */
        $this->assertTrue($true());
    }
}
