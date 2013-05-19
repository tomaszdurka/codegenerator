<?php

class CG_File extends CG_Block {

	/** @var CG_Block[] */
	private $_blocks = array();

	/**
	 * @param CG_Block $block
	 */
	public function addBlock(CG_Block $block) {
		$this->_blocks[] = $block;
	}

	public function dump() {
		$lines = array();
		$lines[] = '<?php';
		foreach ($this->_blocks as $block) {
			$lines[] = '';
			$lines[] = $block->dump();
		}
		$lines[] = '';
		return $this->_dumpLines($lines);
	}
}
