<?php namespace SleepingOwl\Apist\Methods;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use SleepingOwl\Apist\Apist;
use SleepingOwl\Apist\DomCrawler\Crawler;
use SleepingOwl\Apist\Selectors\ApistSelector;

class ApistMethod
{
	/**
	 * @var Apist
	 */
	protected $resource;
	/**
	 * @var string
	 */
	protected $url;
	/**
	 * @var ApistSelector[]|ApistSelector
	 */
	protected $schemaBlueprint;
	/**
	 * @var string
	 */
	protected $method = 'GET';
	/**
	 * @var string
	 */
	protected $content;
	/**
	 * @var Crawler
	 */
	protected $crawler;

	/**
	 * @param $resource
	 * @param $url
	 * @param $schemaBlueprint
	 */
	function __construct($resource, $url, $schemaBlueprint)
	{
		$this->resource = $resource;
		$this->url = $url;
		$this->schemaBlueprint = $schemaBlueprint;
		$this->crawler = new Crawler();
	}

	/**
	 * Perform method action
	 *
	 * @param array $arguments
	 * @return array
	 */
	public function get($arguments = [])
	{
		try
		{
			$this->makeRequest($arguments);
		} catch (ConnectException $e)
		{
			$url = $e->getRequest()->getUrl();
			return $this->errorResponse($e->getCode(), $e->getMessage(), $url);
		} catch (RequestException $e)
		{
			$url = $e->getRequest()->getUrl();
			$status = $e->getCode();
			$response = $e->getResponse();
			$reason = $e->getMessage();
			if ( ! is_null($response))
			{
				$reason = $response->getReasonPhrase();
			}
			return $this->errorResponse($status, $reason, $url);
		}

		return $this->parseBlueprint($this->schemaBlueprint);
	}

	/**
	 * Make http request
	 *
	 * @param array $arguments
	 */
	protected function makeRequest($arguments = [])
	{
		$defaults = $this->getDefaultOptions();
		$arguments = array_merge($defaults, $arguments);
		$client = $this->resource->getGuzzle();
		$request = $client->createRequest($this->getMethod(), $this->url, $arguments);
		$response = $client->send($request);
		$this->setContent((string)$response->getBody());
	}

	/**
	 * @param $blueprint
	 * @param null $node
	 * @return array|string
	 */
	public function parseBlueprint($blueprint, $node = null)
	{
		if (is_null($blueprint))
		{
			return $this->content;
		}
		if ( ! is_array($blueprint))
		{
			$blueprint = $this->parseBlueprintValue($blueprint, $node);
		} else
		{
			array_walk_recursive($blueprint, function (&$value) use ($node)
			{
				$value = $this->parseBlueprintValue($value, $node);
			});
		}
		return $blueprint;
	}

	/**
	 * @param $value
	 * @param $node
	 * @return array|string
	 */
	protected function parseBlueprintValue($value, $node)
	{
		if ($value instanceof ApistSelector)
		{
			return $value->getValue($this, $node);
		}
		return $value;
	}

	/**
	 * Response with error
	 *
	 * @param $status
	 * @param $reason
	 * @param $url
	 * @return array
	 */
	protected function errorResponse($status, $reason, $url)
	{
		return [
			'url'   => $url,
			'error' => [
				'status' => $status,
				'reason' => $reason,
			]
		];
	}

	/**
	 * @return Crawler
	 */
	public function getCrawler()
	{
		return $this->crawler;
	}

	/**
	 * @return string
	 */
	public function getMethod()
	{
		return $this->method;
	}

	/**
	 * @param string $method
	 * @return $this
	 */
	public function setMethod($method)
	{
		$this->method = $method;
		return $this;
	}

	/**
	 * @param string $content
	 * @return $this
	 */
	public function setContent($content)
	{
		$this->content = $content;
		$this->crawler->addContent($content);
		return $this;
	}

	/**
	 * @return array
	 */
	protected function getDefaultOptions()
	{
		return [
			'cookies' => true
		];
	}

	/**
	 * @return Apist
	 */
	public function getResource()
	{
		return $this->resource;
	}

}