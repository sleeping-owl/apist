<?php

use SleepingOwl\Apist\Apist;

class TestApi extends Apist
{

	public function index()
	{
		return $this->get('/', [
			'title'     => Apist::filter('.page_head .title'),
			'copyright' => Apist::filter('.copyright .about a')->first()->attr('href'),
			'posts'     => Apist::filter('.posts .post')->each(function ()
			{
				return [
					'title' => Apist::filter('h1.title a')->text()
				];
			}),
		]);
	}

	public function element_not_found()
	{
		return $this->get('/', [
			'title' => Apist::filter('.page_header')
		]);
	}

	public function non_array_blueprint()
	{
		return $this->get('/', Apist::filter('.page_head .title'));
	}

} 