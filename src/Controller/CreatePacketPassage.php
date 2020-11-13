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
	 * 				"ip"		: "8.8.8.2",
	 * 				"indice"	: 42,
	 * 				"traceroute": 1
	 *          }
	 *
	 * "traceroute" attribute have to be an existing Traceroute id
	 * All attributes required (except "isp")
	 *
	 * @param Request $request
	 * @return JsonResponse|PacketPassage
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
			$position = $this->getDoctrine()
							 ->getRepository('App:Position')
							 ->initPositionFormWhoIsAPI($ip);

			if (!is_null($position))
			{
				$ip->setPosition($position);
				$this->getDoctrine()->getManager()->persist($ip);
			}
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
