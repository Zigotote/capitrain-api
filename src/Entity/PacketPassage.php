<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\PacketPassageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     itemOperations={
 *     	"get",
 * 	   },
 *     collectionOperations={
 *     	"post"={
 *              "method"="POST",
 *              "controller"=App\Controller\CreatePacketPassage::class,
 *              "defaults"={"_api_receive"=false}
 *      },
 *     	"get",
 *	   }
 * )
 * @ORM\Entity(repositoryClass=PacketPassageRepository::class)
 */
class PacketPassage
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
	 * @Groups({"get-traceroute"})
	 */
    private $id;

    /**
     * @ORM\Column(type="integer")
	 * @Groups({"get-traceroute"})
	 */
    private $indice;

    /**
     * @ORM\ManyToOne(targetEntity=Ip::class, inversedBy="packetPassages")
     * @ORM\JoinColumn(nullable=false)
	 * @Groups({"get-traceroute"})
	 */
    private $ip;

    /**
     * @ORM\ManyToOne(targetEntity=Traceroute::class, inversedBy="packetPassages")
     * @ORM\JoinColumn(nullable=false)
     */
    private $traceroute;

	/**
	 * @ORM\OneToOne(targetEntity=PacketPassage::class, inversedBy="previous")
	 * @ORM\JoinColumn(nullable=true)
	 */
	private $next;

	/**
	 * @ORM\OneToOne(targetEntity=PacketPassage::class, mappedBy="next")
	 * @ORM\JoinColumn(nullable=true)
	 */
	private $previous;

	public function getId(): ?int
    {
        return $this->id;
    }

    public function getIndice(): ?int
    {
        return $this->indice;
    }

    public function setIndice(int $indice): self
    {
        $this->indice = $indice;

        return $this;
    }

    public function getIp(): ?Ip
    {
        return $this->ip;
    }

    public function setIp(?Ip $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    public function getTraceroute(): ?Traceroute
    {
        return $this->traceroute;
    }

    public function setTraceroute(?Traceroute $traceroute): self
    {
        $this->traceroute = $traceroute;

        return $this;
    }

    public function getNext(): ?PacketPassage {
		return $this->next;
	}

	public function setNext(?PacketPassage $next): self {
		if(!is_null($this->next)) {
			$old = $this->next;
			$this->next = null;
			if(is_null($old->getPrevious())) {
				$old->setPrevious(null);
			}
		}
		$this->next = $next;

		return $this;
	}

	public function getPrevious(): ?PacketPassage {
		return $this->previous;
	}

	public function setPrevious(?PacketPassage $previous): self {
		if(!is_null($this->previous)) {
			$old = $this->previous;
			$this->previous = null;
			if(is_null($old->getNext())) {
				$old->setNext($previous);
			}
		}
		$this->previous = $previous;

		return $this;
	}
}
