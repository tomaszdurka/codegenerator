<?php

namespace TestsCodeGenerator;

use CodeGenerator\ClassBlock;
use CodeGenerator\FileBlock;

class CG_ClassTest extends \PHPUnit_Framework_TestCase {

    public function testDump() {
        $classes = array('CodeGeneratorMocks\\MockAbstractClass', 'CodeGeneratorMocks\\MockClass');
        foreach ($classes as $className) {
            $file = new FileBlock();

            $reflectionClass = new \ReflectionClass($className);
            $reflectedClass = ClassBlock::buildFromReflection($reflectionClass);
            $file->addBlock($reflectedClass);

            $actual = $file->dump();
            $expected = file_get_contents($reflectionClass->getFileName());
            $this->assertSame($expected, $actual);
        }
    }

    public function testGetName() {
        $className = 'Foo';
        $class = new ClassBlock($className, 'Bar', array('Countable'));
        $this->assertSame($className, $class->getName());
    }
}
