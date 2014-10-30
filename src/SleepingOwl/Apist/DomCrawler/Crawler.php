<?php namespace SleepingOwl\Apist\DomCrawler;

use Symfony\Component\DomCrawler\Crawler as SymfonyCrawler;

class Crawler extends SymfonyCrawler
{
	protected $pseudoClasses = [
		'first',
		'last',
		'eq'
	];

	public function filter($selector)
	{
		if ($result = $this->parsePseudoClasses($selector))
		{
			return $result;
		}
		return parent::filter($selector);
	}

	protected function parsePseudoClasses($selector)
	{
		foreach ($this->pseudoClasses as $pseudoClass)
		{
			if (preg_match('/^(?<first>.*?):' . $pseudoClass . '(\((?<param>[0-9]+)\))?(?<last>.*)$/', $selector, $attrs))
			{
				$result = $this->filter($attrs['first']);
				$result = call_user_func([
					$result,
					$pseudoClass
				], $attrs['param']);
				$filter = $attrs['last'];
				if (trim($filter) != '')
				{
					$result = $result->filter($filter);
				}
				return $result;
			}
		}
		return null;
	}

}