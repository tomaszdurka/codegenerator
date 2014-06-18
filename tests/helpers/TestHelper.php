<?php

namespace CodeGeneratorHelpers;

use CodeGenerator\Block;

class TestHelper {

	/**
	 * @param Block   $object
	 * @param string     $methodName
	 * @param array|null $arguments
	 * @return mixed
	 */
	public static function invokeMethod(Block $object, $methodName, array $arguments = null) {
		$arguments = (array) $arguments;
		$reflection = new \ReflectionMethod(get_class($object), $methodName);
		$reflection->setAccessible(true);
		return $reflection->invokeArgs($object, $arguments);
	}
}
