<?php
// api/src/Controller/GetAllNodeUse.php

namespace App\Controller;

use App\Entity\PacketPassage;
use App\Entity\Position;
use App\Repository\PacketPassageRepository;
use App\Repository\TracerouteRepository;
use App\Services\GetNodeUseService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class GetAllNodeUse
 * Return city weight in network roots
 * @package App\Controller
 */
class GetAllNodeUse extends AbstractController
{
	/**
	 * GetAllNodeUse constructor.
	 */
	public function __construct()
	{
	}

	/**
	 * @param Request $request
	 * @return JsonResponse|PacketPassage
	 *
	 */
	public function __invoke(Request $request)
	{
		$manager = $this->getDoctrine()->getManager();
		/** @var PacketPassageRepository $packetPassageRepo */
		$packetPassageRepo = $this->getDoctrine()->getRepository('App:PacketPassage');
		$nbRoute = $packetPassageRepo->count([]);
		$positions = $packetPassageRepo->findAllOrderByPosition();

		$res = [];
		/** @var Position $position */
		foreach($positions as $position) {
			$country = $position['country'];
			$city = $position['city'];

			if(!array_key_exists($country, $res)) {
				$res[$country] = [];
			}
			if(!array_key_exists($city, $res[$country])) {
				$res[$country][$city] = 0;
			}
			$res[$country][$city] += (1/$nbRoute);
		}
		return $res;
	}
}
