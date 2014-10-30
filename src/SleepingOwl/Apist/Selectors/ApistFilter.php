<?php namespace SleepingOwl\Apist\Selectors;

use SleepingOwl\Apist\Apist;
use SleepingOwl\Apist\DomCrawler\Crawler;
use SleepingOwl\Apist\Methods\ApistMethod;

/**
 * Class ApistFilter
 *
 * @method ApistFilter else($blueprint)
 */
class ApistFilter
{
	/**
	 * @var Crawler
	 */
	protected $node;
	/**
	 * @var Apist
	 */
	protected $resource;
	/**
	 * @var ApistMethod
	 */
	protected $method;

	/**
	 * @param mixed $node
	 * @param ApistMethod $method
	 */
	function __construct($node, ApistMethod $method)
	{
		$this->node = $node;
		$this->method = $method;
		$this->resource = $method->getResource();
	}

	/**
	 * @return ApistFilter
	 */
	public function text()
	{
		return $this->node->text();
	}

	/**
	 * @return ApistFilter
	 */
	public function html()
	{
		return $this->node->html();
	}

	/**
	 * @param $selector
	 * @return ApistFilter
	 */
	public function filter($selector)
	{
		return $this->node->filter($selector);
	}

	/**
	 * @param $attribute
	 * @return ApistFilter
	 */
	public function attr($attribute)
	{
		return $this->node->attr($attribute);
	}

	/**
	 * @param $position
	 * @return ApistFilter
	 */
	public function eq($position)
	{
		return $this->node->eq($position);
	}

	/**
	 * @return ApistFilter
	 */
	public function first()
	{
		return $this->node->first();
	}

	/**
	 * @return ApistFilter
	 */
	public function last()
	{
		return $this->node->last();
	}

	/**
	 * @return ApistFilter
	 */
	public function element()
	{
		return $this->node;
	}

	/**
	 * @param $callback
	 * @return ApistFilter
	 */
	public function call($callback)
	{
		return $callback($this->node);
	}

	/**
	 * @return ApistFilter
	 */
	public function trim()
	{
		$this->guardText();
		return trim($this->node);
	}

	/**
	 * @return ApistFilter
	 */
	public function intval()
	{
		$this->guardText();
		return intval($this->node);
	}

	/**
	 * @return ApistFilter
	 */
	public function floatval()
	{
		$this->guardText();
		return floatval($this->node);
	}

	/**
	 * @return ApistFilter
	 */
	public function exists()
	{
		return count($this->node) > 0;
	}

	/**
	 * @param $callback
	 * @return ApistFilter
	 */
	public function check($callback)
	{
		return $this->call($callback);
	}

	/**
	 * @param $blueprint
	 * @return ApistFilter
	 */
	public function then($blueprint)
	{
		if ($this->node === true)
		{
			return $this->method->parseBlueprint($blueprint);
		}
		return $this->node;
	}

	/**
	 * @param $blueprint
	 * @return ApistFilter
	 */
	public function each($blueprint = null)
	{
		$callback = $blueprint;
		if (is_null($callback))
		{
			$callback = function ($node)
			{
				return $node;
			};
		}
		if ( ! is_callable($callback))
		{
			$callback = function ($node) use ($blueprint)
			{
				return $this->method->parseBlueprint($blueprint, $node);
			};
		}
		return $this->node->each($callback);
	}

	/**
	 * Guard string method to be called with Crawler object
	 */
	protected function guardText()
	{
		if (is_object($this->node))
		{
			$this->node = $this->node->text();
		}
	}
}