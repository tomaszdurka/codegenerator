<?php

namespace CodeGenerator;

use CodeGenerator\Exception\Exception;

class FunctionBlock extends Block {

    /** @var string|null */
    protected $_name;

    /** @var ParameterBlock[] */
    private $_parameters = array();

    /** @var string */
    protected $_code;

    /** @var DocBlock|string|null */
    protected $_docBlock;

    /** @var string */
    protected $_returnType;

    /**
     * @param callable|string|null $body
     */
    public function __construct($body = null) {
        $this->useDynamicDocBlock();
        if (null !== $body) {
            if ($body instanceof \Closure) {
                $this->extractFromClosure($body);
            } else {
                $this->setCode($body);
            }
        }
    }

    /**
     * @param string $name
     */
    public function setName($name) {
        $this->_name = (string) $name;
    }

    /**
     * @return string|null
     */
    public function getName() {
        return $this->_name;
    }

    /**
     * @param ParameterBlock $parameter
     * @throws Exception
     */
    public function addParameter(ParameterBlock $parameter) {
        if (array_key_exists($parameter->getName(), $this->_parameters)) {
            throw new Exception('Parameter `' . $parameter->getName() . '` is already set.');
        }
        $this->_parameters[$parameter->getName()] = $parameter;
    }

    /**
     * @return ParameterBlock[]
     */
    public function getParameters() {
        return $this->_parameters;
    }

    /**
     * @param string $code
     */
    public function setCode($code) {
        if (null !== $code) {
            $code = $this->_outdent((string) $code, true);
        }
        $this->_code = $code;
    }

    /**
     * @param DocBlock|string|null $docBlock
     */
    public function setDocBlock($docBlock) {
        $this->_docBlock = $docBlock;
    }

    public function useDynamicDocBlock() {
        $this->setDocBlock(new DynamicFunctionDocBlock($this));
    }

    /**
     * @return DocBlock|null|string
     */
    public function getDocBlock() {
        return $this->_docBlock;
    }

    /**
     * @return string
     */
    public function getReturnType() {
        return $this->_returnType;
    }

    /**
     * @param string $returnType
     */
    public function setReturnType($returnType) {
        $this->_returnType = $returnType;
    }

    /**
     * @param \ReflectionFunctionAbstract $reflection
     */
    public function setBodyFromReflection(\ReflectionFunctionAbstract $reflection) {
        /** @var $reflection \ReflectionMethod */
        if (is_a($reflection, '\\ReflectionMethod') && $reflection->isAbstract()) {
            $this->_code = null;
            return;
        }
        $file = new \SplFileObject($reflection->getFileName());
        $file->seek($reflection->getStartLine() - 1);

        $code = '';
        while ($file->key() < $reflection->getEndLine()) {
            $code .= $file->current();
            $file->next();
        }

        $begin = strpos($code, 'function');
        $code = substr($code, $begin);

        $begin = strpos($code, '{');
        $end = strrpos($code, '}');
        $code = substr($code, $begin + 1, $end - $begin - 1);
        $code = preg_replace('/^\s*[\r\n]+/', '', $code);
        $code = preg_replace('/[\r\n]+\s*$/', '', $code);

        if (!trim($code)) {
            $code = null;
        }
        $this->setCode($code);
    }

    /**
     * @param \ReflectionFunctionAbstract $reflection
     */
    public function setParametersFromReflection(\ReflectionFunctionAbstract $reflection) {
        foreach ($reflection->getParameters() as $reflectionParameter) {
            $parameter = ParameterBlock::buildFromReflection($reflectionParameter);
            $this->addParameter($parameter);
        }
    }

    /**
     * @param \ReflectionFunctionAbstract $reflection
     */
    public function setDocBlockFromReflection(\ReflectionFunctionAbstract $reflection) {
        $docBlock = $reflection->getDocComment();
        if ($docBlock) {
            $docBlock = preg_replace('/([\n\r])(' . self::$_indentation . ')+/', '$1', $docBlock);
            $this->setDocBlock($docBlock);
        } else {
            $this->setDocBlock(null);
        }
    }

    public function dump() {
        return $this->_dumpLine(
            $this->_dumpDocBlock(),
            $this->_dumpHeader() . $this->_dumpBody()
        );
    }

    /**
     * @return string|null
     */
    protected function _dumpDocBlock() {
        if (null === $this->_docBlock) {
            return null;
        }
        return (string) $this->_docBlock;
    }

    /**
     * @return string
     */
    protected function _dumpHeader() {
        $content = 'function';
        if ($this->_name) {
            $content .= ' ' . $this->_name;
        }
        $content .= '(';
        $content .= implode(', ', $this->_parameters);
        $content .= ')';
        return $content;
    }

    /**
     * @return string
     */
    protected function _dumpBody() {
        $code = $this->_code;
        if ($code) {
            $code = $this->_indent($code);
        }
        return $this->_dumpLine(' {', $code, '}');
    }

    /**
     * @param \ReflectionFunctionAbstract $reflection
     */
    public function extractFromReflection(\ReflectionFunctionAbstract $reflection) {
        $this->setBodyFromReflection($reflection);
        $this->setParametersFromReflection($reflection);
        $this->setDocBlockFromReflection($reflection);
    }

    /**
     * @param \Closure $closure
     */
    public function extractFromClosure(\Closure $closure) {
        $this->extractFromReflection(new \ReflectionFunction($closure));
    }
}
