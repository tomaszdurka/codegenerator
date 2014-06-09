<?php

class ClassTest extends PHPUnit_Framework_TestCase {

    public function testDump() {
        $classes = array('MockAbstractClass', 'MockClass');
        foreach ($classes as $className) {
            require DIR_TESTS . 'mocks/' . $className . '.php';
            $file = new CG_File();

            $reflectionClass = new ReflectionClass($className);
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
