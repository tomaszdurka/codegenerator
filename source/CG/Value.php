<?php

class CG_Value extends CG_Block {

	/** @var mixed */
	private $_value;

	/**
	 * @param mixed $value
	 */
	public function __construct($value) {
		$this->_value = $value;
	}

	/**
	 * @return string
	 */
	public function dump() {
		if (is_array($this->_value)) {
			$array = new CG_Array($this->_value);
			return $array->dump();
		}
		return var_export($this->_value, true);
	}
}
