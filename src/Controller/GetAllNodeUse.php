<?php
// api/src/Controller/GetAllNodeUse.php

namespace App\Controller;

use App\Entity\PacketPassage;
use App\Entity\Position;
use App\Repository\PacketPassageRepository;
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
	 * Return city weight in network roots
	 * 
	 * @example  :
	 *           With the next map:
	 *           	A->B
	 *           	B->C
	 *           	D->B
	 *           	B->A
	 *
	 *           The request wil return
	 *           	{
	 *           		A: 25%,
	 *           		B: 50%
	 *           		C: 12.5%
	 *           		D: 12.5%
	 *           	}
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
