<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WarehouseRepository")
 */
class Warehouse
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @ORM\Column(type="float")
     */
    private $cost;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Device", mappedBy="warehouse")
     */
    private $devices;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Sim", mappedBy="warehouse")
     */
    private $sims;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Office", cascade={"persist", "remove"})
     */
    private $office;

    public function __construct()
    {
        $this->devices = new ArrayCollection();
        $this->sims = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getCost(): ?float
    {
        return $this->cost;
    }

    public function setCost(float $cost): self
    {
        $this->cost = $cost;

        return $this;
    }

    /**
     * @return Collection|Device[]
     */
    public function getDevices(): Collection
    {
        return $this->devices;
    }

    public function addDevice(Device $device): self
    {
        if (!$this->devices->contains($device)) {
            $this->devices[] = $device;
            $device->setWarehouse($this);
        }

        return $this;
    }

    public function removeDevice(Device $device): self
    {
        if ($this->devices->contains($device)) {
            $this->devices->removeElement($device);
            // set the owning side to null (unless already changed)
            if ($device->getWarehouse() === $this) {
                $device->setWarehouse(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Sim[]
     */
    public function getSims(): Collection
    {
        return $this->sims;
    }

    public function addSim(Sim $sim): self
    {
        if (!$this->sims->contains($sim)) {
            $this->sims[] = $sim;
            $sim->setWarehouse($this);
        }

        return $this;
    }

    public function removeSim(Sim $sim): self
    {
        if ($this->sims->contains($sim)) {
            $this->sims->removeElement($sim);
            // set the owning side to null (unless already changed)
            if ($sim->getWarehouse() === $this) {
                $sim->setWarehouse(null);
            }
        }

        return $this;
    }

    public function getOffice(): ?Office
    {
        return $this->office;
    }

    public function setOffice(?Office $office): self
    {
        $this->office = $office;

        return $this;
    }

}
