<?php

namespace CodeGenerator;

class InterfaceMethodBlock extends FunctionBlock
{
    /**
     * @param callable|null|string $name
     */
    public function __construct($name)
    {
        $this->setName($name);
        parent::__construct();
    }

    /**
     * @return string
     */
    protected function _dumpHeader()
    {
        return 'public ' . parent::_dumpHeader();
    }

    /**
     * @return string
     */
    protected function _dumpBody()
    {
        return ';';
    }

    /**
     * @param \ReflectionMethod $reflection
     * @return InterfaceMethodBlock
     */
    public static function buildFromReflection(\ReflectionMethod $reflection)
    {
        $method = new self($reflection->getName());
        $method->extractFromReflection($reflection);

        return $method;
    }
}