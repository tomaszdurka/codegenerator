<?php

class CG_Class extends CG_Block {

    /** @var string */
    private $_name;

    /** @var string */
    private $_parentClassName;

    /** @var string[] */
    private $_interfaces;

    /** @var string[] */
    private $_uses = array();

    /** @var CG_Constant[] */
    private $_constants = array();

    /** @var CG_Property[] */
    private $_properties = array();

    /** @var CG_Method[] */
    private $_methods = array();

    /** @var boolean */
    private $_abstract;

    /**
     * @param string        $name
     * @param string|null   $parentClassName
     * @param array|null    $interfaces
     */
    public function __construct($name, $parentClassName = null, array $interfaces = null) {
        $this->_name = (string) $name;
        if (null !== $parentClassName) {
            $this->setParentClassName($parentClassName);
        }
        if (null !== $interfaces) {
            $this->setInterfaces($interfaces);
        }
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->_name;
    }

    /**
     * @param string $parentClassName
     */
    public function setParentClassName($parentClassName) {
        $this->_parentClassName = (string) $parentClassName;
    }

    /**
     * @param string[] $interfaces
     */
    public function setInterfaces(array $interfaces) {
        foreach ($interfaces as $interface) {
            $this->addInterface($interface);
        }
    }

    /**
     * @param boolean $abstract
     */
    public function setAbstract($abstract) {
        $this->_abstract = (bool) $abstract;
    }

    /**
     * @param string $name
     */
    public function addUse($name) {
        $this->_uses[] = $name;
    }

    /**
     * @param CG_Constant $constant
     */
    public function addConstant(CG_Constant $constant) {
        $this->_constants[$constant->getName()] = $constant;
    }

    /**
     * @param CG_Property $property
     */
    public function addProperty(CG_Property $property) {
        $this->_properties[$property->getName()] = $property;
    }

    /**
     * @param CG_Method $method
     */
    public function addMethod(CG_Method $method) {
        $this->_methods[$method->getName()] = $method;
    }

    /**
     * @param string $interface
     */
    public function addInterface($interface) {
        $this->_interfaces[] = $interface;
    }

    /**
     * @return string
     */
    public function dump() {
        $lines = array();
        $lines[] = $this->_dumpHeader();
        foreach ($this->_uses as $use) {
            $lines[] = '';
            $lines[] = $this->_indent("use ${use};");
        }
        foreach ($this->_constants as $constant) {
            $lines[] = '';
            $lines[] = $this->_indent($constant->dump());
        }
        foreach ($this->_properties as $property) {
            $lines[] = '';
            $lines[] = $this->_indent($property->dump());
        }
        foreach ($this->_methods as $method) {
            $lines[] = '';
            $lines[] = $this->_indent($method->dump());
        }
        $lines[] = $this->_dumpFooter();
        return $this->_dumpLines($lines);
    }

    /**
     * @return string
     */
    private function _dumpHeader() {
        $content = '';
        if ($this->_abstract) {
            $content .= 'abstract ';
        }
        $content .= 'class ' . $this->_name;
        if ($this->_parentClassName) {
            $content .= ' extends ' . $this->_parentClassName;
        }
        if ($this->_interfaces) {
            $content .= ' implements ' . implode(', ', $this->_interfaces);
        }
        $content .= ' {';
        return $content;
    }

    /**
     * @return string
     */
    private function _dumpFooter() {
        return '}';
    }

    public static function buildFromReflection(ReflectionClass $reflection) {
        $class = new self($reflection->getName());
        $reflectionParentClass = $reflection->getParentClass();
        if ($reflectionParentClass) {
            $class->setParentClassName($reflectionParentClass->getName());
        }
        $class->setAbstract($reflection->isAbstract());
        if ($interfaces = $reflection->getInterfaceNames()) {
            if ($reflectionParentClass) {
                $parentInterfaces = $reflection->getParentClass()->getInterfaceNames();
                $interfaces = array_diff($interfaces, $parentInterfaces);
            }
            $class->setInterfaces($interfaces);
        }
        foreach ($reflection->getMethods() as $reflectionMethod) {
            if ($reflectionMethod->getDeclaringClass() == $reflection) {
                $method = CG_Method::buildFromReflection($reflectionMethod);
                $class->addMethod($method);
            }
        }
        foreach ($reflection->getProperties() as $reflectionProperty) {
            if ($reflectionProperty->getDeclaringClass() == $reflection) {
                $property = CG_Property::buildFromReflection($reflectionProperty);
                $class->addProperty($property);
            }
        }
        foreach ($reflection->getConstants() as $name => $value) {
            if (!$reflection->getParentClass()->hasConstant($name)) {
                $class->addConstant(new CG_Constant($name, $value));
            }
        }
        return $class;
    }
}
