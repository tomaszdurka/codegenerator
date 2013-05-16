<?php

class CG_Method extends CG_Block {

	/** @var string */
	private $_name;

	/**
	 * @param $name
	 */
	public function __construct($name) {
		$this->_name = (string) $name;
	}

	public function dump() {
		return $this->_dumpLine(
			$this->_dumpHeader(),
			$this->_indent($this->_dumpBody()),
			$this->_dumpFooter()
		);
	}

	private function _dumpHeader() {
		return 'function ' . $this->_name . ' {';
	}

	private function _dumpFooter() {
		return '}';
	}

	private function _dumpBody() {
		return '// To be implemented';
	}
}
