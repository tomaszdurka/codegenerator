<?php

class CG_Parameter extends CG_Block {

	/** @var string */
	private $_name;

	/** @var string|null */
	private $_type;

	/** @var boolean */
	private $_optional;

	/**
	 * @param string       $name
	 * @param string|null  $type
	 * @param boolean|null $optional
	 */
	public function __construct($name, $type = null, $optional = null) {
		$this->_name = (string) $name;
		if (null !== $type) {
			$this->_type = (string) $type;
		}
		if (null !== $optional) {
			$this->_optional = (bool) $optional;
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
			$content .= ' = null';
		}
		return $content;
	}
}
