<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\IpRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={"get-ip"}},
 *     collectionOperations={
 *         "get"={
 *               "normalization_context"={"groups"={"get-ip"}}
 *         },
 *        "post"={
 *              "method"="POST",
 *              "controller"=App\Controller\CreateIP::class,
 *              "defaults"={"_api_receive"=false}
 *        },
 *     },
 * )
 * @ORM\Entity(repositoryClass=IpRepository::class)
 */
class Ip
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
	 * @Groups({"get-ip", "get-traceroute"})
	 */
    private $id;

    /**
     * @ORM\Column(type="boolean")
	 * @Groups({"get-ip"})
	 */
    private $isShared;

    /**
     * @ORM\OneToOne(targetEntity=Position::class, cascade={"persist", "remove"})
	 * @Groups({"get-ip", "get-traceroute"})
	 */
    private $position;

    /**
     * @ORM\Column(type="string", length=30)
	 * @Groups({"get-ip", "get-traceroute"})
	 */
    private $ip;

    /**
     * @ORM\OneToMany(targetEntity=PacketPassage::class, mappedBy="ip", orphanRemoval=true)
     */
    private $packetPassages;

    public function __construct()
    {
        $this->packetPassages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIsShared(): ?bool
    {
        return $this->isShared;
    }

    public function setIsShared(bool $isShared): self
    {
        $this->isShared = $isShared;

        return $this;
    }

    public function getPosition(): ?Position
    {
        return $this->position;
    }

    public function setPosition(?Position $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * @return Collection|PacketPassage[]
     */
    public function getPacketPassages(): Collection
    {
        return $this->packetPassages;
    }

    public function addPacketPassage(PacketPassage $packetPassage): self
    {
        if (!$this->packetPassages->contains($packetPassage)) {
            $this->packetPassages[] = $packetPassage;
            $packetPassage->setIp($this);
        }

        return $this;
    }

    public function removePacketPassage(PacketPassage $packetPassage): self
    {
        if ($this->packetPassages->contains($packetPassage)) {
            $this->packetPassages->removeElement($packetPassage);
            // set the owning side to null (unless already changed)
            if ($packetPassage->getIp() === $this) {
                $packetPassage->setIp(null);
            }
        }

        return $this;
    }
}
