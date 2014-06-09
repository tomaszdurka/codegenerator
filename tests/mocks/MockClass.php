<?php

class MockClass extends MockAbstractClass {

    const FOO = 1;

    /** @var array */
    public $foo = array(1, 2);

    /** @var int */
    protected $_bar = 1;

    private $_foo;

    /**
     * @return int
     */
    public function count() {
        return count($this->foo);
    }

    public function withTypeHinting(Countable $countable, array $array) {
    }

    public function defaultValues($defaultValue = null, $defaultArray = array()) {
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
}
