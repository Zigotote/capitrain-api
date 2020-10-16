<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\PacketPassageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=PacketPassageRepository::class)
 */
class PacketPassage
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $indice;

    /**
     * @ORM\ManyToOne(targetEntity=Ip::class, inversedBy="packetPassages")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ip;

    /**
     * @ORM\ManyToOne(targetEntity=Traceroute::class, inversedBy="packetPassages")
     * @ORM\JoinColumn(nullable=false)
     */
    private $traceroute;

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
}
