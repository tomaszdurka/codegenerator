<?php

abstract class CG_Block {

	/** @var string  */
	protected $_indentation = '	';

	/**
	 * @return string
	 */
	abstract public function dump();

	/**
	 * @param string $content
	 * @return string
	 */
	protected function _indent($content) {
		return preg_replace('/(:?^|[\n])/', '$1' . $this->_indentation, $content);
	}

	/**
	 * @param string $line, $line, $line
	 * @return string
	 */
	protected function _dumpLine($line) {
		$lines = func_get_args();
		return $this->_dumpLines($lines);
	}

	/**
	 * @param string[] $lines
	 * @return string
	 */
	protected function _dumpLines(array $lines) {
		return implode(PHP_EOL, $lines);
	}
}
