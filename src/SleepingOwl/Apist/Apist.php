<?php namespace SleepingOwl\Apist;

use GuzzleHttp\Client;
use SleepingOwl\Apist\Methods\ApistMethod;
use SleepingOwl\Apist\Selectors\ApistSelector;
use SleepingOwl\Apist\Yaml\YamlApist;
use Symfony\Component\DomCrawler\Crawler;

abstract class Apist
{
	/**
	 * @var string
	 */
	protected $baseUrl;
	/**
	 * @var Client
	 */
	protected $guzzle;
	/**
	 * @var ApistMethod
	 */
	protected $currentMethod;

	/**
	 * @param array $options
	 */
	function __construct($options = [])
	{
		$options['base_url'] = $this->getBaseUrl();
		$this->guzzle = new Client($options);
	}

	/**
	 * @return Client
	 */
	public function getGuzzle()
	{
		return $this->guzzle;
	}

	/**
	 * @param Client $guzzle
	 */
	public function setGuzzle($guzzle)
	{
		$this->guzzle = $guzzle;
	}

	/**
	 * Create filter object
	 *
	 * @param $cssSelector
	 * @return ApistSelector
	 */
	public static function filter($cssSelector)
	{
		return new ApistSelector($cssSelector);
	}

	/**
	 * Get current node
	 *
	 * @return ApistSelector
	 */
	public static function current()
	{
		return static::filter('*');
	}

	/**
	 * Initialize api from yaml configuration file
	 *
	 * @param $file
	 * @return YamlApist
	 */
	public static function fromYaml($file)
	{
		return new YamlApist($file, []);
	}

	/**
	 * @return ApistMethod
	 */
	public function getCurrentMethod()
	{
		return $this->currentMethod;
	}

	/**
	 * @return string
	 */
	public function getBaseUrl()
	{
		return $this->baseUrl;
	}

	/**
	 * @param string $baseUrl
	 */
	public function setBaseUrl($baseUrl)
	{
		$this->baseUrl = $baseUrl;
	}

	/**
	 * @param $httpMethod
	 * @param $url
	 * @param $blueprint
	 * @param array $options
	 * @return array
	 */
	protected function request($httpMethod, $url, $blueprint, $options = [])
	{
		$this->currentMethod = new ApistMethod($this, $url, $blueprint);
		$this->currentMethod->setMethod($httpMethod);
		$result = $this->currentMethod->get($options);
		$this->currentMethod = null;
		return $result;
	}

	/**
	 * @param $content
	 * @param $blueprint
	 * @return array|string
	 */
	protected function parse($content, $blueprint)
	{
		$this->currentMethod = new ApistMethod($this, null, $blueprint);
		$this->currentMethod->setContent($content);
		$result = $this->currentMethod->parseBlueprint($blueprint);
		$this->currentMethod = null;
		return $result;
	}

	/**
	 * @param $url
	 * @param $blueprint
	 * @param array $options
	 * @return array
	 */
	protected function get($url, $blueprint, $options = [])
	{
		return $this->request('GET', $url, $blueprint, $options);
	}

	/**
	 * @param $url
	 * @param $blueprint
	 * @param array $options
	 * @return array
	 */
	protected function head($url, $blueprint, $options = [])
	{
		return $this->request('HEAD', $url, $blueprint, $options);
	}

	/**
	 * @param $url
	 * @param $blueprint
	 * @param array $options
	 * @return array
	 */
	protected function post($url, $blueprint, $options = [])
	{
		return $this->request('POST', $url, $blueprint, $options);
	}

	/**
	 * @param $url
	 * @param $blueprint
	 * @param array $options
	 * @return array
	 */
	protected function put($url, $blueprint, $options = [])
	{
		return $this->request('PUT', $url, $blueprint, $options);
	}

	/**
	 * @param $url
	 * @param $blueprint
	 * @param array $options
	 * @return array
	 */
	protected function patch($url, $blueprint, $options = [])
	{
		return $this->request('PATCH', $url, $blueprint, $options);
	}

	/**
	 * @param $url
	 * @param $blueprint
	 * @param array $options
	 * @return array
	 */
	protected function delete($url, $blueprint, $options = [])
	{
		return $this->request('DELETE', $url, $blueprint, $options);
	}

	/**
	 * @param $node
	 * @return mixed
	 */
	public function element($node)
	{
		return $node;
	}

	/**
	 * @param $node
	 * @param $callback
	 * @return mixed
	 */
	public function call($node, $callback)
	{
		return $callback($node);
	}

	/**
	 * @param $node
	 * @param $callback
	 * @return mixed
	 */
	public function check($node, $callback)
	{
		return $this->call($node, $callback);
	}

	/**
	 * @param $node
	 * @return bool
	 */
	public function exists($node)
	{
		return (bool)count($node);
	}

	/**
	 * @param $node
	 * @param $blueprint
	 * @return array|string
	 */
	public function then($node, $blueprint)
	{
		if ($node === true)
		{
			return $this->getCurrentMethod()->parseBlueprint($blueprint);
		}
		return $node;
	}

	/**
	 * @param Crawler $node
	 * @param $blueprint
	 * @return mixed
	 */
	public function each(Crawler $node, $blueprint = null)
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
				return $this->getCurrentMethod()->parseBlueprint($blueprint, $node);
			};
		}
		return $node->each($callback);
	}

}