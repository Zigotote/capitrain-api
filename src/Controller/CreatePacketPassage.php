<?php
// api/src/Controller/CreatePacketPassage.php

namespace App\Controller;

use App\Entity\Ip;
use App\Entity\PacketPassage;
use App\Entity\Position;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class CreatePacketPassage extends AbstractController
{
	public function __construct()
	{}

	/**
	 * @param Request $request
	 * @return JsonResponse|PacketPassage
	 *
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
		$this->getDoctrine()->getManager()->persist($packetPassage);

		$this->getDoctrine()->getManager()->flush();

		return $packetPassage;
	}

	/**
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
					 ->initPositionFormWhoIsAPI($ip->getIp());

			}
		}
		else
		{
			$position = $this->getDoctrine()
				 ->getRepository('App:Position')
				 ->initPositionFormWhoIsAPI($ip->getIp());
		}

		if (!is_null($position))
		{
			$ip->setPosition($position);
			$this->getDoctrine()->getManager()->persist($ip);
			$this->getDoctrine()->getManager()->flush();
		}
	}
}
