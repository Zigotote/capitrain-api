<?php
// api/src/Controller/CreatePacketPassagePublication.php

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
	 *
	 * @param Request $request
	 * @return JsonResponse|PacketPassage
	 *
	 */
	public function __invoke(Request $request): PacketPassage
	{
		$data = json_decode($request->getContent(), true);
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
		}

		if (!empty($positionData) && is_null($ip->getPosition())) {
			$longitude = $positionData['logitude'];
			$latitude = $positionData['latitude'];
			if (is_null($longitude) || is_null($latitude))
			{
				$position = new Position();
				$position->setLatitude($latitude);
				$position->setLongitude($longitude);
				$this->getDoctrine()->getManager()->persist($position);
			}
		}

		$packetPassage = new PacketPassage();
		$packetPassage->setIp($ip);
		$packetPassage->setIndice($indice);
		$packetPassage->setTraceRoute($traceRoute);
		$this->getDoctrine()->getManager()->persist($packetPassage);

		$this->getDoctrine()->getManager()->flush();

		return $packetPassage;
	}
}
