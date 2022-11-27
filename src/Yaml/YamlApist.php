<?php

namespace SleepingOwl\Apist\Yaml;

use GuzzleHttp\Exception\GuzzleException;
use SleepingOwl\Apist\Apist;

class YamlApist extends Apist
{
    /** @var Parser */
    protected $parser;

    /**
     * @param null|mixed $file
     */
    public function __construct($file = null, array $options = [])
    {
        if (!is_null($file)) {
            $this->loadFromYml($file);
        }
        parent::__construct($options);
    }

    /**
     * @return array
     *
     * @throws GuzzleException
     */
    public function __call($name, $arguments)
    {
        if (is_null($this->parser)) {
            throw new \InvalidArgumentException(sprintf('Method "%s" not found.', $name));
        }
        $method = $this->parser->getMethod($name);
        $method = $this->parser->insertMethodArguments($method, $arguments);
        $httpMethod = isset($method['method']) ? strtoupper($method['method']) : 'GET';
        $options = $method['options'] ?? [];

        return $this->request($httpMethod, $method['url'], $method['blueprint'], $options);
    }

    /**
     * Load method data from yaml file.
     *
     * @param mixed $file
     */
    protected function loadFromYml($file)
    {
        $this->parser = new Parser($file);
        $this->parser->load($this);
    }
}
