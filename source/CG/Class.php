<?php

class CG_Class extends CG_Block {

	/** @var string */
	private $_name;

	/** @var string */
	private $_parentClassName;

	/** @var string[] */
	private $_interfaces;

	/** @var CG_Constant[] */
	private $_constants = array();

	/** @var CG_Property[] */
	private $_properties = array();

	/** @var CG_Method[] */
	private $_methods = array();

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
	 * @param string $parentClassName
	 */
	public function setParentClassName($parentClassName) {
		$this->_parentClassName = (string) $parentClassName;
	}

	/**
	 * @param string[] $interfaces
	 */
	public function setInterfaces(array $interfaces) {
		$this->_interfaces = $interfaces;
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
	 * @return string
	 */
	public function dump() {
		$lines = array();
		$lines[] = $this->_dumpHeader();
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
		$content = 'class ' . $this->_name;
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
		if ($reflection->getParentClass()) {
			$class->setParentClassName($reflection->getParentClass()->getName());
		}
		$class->setInterfaces($reflection->getInterfaceNames());
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
