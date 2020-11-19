<?php
// api/src/Controller/GetPacketPassagesByISP.php

namespace App\Controller;

use App\Entity\Ip;
use App\Entity\PacketPassage;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Return packetPassages as graph theory (but nodes and edges depends on ISP)
 *
 * @example
 *         With this format :
 *         {
 *                nodes: [
 *                  countryD_regionD_cityD_ISPD => {id: "countryD_regionD_cityD_ISPD", "city": "D", "isp": ISPD", weight:4},
 * 					countryE_regionE_cityE_ISPE => {id: "countryE_regionE_cityE_ISPE", "city": "E", "isp": ISPE", weight:5},
 *					countryF_regionF_cityF_ISPF => {id: "countryF_regionF_cityF_ISPF", "city": "F", "isp": ISPF", weight:3},
 * 				  ],
 * 				  edges: [
 *					"countryD_regionD_cityD_ISPD__TO__countryE_regionE_cityE_ISPE" => {from: "countryD_regionD_cityD_ISPD", to: "countryE_regionE_cityE_ISPE", weight:2},
 * 					"countryE_regionE_cityE_ISPE__TO__countryD_regionD_cityD_ISPD" => {from: "countryE_regionE_cityE_ISPE", to: "countryD_regionD_cityD_ISPD", weight:1},
 * 					"countryE_regionE_cityE_ISPE__TO__countryF_regionF_cityF_ISPF" => {from: "countryE_regionE_cityE_ISPE", to: "countryF_regionF_cityF_ISPF", weight:1},
 *					"countryF_regionF_cityF_ISPF__TO__countryD_regionD_cityD_ISPD" => {from: "countryF_regionF_cityF_ISPF", to: "countryD_regionD_cityD_ISPD", weight:1},
 * 					"countryF_regionF_cityF_ISPF__TO__countryE_regionE_cityE_ISPE" => {from: "countryF_regionF_cityF_ISPF", to: "countryE_regionE_cityE_ISPE", weight:1},
 * 				  ]
 * 		 }
 *       So "Paris Orange" will be a different node than "Paris SFR" or "Paris Free"
 *
 * @package App\Controller
 */
class GetPacketPassagesByISP extends AbstractController
{
	private $nodes = [];
	private $edges = [];

	/**
	 * GetAllNodeUse constructor.
	 */
	public function __construct()
	{
	}

	public function __invoke(Request $request)
	{
		$ppList = $this->getDoctrine()->getRepository('App:PacketPassage')
									  ->findAllPacketWithPreviousAndPosition();
		$this->nodes = [];
		$this->edges = [];

		/** @var PacketPassage $pp */
		foreach ($ppList as $pp) {
			if(!is_null($pp->getPrevious())
			   && $this->havePosition($pp->getPrevious())
			) {
				// Upd nodes weight
				$this->addNodePassage($pp->getPrevious()->getIp());
				$this->addNodePassage($pp->getIp());

				// Upd edge weight
				$this->addEdge($pp);
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

	private function addNodePassage(Ip $ip) {
		$pos = $ip->getPosition();
		$key = $this->ipToPositionKey($ip);
		if(!array_key_exists($key, $this->nodes)){
			$this->nodes[$key] = ["id" => $key,
								  "city" => $pos->getCity(),
								  "isp" => $this->ispToString($ip->getIsp()),
								  "weight" => 0];
		}
		$this->nodes[$key]["weight"] ++;
	}

	private function addEdge(PacketPassage $pp)
	{
		$previousKey = $this->ipToPositionKey($pp->getPrevious()->getIp());
		$currentKey = $this->ipToPositionKey($pp->getIp());
		$key = $previousKey."__TO__".$currentKey;
		if(!array_key_exists($key, $this->edges)){
			$this->edges[$key] = ["from" => $previousKey,
								  "to" => $currentKey,
								  "weight" => 0,
								  "isp" => $this->ispToString($pp->getPrevious()->getIp()->getIsp())];
		}
		$this->edges[$key]["weight"] ++;
	}

	/**
	 * Parse Ip $ip to unique key
	 * @param Ip $ip
	 *
	 * @return string
	 */
	private function ipToPositionKey(Ip $ip): string
	{
		$pos = $ip->getPosition();
		return $pos->getCountry() . '_' . $pos->getRegion() . '_' . $pos->getCity() . '_' . $this->ispToString($ip->getIsp());
	}

	private function ispToString($brutIsp) {
		return 		$isp = is_null($brutIsp) ? 'inconnu' : $brutIsp;
	}
}
