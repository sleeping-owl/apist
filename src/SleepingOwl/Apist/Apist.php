<?php namespace SleepingOwl\Apist;

use GuzzleHttp\Client;
use SleepingOwl\Apist\Methods\ApistMethod;
use SleepingOwl\Apist\Selectors\ApistFilter;
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
	 * @var bool
	 */
	protected $suppressExceptions = true;

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
	 * @return ApistFilter
	 */
	public static function filter($cssSelector)
	{
		return new ApistSelector($cssSelector);
	}

	/**
	 * Get current node
	 *
	 * @return ApistFilter
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
	 * @return boolean
	 */
	public function isSuppressExceptions()
	{
		return $this->suppressExceptions;
	}

	/**
	 * @param boolean $suppressExceptions
	 */
	public function setSuppressExceptions($suppressExceptions)
	{
		$this->suppressExceptions = $suppressExceptions;
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
	protected function get($url, $blueprint = null, $options = [])
	{
		return $this->request('GET', $url, $blueprint, $options);
	}

	/**
	 * @param $url
	 * @param $blueprint
	 * @param array $options
	 * @return array
	 */
	protected function head($url, $blueprint = null, $options = [])
	{
		return $this->request('HEAD', $url, $blueprint, $options);
	}

	/**
	 * @param $url
	 * @param $blueprint
	 * @param array $options
	 * @return array
	 */
	protected function post($url, $blueprint = null, $options = [])
	{
		return $this->request('POST', $url, $blueprint, $options);
	}

	/**
	 * @param $url
	 * @param $blueprint
	 * @param array $options
	 * @return array
	 */
	protected function put($url, $blueprint = null, $options = [])
	{
		return $this->request('PUT', $url, $blueprint, $options);
	}

	/**
	 * @param $url
	 * @param $blueprint
	 * @param array $options
	 * @return array
	 */
	protected function patch($url, $blueprint = null, $options = [])
	{
		return $this->request('PATCH', $url, $blueprint, $options);
	}

	/**
	 * @param $url
	 * @param $blueprint
	 * @param array $options
	 * @return array
	 */
	protected function delete($url, $blueprint = null, $options = [])
	{
		return $this->request('DELETE', $url, $blueprint, $options);
	}

}