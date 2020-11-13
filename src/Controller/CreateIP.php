<?php
// api/src/Controller/C                               .php

namespace App\Controller;

use App\Entity\Ip;
use App\Entity\PacketPassage;
use App\Entity\Position;
use phpDocumentor\Reflection\Types\This;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class CreateIP extends AbstractController
{
	public function __construct()
	{}

	/**
	 * @param Request $request
	 * @return JsonResponse|Ip
	 *
	 */
	public function __invoke(Request $request): Ip
	{
		$data = json_decode($request->getContent(), true);
		if(array_key_exists('ip', $data) && !empty($data['ip'])) {
			$ip = $this->getDoctrine()->getRepository('App:Ip')->findOneBy(['ip' => $data['ip']]);
			if(is_null($ip))
			{
				$ip = new Ip();
				$ip->setIp($data['ip']);
				$ip->setIsShared(true);
				$position = $this->getDoctrine()
								 ->getRepository("App:Position")
								 ->initPositionFormWhoIsAPI($ip);
				$ip->setPosition($position);

				$this->getDoctrine()
					 ->getManager()
					 ->persist($ip);
				$this->getDoctrine()
					 ->getManager()
					 ->flush();
			}
			return $ip;
		}
		return new JsonResponse(array('error' => 'ip_required'), Response::HTTP_BAD_REQUEST);
	}
}
