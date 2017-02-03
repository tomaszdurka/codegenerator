<?php

namespace CodeGenerator;

use CodeGenerator\Exception\Exception;

class ParameterBlock extends Block {

    /** @var string */
    private $_name;

    /** @var string|null */
    private $_type;

    /** @var mixed */
    private $_defaultValue;

    /** @var boolean */
    private $_defaultValueAvailable;

    /** @var boolean */
    private $_passedByReference;

    /** @var boolean */
    private $_variadic;

    /** @var boolean */
    private $_optional;

    /**
     * @param string       $name
     * @param string|null  $type
     * @param bool|null    $defaultValueAvailable
     * @param mixed|null   $defaultValue
     * @param boolean|null $passedByReference
     * @param boolean|null $variadic
     * @param boolean|null $optional
     * @throws Exception
     */
    public function __construct($name, $type = null, $defaultValueAvailable = null, $defaultValue = null, $passedByReference = null, $variadic = null, $optional = null) {
        if (!$defaultValueAvailable && null !== $defaultValue) {
            throw new Exception('Cannot set default value for parameter without default value available');
        }
        if ($this->_defaultValueAvailable && $variadic) {
            throw new Exception('Cannot set default value for variadic parameters');
        }

        $this->_name = (string) $name;
        if (null !== $type) {
            $this->_type = (string) $type;
        }
        $this->_defaultValueAvailable = (bool) $defaultValueAvailable;
        if ($this->_defaultValueAvailable) {
            $this->_defaultValue = $defaultValue;
        }
        $this->_passedByReference = (bool) $passedByReference;
        $this->_variadic = (bool) $variadic;
        $this->_optional = (bool) $optional;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->_name;
    }

    /**
     * @return string
     */
    public function dump() {
        $content = '';
        if ($this->_type) {
            $content .= $this->_getType() . ' ';
        }
        if ($this->_variadic) {
            $content .= '...';
        }
        if ($this->_passedByReference) {
            $content .= '&';
        }
        $content .= '$' . $this->_name;
        if ($this->_defaultValueAvailable) {
            $content .= ' = ' . $this->_dumpDefaultValue();
        } elseif ($this->_optional && !$this->_variadic) {
            $content .= ' = null';
        }
        return $content;
    }

    protected function _dumpDefaultValue() {
        if (null === $this->_defaultValue) {
            return 'null';
        }
        $value = new ValueBlock($this->_defaultValue);
        return $value->dump();
    }

    /**
     * @return null|string
     */
    protected function _getType() {
        $type = $this->_type;
        if (!in_array($type, [null, 'array', 'callable'], true)) {
            $type = self::_normalizeClassName($type);
        }
        return $type;
    }

    /**
     * @param \ReflectionParameter $reflection
     * @return ParameterBlock
     */
    public static function buildFromReflection(\ReflectionParameter $reflection) {
        $type = null;
        if ($reflection->isCallable()) {
            $type = 'callable';
        }
        if ($reflection->isArray()) {
            $type = 'array';
        }
        if ($reflection->getClass()) {
            $type = $reflection->getClass()->getName();
        }
        $defaultValue = null;
        if ($reflection->isDefaultValueAvailable()) {
            $defaultValue = $reflection->getDefaultValue();
        }
        return new self($reflection->getName(), $type, $reflection->isDefaultValueAvailable(), $defaultValue, $reflection->isPassedByReference(), $reflection->isVariadic(), $reflection->isOptional());
    }
}
