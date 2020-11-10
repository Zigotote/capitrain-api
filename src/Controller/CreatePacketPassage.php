<?php
// api/src/Controller/CreatePacketPassage.php

namespace App\Controller;

use App\Entity\Ip;
use App\Entity\PacketPassage;
use App\Entity\Position;
use App\Entity\Traceroute;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class CreatePacketPassage extends AbstractController
{
	public function __construct()
	{}

	/**
	 * Create a new PacketPassage object
	 *
	 *@example Request format expected :
	 *          POST
	 *          body = {
	 *          	"position": {
	 *					"longitude": 42.5,
	 * 					"latitude" : 51.65,
	 * 					"country"  : "France",
	 * 					"city"	   : "Paris",
	 * 					"isp"	   : "Orange SA"
	 *				},
	 * 				"ip"		: "8.8.8.2",
	 * 				"indice"	: 42,
	 * 				"traceroute": 1
	 *          }
	 *
	 * "position" attribute is nullable
	 * "traceroute" attribute have to be an existing Traceroute id
	 * All attributes required (except "isp")
	 *
	 * @param Request $request
	 * @return JsonResponse|PacketPassage
	 */
	public function __invoke(Request $request): PacketPassage
	{
		$data = json_decode($request->getContent(), true);
		$positionData = null;
		if(array_key_exists('ip', $data)) {
			$ipValue = $data['ip'];
		}
		if(array_key_exists('indice', $data)) {
			$indice = $data['indice'];
		}
		if(array_key_exists('traceroute', $data)) {
			$traceRouteId = $data['traceroute'];
		}
		if(array_key_exists('position', $data)) {
			$positionData = $data['position'];
		}

		// Verify request validity
		if(empty($ipValue)) {
			return new JsonResponse(array('error' => 'ip_required'), Response::HTTP_BAD_REQUEST);
		}
		if(is_null($traceRouteId)) {
			return new JsonResponse(array('error' => 'traceRoute_required'), Response::HTTP_BAD_REQUEST);
		}
		$traceRoute = $this->getDoctrine()->getRepository('App:Traceroute')->findOneBy(['id' => $traceRouteId]);
		if (is_null($traceRoute)) {
			return new JsonResponse(array('error' => 'traceRoute_unknown'), Response::HTTP_BAD_REQUEST);
		}

		// Update and create elements
		$ip = $this->getDoctrine()->getRepository('App:Ip')->findOneBy(['ip' => $ipValue]);
		if(is_null($ip)) {
			$ip = new Ip();
			$ip->setIp($ipValue);
			$ip->setIsShared(false);
			$this->getDoctrine()->getManager()->persist($ip);

			// Upd position
			$this->updPosition($positionData,
							   $ip);
		}

		$packetPassage = new PacketPassage();
		$packetPassage->setIp($ip);
		$packetPassage->setIndice($indice);
		$packetPassage->setTraceRoute($traceRoute);
		if($indice > 0)
		{
			$previousList = $this->getPreviousPacketPassage($traceRoute, $indice);
			$packetPassage->setPrevious(empty($previousList) ? null : $previousList[0]);
		}

		$this->getDoctrine()->getManager()->persist($packetPassage);

		$this->getDoctrine()->getManager()->flush();

		return $packetPassage;
	}

	/**
	 * Update ip position, (from request data or from whoIs api)
	 * @param $positionData
	 * @param $ipValue
	 */
	private function updPosition($positionData, Ip $ip)
	{
		if (!empty($positionData))
		{
			$longitude = $positionData['longitude'];
			$latitude  = $positionData['latitude'];
			$country  = $positionData['country'];
			$city  = $positionData['city'];
			$isp  = $positionData['isp'];

			if(!is_null($isp)) {
				$ip->setIsp($isp);
				$this->getEntityManager()
					 ->persist($ip);
			}

			if (!is_null($longitude)
				&& !is_null($latitude)
				&& !is_null($city)
				&& !is_null($country))
			{
				$position = new Position();
				$position->setLatitude($latitude);
				$position->setLongitude($longitude);
				$position->setCountry($positionData['country']);
				$position->setCity($positionData['city']);

				$this->getDoctrine()
					 ->getManager()
					 ->persist($position);
			}
			else
			{
				$position = $this->getDoctrine()
					 ->getRepository('App:Position')
					 ->initPositionFormWhoIsAPI($ip);

			}
		}
		else
		{
			$position = $this->getDoctrine()
				 ->getRepository('App:Position')
				 ->initPositionFormWhoIsAPI($ip);
		}

		if (!is_null($position))
		{
			$ip->setPosition($position);
			$this->getDoctrine()->getManager()->persist($ip);
			$this->getDoctrine()->getManager()->flush();
		}
	}

	/**
	 * Get previous packetPassage if exist else return null
	 * (sometimes packetPassage haven't got previous because of traceroute failure or
	 *
	 * @param int $currentIndice
	 * @param $traceRoute
	 */
	private function getPreviousPacketPassage(Traceroute $traceRoute, int $currentIndice)
	{
		$filter = ['traceroute' => $traceRoute->getId(), 'indice' => $currentIndice - 1];
		return $this->getDoctrine()->getRepository('App:PacketPassage')->findBy($filter);
	}
}
