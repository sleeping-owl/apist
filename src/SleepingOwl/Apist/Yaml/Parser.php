<?php namespace SleepingOwl\Apist\Yaml;

use SleepingOwl\Apist\Apist;
use SleepingOwl\Apist\Selectors\ApistSelector;
use Symfony\Component\Yaml\Yaml;

class Parser
{
	/**
	 * @var array
	 */
	protected $methods = [];
	/**
	 * @var array
	 */
	protected $structures = [];
	/**
	 * @var string
	 */
	protected $file;

	/**
	 * @param $file
	 */
	function __construct($file)
	{
		$this->file = $file;
	}

	/**
	 * @param Apist $resource
	 */
	public function load(Apist $resource)
	{
		$data = Yaml::parse($this->file);
		if (isset($data['baseUrl']))
		{
			$resource->setBaseUrl($data['baseUrl']);
			unset($data['baseUrl']);
		}
		foreach ($data as $method => $methodConfig)
		{
			if ($method[0] === '_')
			{
				# structure
				$this->structures[$method] = $methodConfig;
			} else
			{
				# method
				if ( ! isset($methodConfig['blueprint']))
				{
					$methodConfig['blueprint'] = null;
				}
				$methodConfig['blueprint'] = $this->parseBlueprint($methodConfig['blueprint']);
				$this->methods[$method] = $methodConfig;
			}
		}
	}

	/**
	 * @param $blueprint
	 * @return array
	 */
	protected function parseBlueprint($blueprint)
	{
		$callback = function (&$value)
		{
			if (is_string($value))
			{
				$value = str_replace(':current', '*', $value);
			}
			if ($value[0] === ':')
			{
				# structure
				$structure = $this->getStructure($value);
				$value = $this->parseBlueprint($structure);
				return;
			}
			if (strpos($value, '|') === false) return;

			$parts = preg_split('/\s?\|\s?/', $value);
			$selector = array_shift($parts);
			$value = Apist::filter($selector);
			foreach ($parts as $part)
			{
				$this->addCallbackToFilter($value, $part);
			}
		};
		if ( ! is_array($blueprint))
		{
			$callback($blueprint);
		} else
		{
			array_walk_recursive($blueprint, $callback);
		}
		return $blueprint;
	}

	/**
	 * @param ApistSelector $filter
	 * @param $callback
	 */
	protected function addCallbackToFilter(ApistSelector $filter, $callback)
	{
		$method = strtok($callback, '(),');
		$arguments = [];
		while ($argument = strtok('(),'))
		{
			$argument = trim($argument);
			if (preg_match('/^[\'"].*[\'"]$/', $argument))
			{
				$argument = substr($argument, 1, -1);
			}
			if ($argument[0] === ':')
			{
				# structure
				$structure = $this->getStructure($argument);
				$argument = $this->parseBlueprint($structure);
			}
			$arguments[] = $argument;
		}
		$filter->addCallback($method, $arguments);
	}

	/**
	 * @param $name
	 * @return mixed
	 */
	protected function getStructure($name)
	{
		$structure = '_' . substr($name, 1);
		if ( ! isset($this->structures[$structure]))
		{
			throw new \InvalidArgumentException("Structure '$structure' not found.'");
		}
		return $this->structures[$structure];
	}

	/**
	 * @param $name
	 * @return array
	 */
	public function getMethod($name)
	{
		if ( ! isset($this->methods[$name]))
		{
			throw new \InvalidArgumentException("Method '$name' not found.'");
		}
		$methodConfig = $this->methods[$name];
		return $methodConfig;
	}

	/**
	 * @param $method
	 * @param $arguments
	 * @return mixed
	 */
	public function insertMethodArguments($method, $arguments)
	{
		array_walk_recursive($method, function (&$value) use ($arguments)
		{
			if ( ! is_string($value)) return;
			$value = preg_replace_callback('/\$(?<num>[0-9]+)/', function ($finded) use ($arguments)
			{
				$argumentPosition = intval($finded['num']) - 1;
				return isset($arguments[$argumentPosition]) ? $arguments[$argumentPosition] : null;
			}, $value);
		});
		return $method;
	}
} 