<?php

class CG_Property extends CG_Block {

    /** @var string */
    private $_name;

    /** @var string */
    private $_visibility;

    /** @var mixed */
    private $_defaultValue;

    /** @var string|null */
    protected $_docBlock;

    /**
     * @param string $name
     */
    public function __construct($name) {
        $this->_name = (string) $name;
        $this->setVisibility('public');
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->_name;
    }

    /**
     * @param string $visibility
     */
    public function setVisibility($visibility) {
        $this->_visibility = (string) $visibility;
    }

    /**
     * @param mixed $value
     */
    public function setDefaultValue($value) {
        $this->_defaultValue = $value;
    }

    /**
     * @param string|null $docBlock
     */
    public function setDocBlock($docBlock) {
        if (null !== $docBlock) {
            $docBlock = (string) $docBlock;
        }
        $this->_docBlock = $docBlock;
    }

    public function dump() {
        return $this->_dumpLine(
            $this->_dumpDocBlock(),
            $this->_dumpValue()
        );
    }

    /**
     * @param ReflectionProperty $reflection
     */
    public function extractFromReflection(ReflectionProperty $reflection) {
        $this->_setVisibilityFromReflection($reflection);
        $this->_setDefaultValueFromReflection($reflection);
        $this->_setDocBlockFromReflection($reflection);
    }

    /**
     * @return string
     */
    protected function _dumpDocBlock() {
        return $this->_docBlock;
    }

    /**
     * @return string
     */
    protected function _dumpValue() {
        $content = $this->_visibility . ' $' . $this->_name;
        if (null !== $this->_defaultValue) {
            $value = new CG_Value($this->_defaultValue);
            $content .= ' = ' . $value->dump();
        }
        $content .= ';';
        return $content;
    }

    /**
     * @param ReflectionProperty $reflection
     */
    protected function _setVisibilityFromReflection(ReflectionProperty $reflection) {
        if ($reflection->isPublic()) {
            $this->setVisibility('public');
        }
        if ($reflection->isProtected()) {
            $this->setVisibility('protected');
        }
        if ($reflection->isPrivate()) {
            $this->setVisibility('private');
        }
    }

    protected function _setDocBlockFromReflection(ReflectionProperty $reflection) {
        $docBlock = $reflection->getDocComment();
        if ($docBlock) {
            $docBlock = preg_replace('/([\n\r])\t+/', '$1', $docBlock);
            $this->setDocBlock($docBlock);
        }
    }

    /**
     * @param ReflectionProperty $reflection
     */
    protected function _setDefaultValueFromReflection(ReflectionProperty $reflection) {
        $defaultProperties = $reflection->getDeclaringClass()->getDefaultProperties();
        $value = $defaultProperties[$this->getName()];
        if (null !== $value) {
            $this->setDefaultValue($value);
        }
    }

    /**
     * @param ReflectionProperty $reflection
     * @return CG_Property
     */
    public static function buildFromReflection(ReflectionProperty $reflection) {
        $property = new self($reflection->getName());
        $property->extractFromReflection($reflection);
        // $property->setDefaultValue($reflection->getValue());
        return $property;
    }
}
