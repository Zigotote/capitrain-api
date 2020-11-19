<?php

namespace App\Repository;

use App\Entity\PacketPassage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PacketPassage|null find($id, $lockMode = null, $lockVersion = null)
 * @method PacketPassage|null findOneBy(array $criteria, array $orderBy = null)
 * @method PacketPassage[]    findAll()
 * @method PacketPassage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PacketPassageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PacketPassage::class);
    }

    public function findAllOrderByPosition() {
		return $this
			->createQueryBuilder('pp')
			->select("pos.country")
			->addSelect("pos.region")
			->addSelect("pos.city")
			->join('pp.ip', 'ip')
			->join('ip.position', 'pos')
			->where('ip.position IS NOT NULL')
			->addOrderBy('pos.country', 'ASC')
			->addOrderBy('pos.region', 'ASC')
			->addOrderBy('pos.city', 'ASC')
			->getQuery()
			->getResult();
	}

	public function findPositionReuse() {
		$qb = $this->createQueryBuilder('pp');
    	return $qb->select("pos.country")
				  ->addSelect("pos.region")
				  ->addSelect("pos.city")
				  ->addSelect("pos.city")
				  ->addSelect($qb->expr()->count('pos.city').' as nb')
				  ->join('pp.ip', 'ip')
				  ->join('ip.position', 'pos')
				  ->where('ip.position IS NOT NULL')
				  ->groupBy('pos.country')
				  ->addGroupBy('pos.region')
				  ->addGroupBy('pos.city')
				  ->getQuery()
				  ->getResult();
	}

	public function findAllPacketWithPreviousAndPosition() {
		return $this
			->createQueryBuilder('pp')
			->join('pp.ip', 'ip')
			->join('ip.position', 'pos')
			->where('ip.position IS NOT NULL')
			->andWhere('pp.previous IS NOT NULL')
			->getQuery()
			->getResult();
	}

	// TODO: not tested yet
	public function findAllExchangeISPPosition() {
		return $this
			->createQueryBuilder('pp')
			->select("pos.country")
			->addSelect("pos.region")
			->addSelect("pos.city")
			->join('pp.ip', 'ip')
			->join('ip.position', 'pos')
			->where('ip.position IS NOT NULL')
			->andWhere('ip.is_ispexchange = true')
			->addOrderBy('pos.country', 'ASC')
			->addOrderBy('pos.city', 'ASC')
			->getQuery()
			->getResult();
	}

    // /**
    //  * @return PacketPassage[] Returns an array of PacketPassage objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PacketPassage
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
