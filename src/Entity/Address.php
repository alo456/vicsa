<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AddressRepository")
 */
class Address
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=40)
     */
    private $street;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $extNumber;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $inNumber;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $state;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $colony;

    /**
     * @ORM\Column(type="integer")
     */
    private $pc;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $township;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getExtNumber(): ?string
    {
        return $this->extNumber;
    }

    public function setExtNumber(string $extNumber): self
    {
        $this->extNumber = $extNumber;

        return $this;
    }

    public function getInNumber(): ?string
    {
        return $this->inNumber;
    }

    public function setInNumber(string $inNumber): self
    {
        $this->inNumber = $inNumber;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

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

    public function getColony(): ?string
    {
        return $this->colony;
    }

    public function setColony(string $colony): self
    {
        $this->colony = $colony;

        return $this;
    }

    public function getPc(): ?int
    {
        return $this->pc;
    }

    public function setPc(int $pc): self
    {
        $this->pc = $pc;

        return $this;
    }

    public function getTownship(): ?string
    {
        return $this->township;
    }

    public function setTownship(string $township): self
    {
        $this->township = $township;

        return $this;
    }
}
