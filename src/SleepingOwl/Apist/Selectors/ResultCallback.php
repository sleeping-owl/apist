<?php namespace SleepingOwl\Apist\Selectors;

use SleepingOwl\Apist\Methods\ApistMethod;
use Symfony\Component\DomCrawler\Crawler;

class ResultCallback
{
	/**
	 * @var string
	 */
	protected $methodName;
	/**
	 * @var array
	 */
	protected $arguments;

	/**
	 * @param $methodName
	 * @param $arguments
	 */
	function __construct($methodName, $arguments)
	{
		$this->methodName = $methodName;
		$this->arguments = $arguments;
	}

	/**
	 * Apply result callback to the $node, provided by $method
	 *
	 * @param Crawler $node
	 * @param ApistMethod $method
	 * @return array|string
	 */
	public function apply($node, ApistMethod $method)
	{
		if ($this->methodName === 'else')
		{
			if (is_bool($node)) $node = ! $node;
			$this->methodName = 'then';
		}
		if ($this->isResourceMethod($method))
		{
			return $this->callResourceMethod($method, $node);
		}
		if ($this->isNodeMethod($node))
		{
			return $this->callNodeMethod($node);
		}
		if ($this->isGlobalFunction())
		{
			return $this->callGlobalFunction($node);
		}
		throw new \InvalidArgumentException("Method '{$this->methodName}' was not found");
	}

	/**
	 * @param ApistMethod $method
	 * @return bool
	 */
	protected function isResourceMethod(ApistMethod $method)
	{
		return method_exists($method->getResource(), $this->methodName);
	}

	/**
	 * @param ApistMethod $method
	 * @param $node
	 * @return mixed
	 */
	protected function callResourceMethod(ApistMethod $method, $node)
	{
		$arguments = $this->arguments;
		array_unshift($arguments, $node);
		return call_user_func_array([
			$method->getResource(),
			$this->methodName
		], $arguments);
	}

	/**
	 * @param $node
	 * @return bool
	 */
	protected function isNodeMethod($node)
	{
		return method_exists($node, $this->methodName);
	}

	/**
	 * @param $node
	 * @return mixed
	 */
	protected function callNodeMethod($node)
	{
		return call_user_func_array([
			$node,
			$this->methodName
		], $this->arguments);
	}

	/**
	 * @return bool
	 */
	protected function isGlobalFunction()
	{
		return function_exists($this->methodName);
	}

	/**
	 * @param $node
	 * @return mixed
	 */
	protected function callGlobalFunction($node)
	{
		if (is_object($node))
		{
			$node = $node->text();
		}
		$arguments = $this->arguments;
		array_unshift($arguments, $node);
		return call_user_func_array($this->methodName, $arguments);
	}

} 