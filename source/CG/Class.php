<?php

class CG_Class extends CG_Block {

	/** @var string */
	private $_name;

	/** @var string */
	private $_parentClassName;

	/** @var string[] */
	private $_interfaces;

	/** @var CG_Method[] */
	private $_methods = array();

	/** @var CG_Property[] */
	private $_properties = array();

	/**
	 * @param string        $name
	 * @param string|null   $parentClassName
	 * @param array|null    $interfaces
	 */
	public function __construct($name, $parentClassName = null, array $interfaces = null) {
		$this->_name = (string) $name;
		if ($parentClassName) {
			$this->_parentClassName = (string) $parentClassName;
		}
		if ($interfaces) {
			$this->_interfaces = $interfaces;
		}
	}

	/**
	 * @param CG_Method $method
	 */
	public function addMethod(CG_Method $method) {
		$this->_methods[] = $method;
	}

	/**
	 * @param CG_Property $property
	 */
	public function addProperty(CG_Property $property) {
		$this->_properties[] = $property;
	}

	/**
	 * @return string
	 */
	public function dump() {
		$lines = array();
		$lines[] = $this->_dumpHeader();
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
}
