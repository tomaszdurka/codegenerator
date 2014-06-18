<?php

namespace CodeGenerator;

class FileBlock extends Block {

    /** @var Block[] */
    private $_blocks = array();

    /**
     * @param Block $block
     */
    public function addBlock(Block $block) {
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
