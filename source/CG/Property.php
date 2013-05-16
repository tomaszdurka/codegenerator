<?php

class CG_Property extends CG_Block {

	/** @var string */
	private $_name;

	/** @var string */
	private $_visibility;

	/** @var mixed */
	private $_defaultValue;

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

	public function dump() {
		$content = $this->_visibility . ' $' . $this->_name;
		if (null !== $this->_defaultValue) {
			$content .= ' = ' . var_export($this->_defaultValue, true);
		}
		$content .= ';';
		return $content;
	}

	/**
	 * @param ReflectionProperty $reflection
	 * @return CG_Property
	 */
	public static function buildFromReflection(ReflectionProperty $reflection) {
		$property = new self($reflection->getName());
		if ($reflection->isProtected()) {
			$property->setVisibility('protected');
		}
		if ($reflection->isPrivate()) {
			$property->setVisibility('private');
		}
		//$property->setDefaultValue($reflection->getValue());
		return $property;
	}
}
