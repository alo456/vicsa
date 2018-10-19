<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SimRepository")
 */
class Sim
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=19)
     */
    private $iccid;

    /**
     * @ORM\Column(type="integer")
     */
    private $matKey;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\Column(type="datetimetz")
     */
    private $entryDate;

    /**
     * @ORM\Column(type="datetimetz", nullable=true)
     */
    private $exitDate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Warehouse", inversedBy="sims")
     */
    private $warehouse;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SimBill", inversedBy="sims")
     * @ORM\JoinColumn(nullable=false)
     */
    private $simBill;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Note", inversedBy="sims")
     */
    private $note;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIccid(): ?string
    {
        return $this->iccid;
    }

    public function setIccid(string $iccid): self
    {
        $this->iccid = $iccid;

        return $this;
    }

    public function getMatKey(): ?int
    {
        return $this->matKey;
    }

    public function setMatKey(int $matKey): self
    {
        $this->matKey = $matKey;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getEntryDate(): ?\DateTimeInterface
    {
        return $this->entryDate;
    }

    public function setEntryDate(\DateTimeInterface $entryDate): self
    {
        $this->entryDate = $entryDate;

        return $this;
    }

    public function getExitDate(): ?\DateTimeInterface
    {
        return $this->exitDate;
    }

    public function setExitDate(?\DateTimeInterface $exitDate): self
    {
        $this->exitDate = $exitDate;

        return $this;
    }

    public function getWarehouse(): ?Warehouse
    {
        return $this->warehouse;
    }

    public function setWarehouse(?Warehouse $warehouse): self
    {
        $this->warehouse = $warehouse;

        return $this;
    }

    public function getSimBill(): ?SimBill
    {
        return $this->simBill;
    }

    public function setSimBill(?SimBill $simBill): self
    {
        $this->simBill = $simBill;

        return $this;
    }

    public function getNote(): ?Note
    {
        return $this->note;
    }

    public function setNote(?Note $note): self
    {
        $this->note = $note;

        return $this;
    }
}
