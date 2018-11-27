<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DeviceRepository")
 */
class Device
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=15, unique=true)
     */
    private $imei;

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
     * @ORM\ManyToOne(targetEntity="App\Entity\Warehouse", inversedBy="devices")
     */
    private $warehouse;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\DeviceBill", inversedBy="devices")
     * @ORM\JoinColumn(nullable=false)
     */
    private $deviceBill;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Note", inversedBy="devices")
     */
    private $note;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImei(): ?string
    {
        return $this->imei;
    }

    public function setImei(string $imei): self
    {
        $this->imei = $imei;

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

    public function getDeviceBill(): ?DeviceBill
    {
        return $this->deviceBill;
    }

    public function setDeviceBill(?DeviceBill $deviceBill): self
    {
        $this->deviceBill = $deviceBill;

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
