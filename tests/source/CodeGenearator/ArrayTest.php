<?php

namespace TestsCodeGenerator;

use CodeGenerator\ArrayBlock;

class CG_ArrayTest extends \PHPUnit_Framework_TestCase {

    public function testDumpShort() {
        $value = array('foo', 'bar');
        $array = new ArrayBlock($value);
        $this->assertNotRegExp("/\n/", $array->dump());
        $this->_assertSame($value, $array);
    }

    public function testDumpLong() {
        $value = array_fill(0, 100, 'foo');
        $array = new ArrayBlock($value);
        $this->assertRegExp("/\n    /", $array->dump());
        $this->assertCount(count($value) + 2, explode("\n", $array->dump()));
        $this->_assertSame($value, $array);
    }

    /**
     * @param array    $expected
     * @param ArrayBlock $actual
     */
    private function _assertSame(array $expected, ArrayBlock $actual) {
        $code = 'return ' . $actual->dump() . ';';
        $evaluatedActual = eval($code);
        $this->assertSame($expected, $evaluatedActual);
    }
}
