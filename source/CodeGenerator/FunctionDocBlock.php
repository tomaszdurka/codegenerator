<?php

namespace CodeGenerator;

class FunctionDocBlock extends DocBlock {

    /** @var array */
    private $_parameters;

    /** @var string|null */
    private $_returnType;

    public function __construct() {
        $this->_parameters = [];
        parent::__construct();
    }

    /**
     * @param string               $name
     * @param string|string[]|null $types
     * @param string|null          $description
     */
    public function addParameter($name, $types = null, $description = null) {
        $this->_parameters[] = ['name' => $name, 'type' => (array) $types, 'description' => $description];
    }

    /**
     * @param $type
     */
    public function setReturnType($type) {
        $this->_returnType = $type;
    }

    /**
     * @return string|null
     */
    protected function _getReturnType() {
        return $this->_returnType;
    }

    /**
     * @return array
     */
    protected function _getParameters() {
        return $this->_parameters;
    }

    protected function _getEntries() {
        $entries = parent::_getEntries();
        foreach ($this->_getParameters() as $parameter) {
            $typesString = join('|', $parameter['type']);
            $entries[] = "@param {$typesString} {$parameter['name']} {$parameter['description']}";
        }

        if (null !== $this->_getReturnType()) {
            $entries[] = "@return {$this->_getReturnType()}";
        }
        return $entries;
    }
}
