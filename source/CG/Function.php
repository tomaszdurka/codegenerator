<?php

class CG_Function extends CG_Block {

	/** @var string|null */
	protected $_name;

	/** @var CG_Parameter[] */
	private $_parameters = array();

	/** @var string */
	protected $_code;

	/** @var string */
	protected $_docBlock;

	/**
	 * @param callable|string|null $body
	 */
	public function __construct($body = null) {
		if (null !== $body) {
			if ($body instanceof Closure) {
				$this->extractFromClosure($body);
			} else {
				$this->setCode($body);
			}
		}
	}

	/**
	 * @param string $name
	 */
	public function setName($name) {
		$this->_name = (string) $name;
	}

	/**
	 * @return string|null
	 */
	public function getName() {
		return $this->_name;
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
		$this->_code = $this->_outdent((string) $code, true);
	}

	/**
	 * @param string $docBlock
	 */
	public function setDocBlock($docBlock) {
		$this->_docBlock = (string) $docBlock;
	}

	public function dump() {
		return $this->_dumpLine(
			$this->_dumpDocBlock(),
			$this->_dumpHeader(),
			$this->_indent($this->_dumpBody()),
			$this->_dumpFooter()
		);
	}

	/**
	 * @param ReflectionFunctionAbstract $reflection
	 */
	protected function _setBodyFromReflection(ReflectionFunctionAbstract $reflection) {
		/** @var $reflection ReflectionMethod */
		if (is_a($reflection, 'ReflectionMethod') && $reflection->isAbstract()) {
			$this->_code = null;
			return;
		}
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
		$code = preg_replace('/^\s*[\r\n]+/', '', $code);
		$code = preg_replace('/[\r\n]+\s*$/', '', $code);

		$this->setCode($code);
	}

	/**
	 * @param ReflectionFunctionAbstract $reflection
	 */
	protected function _setParametersFromReflection(ReflectionFunctionAbstract $reflection) {
		foreach ($reflection->getParameters() as $reflectionParameter) {
			$parameter = CG_Parameter::buildFromReflection($reflectionParameter);
			$this->addParameter($parameter);
		}
	}

	protected function _setDocBlockFromReflection(ReflectionFunctionAbstract $reflection) {
		$docBlock = $reflection->getDocComment();
		if ($docBlock) {
			$docBlock = preg_replace('/([\n\r])\t+/', '$1', $docBlock);
			$this->setDocBlock($docBlock);
		}
	}

	/**
	 * @return string
	 */
	protected function _dumpDocBlock() {
		return $this->_docBlock;
	}

	/**
	 * @return string
	 */
	protected function _dumpHeader() {
		$content = 'function';
		if ($this->_name) {
			$content .= ' ' . $this->_name;
		}
		$content .= '(';
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
	 * @param ReflectionFunctionAbstract $reflection
	 */
	public function extractFromReflection(ReflectionFunctionAbstract $reflection) {
		$this->_setBodyFromReflection($reflection);
		$this->_setParametersFromReflection($reflection);
		$this->_setDocBlockFromReflection($reflection);
	}

	/**
	 * @param callable $closure
	 */
	public function extractFromClosure(Closure $closure) {
		$this->extractFromReflection(new ReflectionFunction($closure));
	}
}
