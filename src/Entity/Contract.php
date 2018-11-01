<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ContractRepository")
 */
class Contract
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $accountNumber;

    /**
     * @ORM\Column(type="integer")
     */
    private $deadlines;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $planName;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Client", inversedBy="contracts")
     */
    private $client;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Employee", inversedBy="contracts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $employee;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Activation", inversedBy="contract", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $activation;

    /**
     * @ORM\Column(type="float")
     */
    private $totalPrice;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAccountNumber(): ?string
    {
        return $this->accountNumber;
    }

    public function setAccountNumber(string $accountNumber): self
    {
        $this->accountNumber = $accountNumber;

        return $this;
    }

    public function getDeadlines(): ?int
    {
        return $this->deadlines;
    }

    public function setDeadlines(int $deadlines): self
    {
        $this->deadlines = $deadlines;

        return $this;
    }

    public function getPlanName(): ?string
    {
        return $this->planName;
    }

    public function setPlanName(string $planName): self
    {
        $this->planName = $planName;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getEmployee(): ?Employee
    {
        return $this->employee;
    }

    public function setEmployee(?Employee $employee): self
    {
        $this->employee = $employee;

        return $this;
    }

    public function getActivation(): ?Activation
    {
        return $this->activation;
    }

    public function setActivation(Activation $activation): self
    {
        $this->activation = $activation;

        return $this;
    }

    public function getTotalPrice(): ?float
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(float $totalPrice): self
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }
}
