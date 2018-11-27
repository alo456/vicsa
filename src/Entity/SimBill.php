<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SimBillRepository")
 */
class SimBill
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", unique=true)
     */
    private $docNumber;

    /**
     * @ORM\Column(type="datetimetz")
     */
    private $billDate;

    /**
     * @ORM\Column(type="string", length=4)
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
     * @ORM\OneToMany(targetEntity="App\Entity\Sim", mappedBy="simBill")
     */
    private $sims;

    public function __construct()
    {
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

    public function getBillDate(): ?\DateTimeInterface
    {
        return $this->billDate;
    }

    public function setBillDate(\DateTimeInterface $billDate): self
    {
        $this->billDate = $billDate;

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
            $sim->setSimBill($this);
        }

        return $this;
    }

    public function removeSim(Sim $sim): self
    {
        if ($this->sims->contains($sim)) {
            $this->sims->removeElement($sim);
            // set the owning side to null (unless already changed)
            if ($sim->getSimBill() === $this) {
                $sim->setSimBill(null);
            }
        }

        return $this;
    }
}
