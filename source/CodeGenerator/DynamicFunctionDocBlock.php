<?php

namespace CodeGenerator;

class DynamicFunctionDocBlock extends FunctionDocBlock {

    /** @var FunctionBlock */
    private $_functionBlock;

    /**
     * @param FunctionBlock $functionBlock
     */
    public function __construct(FunctionBlock $functionBlock) {
        $this->_functionBlock = $functionBlock;
        parent::__construct();
    }

    public function _getParameters() {
        $parameters = parent::_getParameters();
        foreach ($this->_functionBlock->getParameters() as $parameterBlock) {
            $types = [];
            if ($parameterBlock->getType()) {
                $types[] = $parameterBlock->getType();
            }
            if ($parameterBlock->isOptional()) {
                $types[] = 'null';
            }
            $parameters[] = ['name' => $parameterBlock->getName(), 'type' => $types, 'description' => null];
        }
        return $parameters;
    }

    protected function _getReturnType() {
        $returnType = parent::_getReturnType();
        if (null !== $returnType) {
            return $returnType;
        }
        return $this->_functionBlock->getReturnType();
    }

}
