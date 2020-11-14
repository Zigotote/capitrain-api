<?php
// api/src/Controller/GetPacketPassages.php

namespace App\Controller;

use App\Entity\PacketPassage;
use App\Entity\Position;
use App\Repository\PacketPassageRepository;
use App\Repository\TracerouteRepository;
use App\Services\GetNodeUseService;
use phpDocumentor\Reflection\Types\String_;
use phpDocumentor\Reflection\Types\This;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Return packetPassages as graph theory
 *
 * @example
 *         With this format :
 *         {
 *                nodes: [
 *                  countryD_regionD_D => {id: "countryD_regionD_D", label: "D", weight:4},
 * 					countryE_regionE_E => {id: "countryE_regionE_E", label: "E", weight:5},
 *					countryF_regionF_F => {id: "countryF_regionF_F", label: "F", weight:3},
 * 				  ],
 * 				  edges: [
 *					"countryD_regionD_D__TO__countryE_regionE_E" => {from: "countryD_regionD_D", to: "countryE_regionE_E", weight:2},
 * 					"countryE_regionE_E__TO__countryD_regionD_D" => {from: "countryE_regionE_E", to: "countryD_regionD_D", weight:1},
 * 					"countryE_regionE_E__TO__countryF_regionF_F" => {from: "countryE_regionE_E", to: "countryF_regionF_F", weight:1},
 *					"countryF_regionF_F__TO__countryD_regionD_D" => {from: "countryF_regionF_F", to: "countryD_regionD_D", weight:1},
 * 					"countryF_regionF_F__TO__countryE_regionE_E" => {from: "countryF_regionF_F", to: "countryE_regionE_E", weight:1},
 * 				  ]
 * 		 }
 *
 * @package App\Controller
 */
class GetPacketPassages extends AbstractController
{
	private $nodes = [];
	private $edges = [];

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
		$ppList = $this->getDoctrine()->getRepository('App:PacketPassage')->findAll();
		$this->nodes = [];
		$this->edges = [];

		/** @var PacketPassage $pp */
		foreach ($ppList as $pp) {
			if($this->havePosition($pp)
			   && !is_null($pp->getPrevious())
			   && $this->havePosition($pp->getPrevious())
			) {
				// Upd nodes weight
				$this->addNodePassage($pp->getPrevious()->getIp()->getPosition());
				$this->addNodePassage($pp->getIp()->getPosition());

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

	private function addNodePassage(Position $pos) {
		$key = $this->positionToKey($pos);
		if(!array_key_exists($key, $this->nodes)){
			$this->nodes[$key] = ["id" => $key,
								  "label" => $pos->getCity(),
								  "weight" => 0];
		}
		$this->nodes[$key]["weight"] ++;
	}

	private function addEdge(PacketPassage $pp)
	{
		$previousKey = $this->positionToKey($pp->getPrevious()->getIp()->getPosition());
		$currentKey = $this->positionToKey($pp->getIp()->getPosition());
		$key = $previousKey."__TO__".$currentKey;
		if(!array_key_exists($key, $this->edges)){
			$this->edges[$key] = ["from" => $previousKey,
								  "to" => $currentKey,
								  "weight" => 0,
								  "isp" => $pp->getPrevious()->getIp()->getIsp()];
		}
		$this->edges[$key]["weight"] ++;
	}

	/**
	 * Parse Position $pos to unique key
	 * @param Position $pos
	 *
	 * @return string
	 */
	private function positionToKey(Position $pos): string
	{
		return $pos->getCountry() . '_' . $pos->getRegion() . '_' . $pos->getCity();
	}
}
