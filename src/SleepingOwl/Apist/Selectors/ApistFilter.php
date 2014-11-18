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
		$this->guardCrawler();
		return $this->node->text();
	}

	/**
	 * @return ApistFilter
	 */
	public function html()
	{
		$this->guardCrawler();
		return $this->node->html();
	}

	/**
	 * @param $selector
	 * @return ApistFilter
	 */
	public function filter($selector)
	{
		$this->guardCrawler();
		return $this->node->filter($selector);
	}

	/**
	 * @param $selector
	 * @return ApistFilter
	 */
	public function filterNodes($selector)
	{
		$this->guardCrawler();
		$rootNode = $this->method->getCrawler();
		$crawler = new Crawler;
		$rootNode->filter($selector)->each(function (Crawler $filteredNode) use ($crawler)
		{
			$filteredNode = $filteredNode->getNode(0);
			foreach ($this->node as $node)
			{
				if ($filteredNode === $node)
				{
					$crawler->add($node);
					break;
				}
			}
		});
		return $crawler;
	}

	/**
	 * @param $selector
	 * @return ApistFilter
	 */
	public function find($selector)
	{
		$this->guardCrawler();
		return $this->node->filter($selector);
	}

	/**
	 * @return ApistFilter
	 */
	public function children()
	{
		$this->guardCrawler();
		return $this->node->children();
	}

	/**
	 * @return ApistFilter
	 */
	public function prev()
	{
		$this->guardCrawler();
		return $this->prevAll()->first();
	}

	/**
	 * @return ApistFilter
	 */
	public function prevAll()
	{
		$this->guardCrawler();
		return $this->node->previousAll();
	}

	/**
	 * @param $selector
	 * @return ApistFilter
	 */
	public function prevUntil($selector)
	{
		return $this->nodeUntil($selector, 'prev');
	}

	/**
	 * @return ApistFilter
	 */
	public function next()
	{
		$this->guardCrawler();
		return $this->nextAll()->first();
	}

	/**
	 * @return ApistFilter
	 */
	public function nextAll()
	{
		$this->guardCrawler();
		return $this->node->nextAll();
	}

	/**
	 * @param $selector
	 * @return ApistFilter
	 */
	public function nextUntil($selector)
	{
		return $this->nodeUntil($selector, 'next');
	}

	/**
	 * @param $selector
	 * @param $direction
	 * @return Crawler
	 */
	public function nodeUntil($selector, $direction)
	{
		$this->guardCrawler();
		$crawler = new Crawler;
		$filter = new static($this->node, $this->method);
		while (1)
		{
			$node = $filter->$direction();
			if (is_null($node))
			{
				break;
			}
			$filter->node = $node;
			if ($filter->is($selector)) break;
			$crawler->add($node->getNode(0));
		}
		return $crawler;
	}

	/**
	 * @return ApistFilter
	 */
	public function is($selector)
	{
		$this->guardCrawler();
		return count($this->filterNodes($selector)) > 0;
	}

	/**
	 * @return ApistFilter
	 */
	public function closest($selector)
	{
		$this->guardCrawler();
		$this->node = $this->node->parents();
		return $this->filterNodes($selector)->last();
	}

	/**
	 * @param $attribute
	 * @return ApistFilter
	 */
	public function attr($attribute)
	{
		$this->guardCrawler();
		return $this->node->attr($attribute);
	}

	/**
	 * @param $attribute
	 * @return ApistFilter
	 */
	public function hasAttr($attribute)
	{
		$this->guardCrawler();
		return ! is_null($this->node->attr($attribute));
	}

	/**
	 * @param $position
	 * @return ApistFilter
	 */
	public function eq($position)
	{
		$this->guardCrawler();
		return $this->node->eq($position);
	}

	/**
	 * @return ApistFilter
	 */
	public function first()
	{
		$this->guardCrawler();
		return $this->node->first();
	}

	/**
	 * @return ApistFilter
	 */
	public function last()
	{
		$this->guardCrawler();
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
	public function trim($mask = null)
	{
		$this->guardText();
		return trim($this->node, $mask);
	}

	/**
	 * @return ApistFilter
	 */
	public function ltrim($mask = null)
	{
		$this->guardText();
		return ltrim($this->node, $mask);
	}

	/**
	 * @return ApistFilter
	 */
	public function rtrim($mask = null)
	{
		$this->guardText();
		return rtrim($this->node, $mask);
	}
	
	/**
	 * @return ApistFilter
	 */
	public function str_replace($search, $replace, $count = null)
	{
		$this->guardText();
		return str_replace($search, $replace, $this->node, $count);
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

	/**
	 * Guard method to be called with Crawler object
	 */
	protected function guardCrawler()
	{
		if ( ! $this->node instanceof Crawler)
		{
			throw new \InvalidArgumentException('Current node isnt instance of Crawler.');
		}
	}

}
