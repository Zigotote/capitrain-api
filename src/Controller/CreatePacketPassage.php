<?php
// api/src/Controller/CreatePacketPassagePublication.php

namespace App\Controller;

use App\Entity\Ip;
use App\Entity\PacketPassage;
use App\Entity\Position;
use App\Repository\IpRepository;
use App\Repository\TracerouteRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
	 * @Route(
	 *     name="packet_passage_add",
	 *     path="/PacketPassage/add",
	 *     methods={"POST"},
	 *     defaults={
	 *         "_api_resource_class"=PacketPassage::class,
	 *         "_api_item_operation_name"="add"
	 *     }
	 * )
	 */
	public function __invoke(Request $request): PacketPassage
	{
		$data = json_decode($request->getContent(), true);
		$ipValue = $data['ip'];
		$indice = $data['indice'];
		$traceRouteId = $data['traceroute'];
		$positionData = $data['position'];

		// Verify request validity
		if(empty($ipValue)) {
			return new JsonResponse(array('error' => 'ip_required'), Response::HTTP_BAD_REQUEST);
		}
		if(is_null($traceRouteId)) {
			return new JsonResponse(array('error' => 'traceRoute_required'), Response::HTTP_BAD_REQUEST);
		}
		$traceRoute = (new TracerouteRepository)->findOneBy(['id' => $traceRouteId]);
		if (is_null($traceRoute)) {
			return new JsonResponse(array('error' => 'traceRoute_unknown'), Response::HTTP_BAD_REQUEST);
		}

		// Update and create elements
		$ip = (new IpRepository)->findOneBy(['id' => $ipValue]);
		if(is_null($ip)) {
			$ip = new Ip($ipValue);
			$this->getDoctrine()->getManager()->persist($ip);
		}

		if (is_null($ip->getPosition())) {
			$longitude = $positionData['logitude'];
			$latitude = $positionData['latitude'];
			if (is_null($longitude) || is_null($latitude))
			{
				$position = new Position();
				$position->setLatitude($latitude);
				$position->setLongitude($longitude);
				$position->setIp($ip);
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
