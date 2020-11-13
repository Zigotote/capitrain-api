<?php
// api/src/Controller/GetCitiesRegion.php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetCitiesRegion extends AbstractController
{
	public function __construct()
	{
	}

	/**
	 * Return the list of all ip cities
	 * @example
	 *         Format expected:
	 *         		body={"country": "France", "region": "Bretagne"}
	 *         Will return something like this :
	 *        		[
	 *         			{"city": "Saint-Malo"},
	 *         			{"city": "Rennes"},
	 *         			{"city": "Brest"}
	 *         		]
	 *
	 * @param Request $request
	 *
	 * @return mixed
	 */
	public function __invoke(Request $request)
	{
		$data = json_decode($request->getContent(), true);
		if(!array_key_exists('country', $data) || is_null($data['country'])) {
			return new JsonResponse(array('error' => 'country_required'), Response::HTTP_BAD_REQUEST);
		}
		if(!array_key_exists('region', $data) || is_null($data['region'])) {
			return new JsonResponse(array('error' => 'region_required'), Response::HTTP_BAD_REQUEST);
		}

		$country = $data['country'];
		$region = $data['region'];
		return $this->getDoctrine()->getRepository('App:Position')
								   ->getAllRegionCities($country, $region);
	}
}
