<?php

namespace CodeGenerator;

class TraitBlock extends Block {

    /** @var string */
    private $_name;

    /** @var array */
    private $_aliases = [];

    /**
     * @param string $name
     */
    public function __construct($name) {
        $this->_name = (string) $name;
    }

    /**
     * @param string $name
     * @param string $alias
     */
    public function addAlias($name, $alias) {
        $this->_aliases[(string) $name] = (string) $alias;
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
        $lines = "use {$this->_name}";
        if ($this->_aliases) {
            $lines .= $this->_dumpLine(" {", $this->_indent($this->_dumpAliases()), "}");
        } else {
            $lines .= ";";
        }
        return $lines;
    }

    /**
     * @return string
     */
    private function _dumpAliases() {
        return $this->_dumpLines(array_map(function ($name, $alias) {
            return "{$name} as {$alias};";
        }, array_keys($this->_aliases), $this->_aliases));
    }

}
