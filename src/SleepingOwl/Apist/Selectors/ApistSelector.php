<?php namespace SleepingOwl\Apist\Selectors;

use InvalidArgumentException;
use SleepingOwl\Apist\Methods\ApistMethod;
use Symfony\Component\DomCrawler\Crawler;

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
		return $this->applyResultCallbackChain($result, $method);
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
		return $this->addCallback($name, $arguments);
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
			$this->addCallback('text');
		}
		/** @var ResultCallback[] $traceStack */
		$traceStack = [];
		foreach ($this->resultMethodChain as $resultCallback)
		{
			try
			{
				$traceStack[] = $resultCallback;
				$node = $resultCallback->apply($node, $method);
			} catch (InvalidArgumentException $e)
			{
				if ($method->getResource()->isSuppressExceptions())
				{
					return null;
				}
				$message = $this->createExceptionMessage($e, $traceStack);
				throw new InvalidArgumentException($message, 0, $e);
			}
		}
		return $node;
	}

	/**
	 * @param $name
	 * @param $arguments
	 * @return $this
	 */
	public function addCallback($name, $arguments = [])
	{
		$resultCallback = new ResultCallback($name, $arguments);
		$this->resultMethodChain[] = $resultCallback;
		return $this;
	}

	/**
	 * @param $e
	 * @param ResultCallback[] $traceStack
	 * @return string
	 */
	protected function createExceptionMessage(\Exception $e, $traceStack)
	{
		$message = "[ filter({$this->selector})";
		foreach ($traceStack as $callback)
		{
			$message .= '->' . $callback->getMethodName() . '(';
			try
			{
				$message .= implode(', ', $callback->getArguments());
			} catch (\Exception $_e)
			{
			}
			$message .= ')';
		}
		$message .= ' ] ' . $e->getMessage();
		return $message;
	}

}