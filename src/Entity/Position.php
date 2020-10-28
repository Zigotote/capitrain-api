<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\PositionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=PositionRepository::class)
 */
class Position
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
	 * @Groups({"get-traceroute"})
	 */
    private $id;

    /**
     * @ORM\Column(type="float")
	 * @Groups({"get-traceroute"})
	 */
    private $longitude;

    /**
     * @ORM\Column(type="float")
	 * @Groups({"get-traceroute"})
	 */
    private $latitude;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }
}
