<?php

namespace SleepingOwl\Apist\Selectors;

use SleepingOwl\Apist\Apist;
use SleepingOwl\Apist\DomCrawler\Crawler;
use SleepingOwl\Apist\Methods\ApistMethod;

/**
 * Class ApistFilter.
 *
 * @method ApistFilter else($blueprint)
 */
class ApistFilter
{
    /** @var Crawler */
    protected $node;

    /** @var Apist */
    protected $resource;

    /** @var ApistMethod */
    protected $method;

    /**
     * @param mixed $node
     */
    public function __construct($node, ApistMethod $method)
    {
        $this->node = $node;
        $this->method = $method;
        $this->resource = $method->getResource();
    }

    /**
     * @return string
     */
    public function text()
    {
        $this->guardCrawler();

        return $this->node->text();
    }

    /**
     * @return string
     */
    public function html()
    {
        $this->guardCrawler();

        return $this->node->html();
    }

    /**
     * @param mixed $selector
     *
     * @return Crawler
     */
    public function filter($selector)
    {
        $this->guardCrawler();

        return $this->node->filter($selector);
    }

    /**
     * @param mixed $selector
     *
     * @return Crawler
     */
    public function filterNodes($selector)
    {
        $this->guardCrawler();
        $rootNode = $this->method->getCrawler();
        $crawler = new Crawler();
        $rootNode->filter($selector)->each(function (Crawler $filteredNode) use ($crawler) {
            $filteredNode = $filteredNode->getNode(0);
            foreach ($this->node as $node) {
                if ($filteredNode === $node) {
                    $crawler->add($node);

                    break;
                }
            }
        });

        return $crawler;
    }

    /**
     * @param mixed $selector
     *
     * @return ApistFilter
     */
    public function find($selector)
    {
        $this->guardCrawler();

        return $this->node->filter($selector);
    }

    /**
     * @return Crawler
     */
    public function children()
    {
        $this->guardCrawler();

        return $this->node->children();
    }

    /**
     * @return Crawler
     */
    public function prev()
    {
        $this->guardCrawler();

        return $this->prevAll()->first();
    }

    /**
     * @return Crawler
     */
    public function prevAll()
    {
        $this->guardCrawler();

        return $this->node->previousAll();
    }

    /**
     * @param mixed $selector
     *
     * @return Crawler
     */
    public function prevUntil($selector)
    {
        return $this->nodeUntil($selector, 'prev');
    }

    /**
     * @return Crawler
     */
    public function next()
    {
        $this->guardCrawler();

        return $this->nextAll()->first();
    }

    /**
     * @return Crawler
     */
    public function nextAll()
    {
        $this->guardCrawler();

        return $this->node->nextAll();
    }

    /**
     * @param mixed $selector
     *
     * @return Crawler
     */
    public function nextUntil($selector)
    {
        return $this->nodeUntil($selector, 'next');
    }

    /**
     * @param mixed $selector
     * @param mixed $direction
     *
     * @return Crawler
     */
    public function nodeUntil($selector, $direction)
    {
        $this->guardCrawler();
        $crawler = new Crawler();
        $filter = new static($this->node, $this->method);
        while (1) {
            $node = $filter->{$direction}();
            if (is_null($node)) {
                break;
            }
            $filter->node = $node;
            if ($filter->is($selector)) {
                break;
            }
            $crawler->add($node->getNode(0));
        }

        return $crawler;
    }

    /**
     * @param mixed $selector
     *
     * @return bool
     */
    public function is($selector)
    {
        $this->guardCrawler();

        return count($this->filterNodes($selector)) > 0;
    }

    /**
     * @param mixed $selector
     *
     * @return Crawler
     */
    public function closest($selector)
    {
        $this->guardCrawler();
        $this->node = $this->node->parents();

        return $this->filterNodes($selector)->last();
    }

    /**
     * @param mixed $attribute
     *
     * @return string
     */
    public function attr($attribute)
    {
        $this->guardCrawler();

        return $this->node->attr($attribute);
    }

    /**
     * @param mixed $attribute
     *
     * @return bool
     */
    public function hasAttr($attribute)
    {
        $this->guardCrawler();

        return !is_null($this->node->attr($attribute));
    }

    /**
     * @param mixed $position
     *
     * @return Crawler
     */
    public function eq($position)
    {
        $this->guardCrawler();

        return $this->node->eq($position);
    }

    /**
     * @return Crawler
     */
    public function first()
    {
        $this->guardCrawler();

        return $this->node->first();
    }

    /**
     * @return Crawler
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
     * @param mixed $callback
     *
     * @return ApistFilter
     */
    public function call($callback)
    {
        return $callback($this->node);
    }

    /**
     * @param mixed $mask
     *
     * @return string
     */
    public function trim($mask = " \t\n\r\0\x0B")
    {
        $this->guardText();

        return trim($this->node, $mask);
    }

    /**
     * @param mixed $mask
     *
     * @return string
     */
    public function ltrim($mask = " \t\n\r\0\x0B")
    {
        $this->guardText();

        return ltrim($this->node, $mask);
    }

    /**
     * @param mixed $mask
     *
     * @return string
     */
    public function rtrim($mask = " \t\n\r\0\x0B")
    {
        $this->guardText();

        return rtrim($this->node, $mask);
    }

    /**
     * @param mixed      $search
     * @param mixed      $replace
     * @param null|mixed $count
     *
     * @return string
     */
    public function str_replace($search, $replace, $count = null)
    {
        $this->guardText();

        return str_replace($search, $replace, $this->node, $count);
    }

    /**
     * @return int
     */
    public function intval()
    {
        $this->guardText();

        return (int)$this->node;
    }

    /**
     * @return float
     */
    public function floatval()
    {
        $this->guardText();

        return (float)$this->node;
    }

    /**
     * @return bool
     */
    public function exists()
    {
        return count($this->node) > 0;
    }

    /**
     * @param mixed $callback
     *
     * @return ApistFilter
     */
    public function check($callback)
    {
        return $this->call($callback);
    }

    /**
     * @param mixed $blueprint
     *
     * @return null|array|Crawler|string
     */
    public function then($blueprint)
    {
        if ($this->node === true) {
            return $this->method->parseBlueprint($blueprint);
        }

        return $this->node;
    }

    /**
     * @param null|mixed $blueprint
     *
     * @return array
     */
    public function each($blueprint = null)
    {
        $callback = $blueprint;
        if (is_null($callback)) {
            $callback = function ($node) {
                return $node;
            };
        }
        if (!is_callable($callback)) {
            $callback = function ($node) use ($blueprint) {
                return $this->method->parseBlueprint($blueprint, $node);
            };
        }

        return $this->node->each($callback);
    }

    /**
     * Guard string method to be called with Crawler object.
     */
    protected function guardText()
    {
        if (is_object($this->node)) {
            $this->node = $this->node->text();
        }
    }

    /**
     * Guard method to be called with Crawler object.
     */
    protected function guardCrawler()
    {
        if (!$this->node instanceof Crawler) {
            throw new \InvalidArgumentException(sprintf('Current node isn\'t instance of Crawler. Got: %s', gettype($this->node)));
        }
    }
}
