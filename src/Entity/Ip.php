<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\IpRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=IpRepository::class)
 */
class Ip
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=Position::class, cascade={"persist", "remove"})
     */
    private $position;

    /**
     * @ORM\Column(type="boolean")
     */
    private $shared;

    /**
     * @ORM\OneToMany(targetEntity=PacketPassage::class, mappedBy="ip")
     */
    private $packetPassages;

    public function __construct()
    {
        $this->packetPassages = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
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

    public function getShared(): ?bool
    {
        return $this->shared;
    }

    public function setShared(bool $shared): self
    {
        $this->shared = $shared;

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
