<?php namespace SleepingOwl\Apist;

use GuzzleHttp\Client;
use SleepingOwl\Apist\Methods\ApistMethod;
use SleepingOwl\Apist\Selectors\ApistSelector;

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
	 * @param array $options
	 */
	function __construct($options = [])
	{
		$options['base_url'] = $this->baseUrl;
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
	 * @param $httpMethod
	 * @param $url
	 * @param $blueprint
	 * @param array $options
	 * @return array
	 */
	protected function request($httpMethod, $url, $blueprint, $options = [])
	{
		$method = new ApistMethod($this, $url, $blueprint);
		$method->setMethod($httpMethod);
		return $method->get($options);
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

}