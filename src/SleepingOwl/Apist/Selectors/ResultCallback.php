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
		if ($this->methodName === 'element')
		{
			return $node;
		}
		if ($this->methodName === 'exists')
		{
			return (bool) count($node);
		}
		if (in_array($this->methodName, ['then', 'else']))
		{
			if ($node && $this->methodName === 'then' || ! $node && $this->methodName === 'else')
			{
				return $method->parseBlueprint($this->arguments[0]);
			}
		}
		if ($this->methodName === 'each')
		{
			return $node->each(function ($node) use ($method)
			{
				return $method->parseBlueprint($this->arguments[0], $node);
			});
		}
		if (method_exists($node, $this->methodName))
		{
			return call_user_func_array([
				$node,
				$this->methodName
			], $this->arguments);
		}
		if (function_exists($this->methodName))
		{
			if ( ! is_string($node))
			{
				$node = $node->text();
			}
			$arguments = $this->arguments;
			array_unshift($arguments, $node);
			return call_user_func_array($this->methodName, $arguments);
		}
		return $node;
	}

} 