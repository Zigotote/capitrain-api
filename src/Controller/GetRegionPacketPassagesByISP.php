<?php
// api/src/Controller/GetRegionPacketPassagesByISP.php

namespace App\Controller;

use App\Entity\Ip;
use App\Entity\PacketPassage;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Return packetPassages as graph theory with a node for each city/isp found
 *
 * @example
 *         With this format :
 *         {
 *                nodes: [
 *                  D => {id: "D", city: "D", "isp": "ISPD", weight:4},
 * 					E => {id: "E", city: "E", "isp": "ISPE", weight:5},
 *					F => {id: "F", city: "F", "isp": "ISPF", weight:3},
 * 				  ],
 * 				  edges: [
 *					"D__TO__E" => {from: "D", to: "E", weight:2, isp: "Orange SA"},
 * 					"E__TO__D" => {from: "E", to: "D", weight:1, isp: "Orange SA"},
 * 					"E__TO__F" => {from: "E", to: "F", weight:1, isp: "Orange SA"},
 *					"F__TO__D" => {from: "F", to: "D", weight:1, isp: "Free"},
 * 					"F__TO__E" => {from: "F", to: "E", weight:1, isp: "Free"},
 * 				  ]
 * 		 }
 *
 * @package App\Controller
 */
class GetRegionPacketPassagesByISP extends AbstractController
{
	private $region;
	private $country;
	private $nodes = [];
	private $edges = [];

	public function __construct()
	{
	}

	public function __invoke(Request $request)
	{
		$data = json_decode($request->getContent(), true);
		// Verify request validity
		if(!array_key_exists('region', $data) || empty($data['region'])) {
			return new JsonResponse(array('error' => 'region_required'), Response::HTTP_BAD_REQUEST);
		}
		if(!array_key_exists('country', $data) || empty($data['country'])) {
			return new JsonResponse(array('error' => 'country_required'), Response::HTTP_BAD_REQUEST);
		}
		$this->region = $data["region"];
		$this->country = $data["country"];

		// TODO Change findAll to custom find to limit foreach duration
		$ppList = $this->getDoctrine()->getRepository('App:PacketPassage')->findAll();
		$this->nodes = [];
		$this->edges = [];

		/** @var PacketPassage $pp */
		foreach ($ppList as $pp) {
			if($this->havePosition($pp)
			   && !is_null($pp->getPrevious())
			   && $this->havePosition($pp->getPrevious())
			) {
				$edgeAllowed = $this->isInTargetRegion($pp) && $this->isInTargetRegion($pp->getPrevious());

				// Upd nodes weight
				$this->addNodePassage($pp->getPrevious()->getIp(), $edgeAllowed);
				$this->addNodePassage($pp->getIp(), $edgeAllowed);

				if($edgeAllowed)
				{
					// Upd edge weight
					$this->addEdge($pp);
				}
			}
		}

		return new JsonResponse(["nodes" => $this->nodes, "edges" => $this->edges]);
	}

	/**
	 * Return true if PacketPassage $pp have a know position
	 * @param PacketPassage $pp
	 *
	 * @return bool
	 */
	private function havePosition(PacketPassage $pp) {
		return !is_null($pp->getIp())
			   && !is_null($pp->getIp()->getPosition());
	}

	private function isInTargetRegion(PacketPassage $pp) {
		return $pp->getIp()->getPosition()->getRegion() == $this->region
			&& $pp->getIp()->getPosition()->getCountry() == $this->country;
	}

	private function addNodePassage(Ip $ip, $weightIncrementationAllowed) {
		$pos = $ip->getPosition();
		if($this->region == $pos->getRegion() && $this->country == $pos->getCountry())
		{
			$key = $this->ipToPositionKey($ip);
			if (!array_key_exists($key,
								  $this->nodes))
			{
				$this->nodes[$key] = [
					"id"     => $key,
					"city"  => $pos->getCity(),
					"isp"  => $this->ispToString($ip->getIsp()),
					"weight" => 0
				];
			}
			if($weightIncrementationAllowed)
			{
				$this->nodes[$key]["weight"]++;
			}
		}
	}

	private function addEdge(PacketPassage $pp)
	{
		$previousKey = $this->ipToPositionKey($pp->getPrevious()->getIp());
		$currentKey =  $this->ipToPositionKey($pp->getIp());
		$key = $previousKey."__TO__".$currentKey;
		if(!array_key_exists($key, $this->edges)){
			$this->edges[$key] = ["from" => $previousKey,
								  "to" => $currentKey,
								  "weight" => 0,
								  "isp" => $this->ispToString($pp->getPrevious()->getIp()->getIsp())];
		}
		$this->edges[$key]["weight"] ++;
	}

	private function ispToString($brutIsp) {
		return 		$isp = is_null($brutIsp) ? 'inconnu' : $brutIsp;
	}

	/**
	 * Parse Ip $ip to unique key
	 * @param Ip $ip
	 *
	 * @return string
	 */
	private function ipToPositionKey(Ip $ip): string
	{
		return $ip->getPosition()->getCity() . '_' . $this->ispToString($ip->getIsp());
	}
}
