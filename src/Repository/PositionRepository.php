<?php

namespace App\Repository;

use App\Entity\Ip;
use App\Entity\Position;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @method Position|null find($id, $lockMode = null, $lockVersion = null)
 * @method Position|null findOneBy(array $criteria, array $orderBy = null)
 * @method Position[]    findAll()
 * @method Position[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PositionRepository extends ServiceEntityRepository
{
	private $client;

	public function __construct(ManagerRegistry $registry, HttpClientInterface $client)
	{
		parent::__construct($registry, Position::class);
		$this->client = $client;
    }

    public function initPositionFormWhoIsAPI(Ip $ip) {
		$response = $this->client->request(
			'GET',
			'http://ipwhois.app/json/'.$ip->getIp().'?objects=success,country,city,latitude,longitude,isp'
		);
		$content = json_decode($response->getContent(), true);
		if($this->noPositionFound($content)) {
			return null;
		}

		$longitude = $content['longitude'];
		$latitude  = $content['latitude'];
		$country  = $content['country'];
		$city  = $content['city'];
		$isp  = $content['isp'];

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
			$position->setCountry($content['country']);
			$position->setCity($content['city']);

			$this->getEntityManager()
				 ->persist($position);
			$this->getEntityManager()
				 ->flush();

			return $position;
		} else {
			return null;
		}
	}

	public function getAllCities() {
		return $this
			->createQueryBuilder('pos')
			->select("pos.country")
			->addSelect("pos.city")
			->orderBy("pos.country", 'ASC')
			->addOrderBy("pos.city", 'ASC')
			->groupBy("pos.country")
			->addGroupBy("pos.city")
			->getQuery()
			->getResult();
	}

	/**
	 * @param $content
	 *
	 * @return bool
	 */
	private function noPositionFound($content)
	{
		return !array_key_exists('success',
								 $content)
			   || !$content['success']
			   || !array_key_exists('latitude', $content)
			   || is_null($content['latitude'])
			   || !array_key_exists('longitude', $content)
			   || is_null($content['longitude']);
}
}
