<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NoteRepository")
 */
class Note
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
    private $docNumber;

    /**
     * @ORM\Column(type="datetimetz")
     */
    private $noteDate;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $paymentTerm;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @ORM\Column(type="float")
     */
    private $discount;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Device", mappedBy="note")
     */
    private $devices;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Sim", mappedBy="note")
     */
    private $sims;

    public function __construct()
    {
        $this->devices = new ArrayCollection();
        $this->sims = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDocNumber(): ?int
    {
        return $this->docNumber;
    }

    public function setDocNumber(int $docNumber): self
    {
        $this->docNumber = $docNumber;

        return $this;
    }

    public function getNoteDate(): ?\DateTimeInterface
    {
        return $this->noteDate;
    }

    public function setNoteDate(\DateTimeInterface $noteDate): self
    {
        $this->noteDate = $noteDate;

        return $this;
    }

    public function getPaymentTerm(): ?string
    {
        return $this->paymentTerm;
    }

    public function setPaymentTerm(string $paymentTerm): self
    {
        $this->paymentTerm = $paymentTerm;

        return $this;
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

    public function getDiscount(): ?float
    {
        return $this->discount;
    }

    public function setDiscount(float $discount): self
    {
        $this->discount = $discount;

        return $this;
    }

    public function getDevice(): ?Activation
    {
        return $this->device;
    }

    public function setDevice(?Activation $device): self
    {
        $this->device = $device;

        // set (or unset) the owning side of the relation if necessary
        $newNote = $device === null ? null : $this;
        if ($newNote !== $device->getNote()) {
            $device->setNote($newNote);
        }

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
            $device->setNote($this);
        }

        return $this;
    }

    public function removeDevice(Device $device): self
    {
        if ($this->devices->contains($device)) {
            $this->devices->removeElement($device);
            // set the owning side to null (unless already changed)
            if ($device->getNote() === $this) {
                $device->setNote(null);
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
            $sim->setNote($this);
        }

        return $this;
    }

    public function removeSim(Sim $sim): self
    {
        if ($this->sims->contains($sim)) {
            $this->sims->removeElement($sim);
            // set the owning side to null (unless already changed)
            if ($sim->getNote() === $this) {
                $sim->setNote(null);
            }
        }

        return $this;
    }
}
