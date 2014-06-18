<?php

namespace TestsCodeGenerator;

use CodeGenerator\CG_Class;
use CodeGenerator\CG_File;

class CG_ClassTest extends \PHPUnit_Framework_TestCase {

    public function testDump() {
        $classes = array('CodeGeneratorMocks\\MockAbstractClass', 'CodeGeneratorMocks\\MockClass');
        foreach ($classes as $className) {
            $file = new CG_File();

            $reflectionClass = new \ReflectionClass($className);
            $reflectedClass = CG_Class::buildFromReflection($reflectionClass);
            $file->addBlock($reflectedClass);

            $actual = $file->dump();
            $expected = file_get_contents($reflectionClass->getFileName());
            $this->assertSame($expected, $actual);
        }
    }

    public function testGetName() {
        $className = 'Foo';
        $class = new CG_Class($className, 'Bar', array('Countable'));
        $this->assertSame($className, $class->getName());
    }
}
