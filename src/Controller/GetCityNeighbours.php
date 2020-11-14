<?php
// api/src/Controller/GetCityNeighbours.php

namespace App\Controller;
use App\Entity\Ip;
use App\Entity\PacketPassage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetCityNeighbours extends AbstractController
{
	private $city;

	public function __construct()
	{
	}

	/**
	 * Return the list of all neightbourg cities
	 * @example
	 *         Format expected:
	 *         		body={"country": "France", "city": "Brest"}
	 *         Will return something like this :
	 *        		[
	 *         			{"city": "Angers"},
	 *         			{"city": "Rennes"},
	 *         			{"city": "Paris"}
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
		if(!array_key_exists('city', $data) || is_null($data['city'])) {
			return new JsonResponse(array('error' => 'city_required'), Response::HTTP_BAD_REQUEST);
		}

		$country = $data['country'];
		$this->city = $data['city'];
		$packetPassages = $this->getDoctrine()->getRepository('App:PacketPassage')
											  ->getAllCityPacketPassages($country, $this->city);
		$res = new JsonResponse([
			"leaving" => [],
			"entering"=> []
		]);
		/** @var PacketPassage $pp */
		foreach($packetPassages as $pp) {
			$this->addPacketPassage($pp->getNext(),
									$res, true);
			$this->addPacketPassage($pp->getPrevious(),
									$res, false);
		}
	}

	/**
	 * Return true if $pp have no position
	 *
	 * @param PacketPassage $pp
	 * @return bool
	 */
	private function emptyPosition(PacketPassage $pp) {
		return is_null($pp->getIp())
			|| is_null($pp->getIp()->getPosition());
	}

	/**
	 * Upd $res to add $next city packetPassage
	 *
	 * @param PacketPassage $pp
	 * @param JsonResponse  $res
	 */
	private function addPacketPassage(PacketPassage $pp, JsonResponse $res, $isLeavingPacket)
	{
		$ppType = $isLeavingPacket ? "leaving": "entering";
		if (!is_null($pp) && !$this->emptyPosition($pp))
		{
			$city = $pp->getIp()
					   ->getPosition();
			if($city == $this->city) {return;}
			if (!array_key_exists($city,
								  $res[$ppType]))
			{
				$res[$ppType][$city] = 0;
			}
			$res[$ppType][$city]++;
		}
	}
}
