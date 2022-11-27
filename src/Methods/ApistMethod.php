<?php

namespace SleepingOwl\Apist\Methods;

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface;
use SleepingOwl\Apist\Apist;
use SleepingOwl\Apist\DomCrawler\Crawler;
use SleepingOwl\Apist\Selectors\ApistSelector;

class ApistMethod
{
    /** @var Apist */
    protected $resource;

    /** @var string */
    protected $url;

    /** @var ApistSelector|ApistSelector[] */
    protected $schemaBlueprint;

    /** @var string */
    protected $method = 'GET';

    /** @var string */
    protected $content;

    /** @var Crawler */
    protected $crawler;

    /** @var ResponseInterface */
    protected $response;

    public function __construct($resource, $url, $schemaBlueprint)
    {
        $this->resource = $resource;
        $this->url = $url;
        $this->schemaBlueprint = $schemaBlueprint;
        $this->crawler = new Crawler();
    }

    /**
     * Perform method action.
     *
     * @return array|string
     *
     * @throws GuzzleException
     */
    public function get(array $arguments = [])
    {
        try {
            $this->makeRequest($arguments);
        } catch (ConnectException $e) {
            $url = $e->getRequest()->getUri();

            return $this->errorResponse($e->getCode(), $e->getMessage(), $url);
        } catch (RequestException $e) {
            $url = $e->getRequest()->getUri();
            $status = $e->getCode();
            $response = $e->getResponse();
            $reason = $e->getMessage();
            if (!is_null($response)) {
                $reason = $response->getReasonPhrase();
            }

            return $this->errorResponse($status, $reason, $url);
        }

        return $this->parseBlueprint($this->schemaBlueprint);
    }

    /**
     * @param null  $node
     * @param mixed $blueprint
     *
     * @return null|array|Crawler|string
     */
    public function parseBlueprint($blueprint, $node = null)
    {
        if (is_null($blueprint)) {
            return $this->content;
        }
        if (!is_array($blueprint)) {
            $blueprint = $this->parseBlueprintValue($blueprint, $node);
        } else {
            array_walk_recursive($blueprint, function (&$value) use ($node) {
                $value = $this->parseBlueprintValue($value, $node);
            });
        }

        return $blueprint;
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
     *
     * @return $this
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * @param string $content
     *
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;
        $this->crawler->addContent($content);

        return $this;
    }

    /**
     * @return Apist
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param MessageInterface $response
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }

    /**
     * Make http request.
     *
     * @throws GuzzleException
     */
    protected function makeRequest(array $options = [])
    {
        $defaults = $this->getDefaultOptions();
        $options = array_merge($defaults, $options);
        $client = $this->resource->getHttpClient();

        $response = $client->request($this->getMethod(), $this->url, $options);
        $this->setResponse($response);
        $this->setContent((string)$response->getBody());
    }

    /**
     * @param mixed $value
     * @param mixed $node
     *
     * @return null|array|Crawler|string
     */
    protected function parseBlueprintValue($value, $node)
    {
        if ($value instanceof ApistSelector) {
            return $value->getValue($this, $node);
        }

        return $value;
    }

    /**
     * Response with error.
     *
     * @param mixed $status
     * @param mixed $reason
     * @param mixed $url
     *
     * @return array
     */
    protected function errorResponse($status, $reason, $url)
    {
        return [
            'url' => $url,
            'error' => [
                'status' => $status,
                'reason' => $reason,
            ],
        ];
    }

    /**
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            'cookies' => true,
        ];
    }
}
