<?php

class CG_Constant extends CG_Block {

    /** @var string */
    private $_name;

    /** @var string|int */
    private $_value;

    /**
     * @param string     $name
     * @param string|int $value
     */
    public function __construct($name, $value) {
        $this->_name = (string) $name;
        $this->_value = $value;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->_name;
    }

    public function dump() {
        return 'const ' . $this->_name . ' = ' . var_export($this->_value, true) . ';';
    }
}
