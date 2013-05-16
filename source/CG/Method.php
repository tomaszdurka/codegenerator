<?php

class CG_Method extends CG_Function {

	/** @var string */
	private $_visibility;

	/** @var boolean */
	private $_static;

	/**
	 * @param string $name
	 * @param callable $closure
	 */
	public function __construct($name, Closure $closure = null) {
		$this->setName($name);
		$this->setVisibility('public');
		parent::__construct($closure);
	}

	/**
	 * @param string $visibility
	 */
	public function setVisibility($visibility) {
		$this->_visibility = (string) $visibility;
	}

	public function setStatic($static) {
		$this->_static = (bool) $static;
	}

	protected function _dumpHeader() {
		$code = $this->_visibility;
		if ($this->_static) {
			$code .= ' static';
		}
		$code .= ' ' . parent::_dumpHeader();
		return $code;
	}

	/**
	 * @param ReflectionMethod $reflection
	 * @return self
	 */
	public static function buildFromReflection(ReflectionMethod	$reflection) {
		$method = new self($reflection->getName());
		$method->extractFromReflection($reflection);
		return $method;
	}
}
