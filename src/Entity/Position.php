<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\PositionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     collectionOperations={
 *        "getAllNodeUse" = {
 *  	        "method"="GET",
 *     			"path"="positions/all_node_use",
 *              "controller"=App\Controller\GetAllNodeUse::class,
 *              "defaults"={"_api_receive"=false}
 *	      },
 *       "getCities" = {
 *  	        "method"="GET",
 *     			"path"="positions/cities",
 *              "controller"=App\Controller\GetCities::class,
 *              "defaults"={"_api_receive"=false}
 *	      },
 *     },
 * )
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

	/**
	 * @ORM\Column(type="string")
	 * @Groups({"get-traceroute"})
	 */
	private $country;

	/**
	 * @ORM\Column(type="string")
	 * @Groups({"get-traceroute"})
	 */
	private $city;

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

	public function getCountry(): ?string
	{
		return $this->country;
	}

	public function setCountry(string $country): self
	{
		$this->country = $country;

		return $this;
	}

	public function getCity(): ?string
	{
		return $this->city;
	}

	public function setCity(string $city): self
	{
		$this->city = $city;

		return $this;
	}
}
