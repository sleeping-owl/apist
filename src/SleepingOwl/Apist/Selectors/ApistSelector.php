<?php namespace SleepingOwl\Apist\Selectors;

use SleepingOwl\Apist\Methods\ApistMethod;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class ApistSelector
 *
 * @method ApistSelector text()
 * @method ApistSelector html()
 * @method ApistSelector attr($attribute)
 * @method ApistSelector each($closure)
 * @method ApistSelector eq($offset)
 * @method ApistSelector first()
 * @method ApistSelector last()
 * @method ApistSelector element()
 * @method ApistSelector trim()
 * @method ApistSelector intval()
 * @method ApistSelector floatval()
 */
class ApistSelector
{
	/**
	 * @var string
	 */
	protected $selector;
	/**
	 * @var ResultCallback[]
	 */
	protected $resultMethodChain = [];

	/**
	 * @param $selector
	 */
	function __construct($selector)
	{
		$this->selector = $selector;
	}

	/**
	 * Get value from content by css selector
	 *
	 * @param ApistMethod $method
	 * @param Crawler $rootNode
	 * @return array|null|string|Crawler
	 */
	public function getValue(ApistMethod $method, Crawler $rootNode = null)
	{
		if (is_null($rootNode))
		{
			$rootNode = $method->getCrawler();
		}
		$result = $rootNode->filter($this->selector);
		try
		{
			return $this->applyResultCallbackChain($result, $method);
		} catch (\InvalidArgumentException $e)
		{
			return null;
		}
	}

	/**
	 * Save callable method as result callback to perform it after getValue method
	 *
	 * @param $name
	 * @param $arguments
	 * @return $this
	 */
	function __call($name, $arguments)
	{
		$resultCallback = new ResultCallback($name, $arguments);
		$this->resultMethodChain[] = $resultCallback;
		return $this;
	}

	/**
	 * Apply all result callbacks
	 *
	 * @param Crawler $node
	 * @param ApistMethod $method
	 * @return array|string|Crawler
	 */
	protected function applyResultCallbackChain(Crawler $node, ApistMethod $method)
	{
		if (empty($this->resultMethodChain))
		{
			return $node->text();
		}
		foreach ($this->resultMethodChain as $resultCallback)
		{
			$node = $resultCallback->apply($node, $method);
		}
		return $node;
	}

} 