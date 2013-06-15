<?php

class CG_Parameter extends CG_Block {

	/** @var string */
	private $_name;

	/** @var string|null */
	private $_type;

	/** @var mixed */
	private $_defaultValue;

	/** @var boolean */
	private $_optional;

	/**
	 * @param string       $name
	 * @param string|null  $type
	 * @param null         $optional
	 * @param mixed|null   $defaultValue
	 * @throws Exception
	 * @internal param bool|null $isOptional
	 */
	public function __construct($name, $type = null, $optional = null, $defaultValue = null) {
		$this->_name = (string) $name;
		if (null !== $type) {
			$this->_type = (string) $type;
		}
		$this->_optional = (bool) $optional;
		if (null !== $defaultValue) {
			if (!$this->_optional) {
				throw new Exception('Cannot set default value for non-optional parameter');
			}
			$this->_defaultValue = $defaultValue;
		}
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
			$content .= $this->_type . ' ';
		}
		$content .= '$' . $this->_name;
		if ($this->_optional) {
			$content .= ' = ' . $this->_dumpDefaultValue();
		}
		return $content;
	}

	protected function _dumpDefaultValue() {
		if (null === $this->_defaultValue) {
			return 'null';
		}
		$value = new CG_Value($this->_defaultValue);
		return $value->dump();
	}

	/**
	 * @param ReflectionParameter $reflection
	 * @return self
	 */
	public static function buildFromReflection(ReflectionParameter $reflection) {
		$type = null;
		if ($reflection->isArray()) {
			$type = 'array';
		}
		if ($reflection->getClass()) {
			$type = $reflection->getClass()->getName();
		}
		$defaultValue = null;
		if ($reflection->isOptional()) {
			$defaultValue = $reflection->getDefaultValue();
		}

		return new self($reflection->getName(), $type, $reflection->isOptional(), $defaultValue);
	}
}
