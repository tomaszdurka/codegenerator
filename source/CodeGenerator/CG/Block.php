<?php

namespace CodeGenerator;

abstract class CG_Block {

    /** @var string */
    protected static $_indentation = '    ';

    /**
     * @return string
     */
    abstract public function dump();

    public function __toString() {
        return $this->dump();
    }

    /**
     * @param string $content
     * @return string
     */
    protected function _indent($content) {
        return preg_replace('/(:?^|[\n])/', '$1' . self::$_indentation, $content);
    }

    /**
     * @param string       $content
     * @param boolean|null $untilUnsafe
     * @return string
     */
    protected function _outdent($content, $untilUnsafe = null) {
        $indentation = self::$_indentation;
        if (!$indentation) {
            return $content;
        }
        $lines = explode(PHP_EOL, $content);
        if ($untilUnsafe) {
            $nonemptyLines = array_filter($lines, function ($line) {
                return (bool) trim($line);
            });
            $unsafeLines = array_filter($nonemptyLines, function ($line) use ($indentation) {
                return strpos($line, $indentation) !== 0;
            });
            if (count($unsafeLines) || !count($nonemptyLines)) {
                return $content;
            }
        }
        foreach ($lines as $key => $line) {
            $lines[$key] = preg_replace('/^' . preg_quote(self::$_indentation) . '/', '$1', $line);
        }
        $content = implode(PHP_EOL, $lines);
        if ($untilUnsafe) {
            $content = $this->_outdent($content, $untilUnsafe);
        }
        return $content;
    }

    /**
     * @param string $line , $line, $line
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
        return implode(PHP_EOL, array_filter($lines, function ($element) {
            return !is_null($element);
        }));
    }

    /**
     * @param string $indentation
     */
    public static function setIndentation($indentation) {
        self::$_indentation = (string) $indentation;
    }

    /**
     * @param string $className
     * @return string
     */
    protected static function _normalizeClassName($className) {
        if (strpos($className, '\\') !== 0) {
            $className = '\\' . $className;
        }
        return $className;
    }
}
