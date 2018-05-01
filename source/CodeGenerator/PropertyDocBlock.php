<?php

namespace CodeGenerator;

class PropertyDocBlock extends DocBlock {

    /**
     * @param string $type
     */
    public function __construct($type) {
        parent::__construct();
        $this->_setOneLiner(true);
        $this->addEntry('@var ' . $type);
    }

}
