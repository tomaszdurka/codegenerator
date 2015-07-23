<?php

namespace TestsCodeGenerator;

use CodeGenerator\FileBlock;
use CodeGenerator\MethodBlock;
use CodeGenerator\TraitBlock;

class CG_TraitTest extends \PHPUnit_Framework_TestCase
{
    public function testDump()
    {
        $file = new FileBlock();

        $reflectionClass = new \ReflectionClass('\\CodeGeneratorMocks\\MockTrait');

        $reflectedClass = TraitBlock::buildFromReflection($reflectionClass);
        $file->addBlock($reflectedClass);

        $actual = $file->dump();
        $expected = file_get_contents($reflectionClass->getFileName());
        $this->assertSame($expected, $actual);
    }

    public function testDumpComposite()
    {
        /**
         * Traits that contain USE are treated like the contain the methods and properties themselves
         * might not be the best solution to handle them like this
         */
        $file = new FileBlock();

        $reflectionClass = new \ReflectionClass('\\CodeGeneratorMocks\\MockCompositeTrait');

        $reflectedClass = TraitBlock::buildFromReflection($reflectionClass);
        $file->addBlock($reflectedClass);

        $actual = $file->dump();

        $expected = <<<'TEST'
<?php

namespace CodeGeneratorMocks;

trait MockCompositeTrait {

    /** @var array */
    public $foo = array(1, 2);

    /** @var int */
    protected $_bar = 1;

    private $_foo;

    /** @var int */
    protected $_bar2 = 1;

    /**
     * @return int
     */
    public function count() {
        return count($this->foo);
    }

    public function withTypeHinting(\Countable $countable, array $array, callable $callable) {
        echo 1;
    }

    public function defaultValues($defaultValue = null, $defaultArray = array()) {
        echo 2;
    }

    public function withReferenceParam(&$param) {
    }

    protected function abstractMethod() {
    }

    private function _foo() {
        // comment
        // indentation
        // back
    }

    public static function staticMethod() {
    }

    /**
     * @return bool
     */
    public function otherMethod() {
        return false;
    }
}

TEST;
        $this->assertSame($expected, $actual);
    }


    public function testGetName()
    {
        $className = 'Foo';
        $class = new TraitBlock($className);
        $this->assertSame($className, $class->getName());
    }

    public function testByHand()
    {
        $file = new FileBlock();

        $trait = new TraitBlock('TestTrait');
        $trait->addUse('\\CodeGeneratorMocks\\MockCompositeTrait');
        $trait->addMethod(new MethodBlock('testMethod', 'echo 1;'));

        $file->addBlock($trait);

        $expected = <<<TEST
<?php

trait TestTrait {

    use \CodeGeneratorMocks\MockCompositeTrait;

    public function testMethod() {
        echo 1;
    }
}

TEST;
        $this->assertSame($expected, $file->dump());
    }
}
