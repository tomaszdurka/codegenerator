<?php

class CG_Method extends CG_Function {

    /** @var string */
    private $_visibility;

    /** @var boolean */
    private $_static;

    /** @var boolean */
    private $_abstract;

    /**
     * @param string               $name
     * @param callable|string|null $body
     */
    public function __construct($name, $body = null) {
        $this->setName($name);
        $this->setVisibility('public');
        $this->setStatic(false);
        $this->setAbstract(false);
        parent::__construct($body);
    }

    /**
     * @param string $visibility
     */
    public function setVisibility($visibility) {
        $this->_visibility = (string) $visibility;
    }

    /**
     * @param boolean $static
     */
    public function setStatic($static) {
        $this->_static = (bool) $static;
    }

    /**
     * @param boolean $abstract
     */
    public function setAbstract($abstract) {
        $this->_abstract = (bool) $abstract;
    }

    /**
     * @param ReflectionFunctionAbstract $reflection
     */
    public function extractFromReflection(ReflectionFunctionAbstract $reflection) {
        parent::extractFromReflection($reflection);
        if ($reflection instanceof ReflectionMethod) {
            $this->_setVisibilityFromReflection($reflection);
            $this->_setStaticFromReflection($reflection);
            $this->_setAbstractFromReflection($reflection);
        }
    }

    protected function _dumpHeader() {
        $code = '';
        if ($this->_abstract) {
            $code .= 'abstract ';
        }
        $code .= $this->_visibility;
        if ($this->_static) {
            $code .= ' static';
        }
        $code .= ' ' . parent::_dumpHeader();
        return $code;
    }

    protected function _dumpBody() {
        if ($this->_abstract) {
            return ';';
        }
        return parent::_dumpBody();
    }

    /**
     * @param ReflectionMethod $reflection
     */
    private function _setVisibilityFromReflection(ReflectionMethod $reflection) {
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

    public function _setAbstractFromReflection(ReflectionMethod $reflection) {
        $this->setAbstract($reflection->isAbstract());
    }

    /**
     * @param ReflectionMethod $reflection
     */
    private function _setStaticFromReflection(ReflectionMethod $reflection) {
        $this->setStatic($reflection->isStatic());
    }

    /**
     * @param ReflectionMethod $reflection
     * @return self
     */
    public static function buildFromReflection(ReflectionMethod $reflection) {
        $method = new self($reflection->getName());
        $method->extractFromReflection($reflection);
        return $method;
    }
}
