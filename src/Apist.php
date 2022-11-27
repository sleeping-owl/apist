<?php

namespace SleepingOwl\Apist;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use SleepingOwl\Apist\Methods\ApistMethod;
use SleepingOwl\Apist\Selectors\ApistSelector;
use SleepingOwl\Apist\Yaml\YamlApist;

abstract class Apist
{
    /** @var string */
    protected $baseUrl;

    /** @var Client */
    protected $httpClient;

    /** @var ApistMethod */
    protected $currentMethod;

    /** @var ApistMethod */
    protected $lastMethod;

    /** @var bool */
    protected $suppressExceptions = true;

    public function __construct(array $options = [])
    {
        $options['base_url'] = $this->getBaseUrl();
        $this->httpClient = new Client($options);
    }

    /**
     * @return Client
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    public function setHttpClient(ClientInterface $httpClient): void
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Create filter object.
     *
     * @param mixed $cssSelector
     *
     * @return ApistSelector
     */
    public static function filter($cssSelector)
    {
        return new ApistSelector($cssSelector);
    }

    /**
     * Get current node.
     *
     * @return ApistSelector
     */
    public static function current()
    {
        return static::filter('*');
    }

    /**
     * Initialize api from yaml configuration file.
     *
     * @param mixed $file
     *
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
     * @return ApistMethod
     */
    public function getLastMethod()
    {
        return $this->lastMethod;
    }

    /**
     * @param string $baseUrl
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * @return bool
     */
    public function isSuppressExceptions()
    {
        return $this->suppressExceptions;
    }

    /**
     * @param bool $suppressExceptions
     */
    public function setSuppressExceptions($suppressExceptions)
    {
        $this->suppressExceptions = $suppressExceptions;
    }

    /**
     * @param mixed $httpMethod
     * @param mixed $url
     * @param mixed $blueprint
     *
     * @return array|string
     *
     * @throws GuzzleException
     */
    protected function request($httpMethod, $url, $blueprint, array $options = [])
    {
        $this->currentMethod = new ApistMethod($this, $url, $blueprint);
        $this->lastMethod = $this->currentMethod;
        $this->currentMethod->setMethod($httpMethod);
        $result = $this->currentMethod->get($options);
        $this->currentMethod = null;

        return $result;
    }

    /**
     * @param mixed $content
     * @param mixed $blueprint
     *
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
     * @param mixed      $url
     * @param null|mixed $blueprint
     *
     * @return array|string
     *
     * @throws GuzzleException
     */
    protected function get($url, $blueprint = null, array $options = [])
    {
        return $this->request('GET', $url, $blueprint, $options);
    }

    /**
     * @param mixed      $url
     * @param null|mixed $blueprint
     *
     * @return array|string
     *
     * @throws GuzzleException
     */
    protected function head($url, $blueprint = null, array $options = [])
    {
        return $this->request('HEAD', $url, $blueprint, $options);
    }

    /**
     * @param mixed      $url
     * @param null|mixed $blueprint
     *
     * @return array|string
     *
     * @throws GuzzleException
     */
    protected function post($url, $blueprint = null, array $options = [])
    {
        return $this->request('POST', $url, $blueprint, $options);
    }

    /**
     * @param mixed      $url
     * @param null|mixed $blueprint
     * @param array      $options
     *
     * @return array|string
     *
     * @throws GuzzleException
     */
    protected function put($url, $blueprint = null, $options = [])
    {
        return $this->request('PUT', $url, $blueprint, $options);
    }

    /**
     * @param mixed      $url
     * @param null|mixed $blueprint
     *
     * @return array|string
     *
     * @throws GuzzleException
     */
    protected function patch($url, $blueprint = null, array $options = [])
    {
        return $this->request('PATCH', $url, $blueprint, $options);
    }

    /**
     * @param mixed      $url
     * @param null|mixed $blueprint
     *
     * @return array|string
     *
     * @throws GuzzleException
     */
    protected function delete($url, $blueprint = null, array $options = [])
    {
        return $this->request('DELETE', $url, $blueprint, $options);
    }
}
