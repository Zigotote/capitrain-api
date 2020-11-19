<?php
// api/src/Controller/GetRegion.php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class GetRegion extends AbstractController
{
	public function __construct()
	{
	}

	/**
	 * Return the list of all ip regions
	 *
	 * @param Request $request
	 *
	 * @return mixed
	 */
	public function __invoke(Request $request)
	{
		$positions = $this->getDoctrine()->getRepository('App:Position')
										 ->getAllRegion();
		$res = [];
		foreach ($positions as $position) {
			$country = $position['country'];
			$region = $position['region'];

			if(!array_key_exists($country, $res)) {
				$res[$country] = ['country' => $country, 'regions' => []];
			}
			array_push($res[$country]['regions'], $region);
		}
		return $res;
	}
}
