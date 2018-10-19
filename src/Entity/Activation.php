<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ActivationRepository")
 */
class Activation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $lineNumber;

    /**
     * @ORM\Column(type="datetimetz")
     */
    private $actDate;

    /**
     * @ORM\Column(type="string", length=2)
     */
    private $status;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Note", cascade={"persist", "remove"})
     */
    private $note;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Device", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $device;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Sim", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $sim;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Contract", mappedBy="activation", cascade={"persist", "remove"})
     */
    private $contract;


    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLineNumber(): ?string
    {
        return $this->lineNumber;
    }

    public function setLineNumber(string $lineNumber): self
    {
        $this->lineNumber = $lineNumber;

        return $this;
    }

    public function getActDate(): ?\DateTimeInterface
    {
        return $this->actDate;
    }

    public function setActDate(\DateTimeInterface $actDate): self
    {
        $this->actDate = $actDate;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

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

    public function getDevice(): ?Device
    {
        return $this->device;
    }

    public function setDevice(Device $device): self
    {
        $this->device = $device;

        return $this;
    }

    public function getSim(): ?Sim
    {
        return $this->sim;
    }

    public function setSim(Sim $sim): self
    {
        $this->sim = $sim;

        return $this;
    }

    public function getContract(): ?Contract
    {
        return $this->contract;
    }

    public function setContract(Contract $contract): self
    {
        $this->contract = $contract;

        // set the owning side of the relation if necessary
        if ($this !== $contract->getActivation()) {
            $contract->setActivation($this);
        }

        return $this;
    }
}
