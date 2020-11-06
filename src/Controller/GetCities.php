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

	public function __invoke(Request $request)
	{
		return $this->getDoctrine()->getRepository('App:Position')
								   ->getAllCities();
	}
}
