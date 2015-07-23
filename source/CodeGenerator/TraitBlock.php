<?php

namespace CodeGenerator;

class TraitBlock extends Block
{
    /** @var string */
    private $_name;

    /** @var string */
    private $_namespace;

    /** @var string[] */
    private $_uses = [];

    /** @var PropertyBlock[] */
    private $_properties = [];

    /** @var MethodBlock[] */
    private $_methods = [];

    /**
     * @param $name
     */
    public function __construct($name)
    {
        $this->_name = (string)$name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @param string $namespace
     */
    public function setNamespace($namespace)
    {
        $this->_namespace = (string)$namespace;
    }

    /**
     * @param string $name
     */
    public function addUse($name)
    {
        $this->_uses[] = $name;
    }

    /**
     * @param PropertyBlock $property
     */
    public function addProperty(PropertyBlock $property)
    {
        $this->_properties[$property->getName()] = $property;
    }

    /**
     * @param MethodBlock $method
     */
    public function addMethod(MethodBlock $method)
    {
        $this->_methods[$method->getName()] = $method;
    }

    /**
     * @return string
     */
    public function dump()
    {
        $lines = [];
        $lines[] = $this->_dumpHeader();
        foreach ($this->_uses as $use) {
            $lines[] = '';
            $lines[] = $this->_indent("use ${use};");
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
    private function _dumpHeader()
    {
        $lines = [];
        if ($this->_namespace) {
            $lines[] = 'namespace ' . $this->_namespace . ';';
            $lines[] = '';
        }
        $classDeclaration = 'trait ' . $this->_name;
        $classDeclaration .= ' {';
        $lines[] = $classDeclaration;

        return $this->_dumpLines($lines);
    }

    /**
     * @return string
     */
    private function _dumpFooter()
    {
        return '}';
    }

    /**
     * @param \ReflectionClass $reflection
     * @return TraitBlock
     */
    public static function buildFromReflection(\ReflectionClass $reflection)
    {
        $class = new self($reflection->getShortName());
        $class->setNamespace($reflection->getNamespaceName());

        foreach ($reflection->getMethods() as $reflectionMethod) {
            if ($reflectionMethod->getDeclaringClass() == $reflection) {
                $method = MethodBlock::buildFromReflection($reflectionMethod);
                $class->addMethod($method);
            }
        }

        foreach ($reflection->getProperties() as $reflectionProperty) {
            if ($reflectionProperty->getDeclaringClass() == $reflection) {
                $property = PropertyBlock::buildFromReflection($reflectionProperty);
                $class->addProperty($property);
            }
        }

        return $class;
    }
}
