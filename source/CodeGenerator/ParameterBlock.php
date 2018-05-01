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
    private $_optional;

    /** @var boolean */
    private $_passedByReference;

    /** @var boolean */
    private $_variadic;

    /**
     * @param string       $name
     * @param string|null  $type
     * @param bool|null    $optional
     * @param mixed|null   $defaultValue
     * @param boolean|null $passedByReference
     * @param boolean|null $variadic
     * @throws Exception
     */
    public function __construct($name, $type = null, $optional = null, $defaultValue = null, $passedByReference = null, $variadic = null) {
        if (!$optional && null !== $defaultValue) {
            throw new Exception('Cannot set default value for non-optional parameter');
        }
        if ($variadic && null !== $this->_defaultValue) {
            throw new Exception('Cannot set default value for variadic parameters');
        }

        $this->_name = (string) $name;
        if (null !== $type) {
            $this->_type = (string) $type;
        }
        $this->_optional = (bool) $optional;
        if ($this->_optional) {
            $this->_defaultValue = $defaultValue;
        }
        $this->_passedByReference = (bool) $passedByReference;
        $this->_variadic = (bool) $variadic;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->_name;
    }

    /**
     * @return bool
     */
    public function isOptional() {
        return $this->_optional;
    }

    /**
     * @return string
     */
    public function dump() {
        $content = '';
        if ($this->_type) {
            $content .= $this->getType() . ' ';
        }
        if ($this->_variadic) {
            $content .= '...';
        }
        if ($this->_passedByReference) {
            $content .= '&';
        }
        $content .= '$' . $this->_name;
        if ($this->_optional && !$this->_variadic) {
            $content .= ' = ' . $this->_dumpDefaultValue();
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
    public function getType() {
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
        $optional = $reflection->isOptional() || $reflection->isDefaultValueAvailable();
        $isVariadic = method_exists($reflection, 'isVariadic') && $reflection->isVariadic();
        return new self($reflection->getName(), $type, $optional, $defaultValue, $reflection->isPassedByReference(), $isVariadic);
    }
}
