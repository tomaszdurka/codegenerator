<?php

class CG_Method extends CG_Block {

	/** @var string */
	private $_name;

	/** @var string */
	private $_visibility;

	/** @var CG_Parameter[] */
	private $_parameters = array();

	/**
	 * @param $name
	 */
	public function __construct($name) {
		$this->_name = (string) $name;
		$this->setVisibility('public');
	}

	/**
	 * @param string $visibility
	 */
	public function setVisibility($visibility) {
		$this->_visibility = (string) $visibility;
	}

	public function dump() {
		return $this->_dumpLine(
			$this->_dumpHeader(),
			$this->_indent($this->_dumpBody()),
			$this->_dumpFooter()
		);
	}

	/**
	 * @param CG_Parameter $parameter
	 * @throws Exception
	 */
	public function addParameter(CG_Parameter $parameter) {
		if (array_key_exists($parameter->getName(), $this->_parameters)) {
			throw new Exception('Paremter `' . $parameter->getName() . '` is already set.');
		}
		$this->_parameters[$parameter->getName()] = $parameter;
	}

	private function _dumpHeader() {
		$content = $this->_visibility . ' function ' . $this->_name . '(';
		$content .= implode(', ', $this->_parameters);
		$content .= ') {';
		return $content;
	}

	private function _dumpFooter() {
		return '}';
	}

	private function _dumpBody() {
		return '// To be implemented';
	}
}
