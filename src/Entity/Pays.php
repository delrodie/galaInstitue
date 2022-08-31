<?php

namespace App\Entity;

use App\Repository\PaysRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaysRepository::class)]
class Pays
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $code = null;

    #[ORM\Column(length: 2, nullable: true)]
    private ?string $alpha2 = null;

    #[ORM\Column(length: 3, nullable: true)]
    private ?string $alpha3 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nomEnGb = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nomFrFr = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?int
    {
        return $this->code;
    }

    public function setCode(?int $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getAlpha2(): ?string
    {
        return $this->alpha2;
    }

    public function setAlpha2(?string $alpha2): self
    {
        $this->alpha2 = $alpha2;

        return $this;
    }

    public function getAlpha3(): ?string
    {
        return $this->alpha3;
    }

    public function setAlpha3(?string $alpha3): self
    {
        $this->alpha3 = $alpha3;

        return $this;
    }

    public function getNomEnGb(): ?string
    {
        return $this->nomEnGb;
    }

    public function setNomEnGb(?string $nomEnGb): self
    {
        $this->nomEnGb = $nomEnGb;

        return $this;
    }

    public function getNomFrFr(): ?string
    {
        return $this->nomFrFr;
    }

    public function setNomFrFr(?string $nomFrFr): self
    {
        $this->nomFrFr = $nomFrFr;

        return $this;
    }
}
