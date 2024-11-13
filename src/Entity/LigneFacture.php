<?php

namespace App\Entity;

use App\Repository\LigneFactureRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LigneFactureRepository::class)]
class LigneFacture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'ligneFactures')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Facture $id_facture = null;

    #[ORM\ManyToOne(inversedBy: 'ligneFactures')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Article $id_article = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $total_ligne = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdFacture(): ?Facture
    {
        return $this->id_facture;
    }

    public function setIdFacture(?Facture $id_facture): static
    {
        $this->id_facture = $id_facture;

        return $this;
    }

    public function getIdArticle(): ?Article
    {
        return $this->id_article;
    }

    public function setIdArticle(?Article $id_article): static
    {
        $this->id_article = $id_article;

        return $this;
    }

    public function getTotalLigne(): ?string
    {
        return $this->total_ligne;
    }

    public function setTotalLigne(string $total_ligne): static
    {
        $this->total_ligne = $total_ligne;

        return $this;
    }
}
