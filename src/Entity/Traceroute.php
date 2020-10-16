<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\TracerouteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=TracerouteRepository::class)
 */
class Traceroute
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\OneToMany(targetEntity=PacketPassage::class, mappedBy="traceroute", orphanRemoval=true)
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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

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
            $packetPassage->setTraceroute($this);
        }

        return $this;
    }

    public function removePacketPassage(PacketPassage $packetPassage): self
    {
        if ($this->packetPassages->contains($packetPassage)) {
            $this->packetPassages->removeElement($packetPassage);
            // set the owning side to null (unless already changed)
            if ($packetPassage->getTraceroute() === $this) {
                $packetPassage->setTraceroute(null);
            }
        }

        return $this;
    }
}
