<?php

class CG_Function extends CG_Block {

	/** @var string|null */
	protected $_name;

	/** @var CG_Parameter[] */
	private $_parameters = array();

	/** @var string */
	protected $_code;

	/**
	 * @param callable $closure
	 */
	public function __construct(Closure $closure = null) {
		if (null !== $closure) {
			$this->extractFromClosure($closure);
		}
	}

	/**
	 * @param string $name
	 */
	public function setName($name) {
		$this->_name = (string) $name;
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

	/**
	 * @param string $code
	 */
	public function setCode($code) {
		$this->_code = (string) $code;
	}

	public function dump() {
		return $this->_dumpLine(
			$this->_dumpHeader(),
			$this->_dumpBody(),
			$this->_dumpFooter()
		);
	}

	/**
	 * @param ReflectionFunction $reflection
	 */
	protected function _setBodyFromReflection(ReflectionFunction $reflection) {
		$file = new SplFileObject($reflection->getFileName());
		$file->seek($reflection->getStartLine() - 1);

		$code = '';
		while ($file->key() < $reflection->getEndLine()) {
			$code .= $file->current();
			$file->next();
		}

		$begin = strpos($code, 'function');
		$code = substr($code, $begin);

		$begin = strpos($code, '{');
		$end = strrpos($code, '}');
		$code = substr($code, $begin + 1, $end - $begin - 1);
		$code = trim($code, PHP_EOL);

		$this->setCode($code);
	}

	/**
	 * @param ReflectionFunction $reflection
	 */
	protected function _setParametersFromReflection(ReflectionFunction $reflection) {
		foreach ($reflection->getParameters() as $reflectionParameter) {
			$parameter = CG_Parameter::buildFromReflection($reflectionParameter);
			$this->addParameter($parameter);
		}
	}

	/**
	 * @return string
	 */
	protected function _dumpHeader() {
		$content = 'function';
		if ($this->_name) {
			$content .= ' ' . $this->_name;
		}
		$content .= ' (';
		$content .= implode(', ', $this->_parameters);
		$content .= ') {';
		return $content;
	}

	/**
	 * @return string
	 */
	private function _dumpFooter() {
		return '}';
	}

	/**
	 * @return string
	 */
	private function _dumpBody() {
		return $this->_code;
	}

	/**
	 * @param ReflectionFunction $reflection
	 * @return CG_Function
	 */
	public function extractFromReflection(ReflectionFunction $reflection) {
		$this->_setBodyFromReflection($reflection);
		$this->_setParametersFromReflection($reflection);
	}

	/**
	 * @param callable $closure
	 * @return CG_Function
	 */
	public function extractFromClosure(Closure $closure) {
		return $this->extractFromReflection(new ReflectionFunction($closure));
	}
}
