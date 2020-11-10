<?php
// api/src/Controller/GetCities.php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class GetCities extends AbstractController
{
	public function __construct()
	{
	}

	/**
	 * Return the list of all ip cities
	 * @example
	 *         Will return something like this :
	 *         [
	 *         		{"country": "France", "city": "Île d'Yeu"},
	 *         		{"country": "France", "city": "Rennes"},
	 *         		{"country": "United Kingdom", "city": "London"},
	 *         		{"country": "United Kingdom", "city": "Manchester"}
	 *         ]
	 *
	 * @param Request $request
	 *
	 * @return mixed
	 */
	public function __invoke(Request $request)
	{
		return $this->getDoctrine()->getRepository('App:Position')
								   ->getAllCities();
	}
}
