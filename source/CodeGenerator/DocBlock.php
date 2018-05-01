<?php

namespace CodeGenerator;

class DocBlock extends Block {

    /** @var bool */
    private $_oneLiner;

    /** @var string[] */
    private $_entries;

    public function __construct() {
        $this->_entries = [];
    }

    public function dump() {
        if ($this->_oneLiner) {
            return '/** ' . join(' ', $this->_getEntries()) . ' */';
        }

        $dump = '/**' . PHP_EOL;
        foreach ($this->_getEntries() as $entry) {
            $dump .= ' * ' . $entry . PHP_EOL;
        }
        $dump .= ' */';
        return $dump;
    }

    /**
     * @param bool $oneLiner
     */
    protected function _setOneLiner($oneLiner) {
        $this->_oneLiner = $oneLiner;
    }

    /**
     * @param string $entry
     */
    public function addEntry($entry) {
        $this->_entries[] = $entry;
    }

    /**
     * @return string[]
     */
    protected function _getEntries() {
        return $this->_entries;
    }
}
