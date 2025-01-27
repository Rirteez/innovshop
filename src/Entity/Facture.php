<?php

namespace App\Entity;

use App\Enum\StatusEnum;
use App\Repository\FactureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Event\PreUpdateEventArgs;

#[ORM\Entity(repositoryClass: FactureRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Facture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $date_facture = null;

    #[ORM\Column(enumType: StatusEnum::class)]
    #[Assert\NotNull(message: 'Le statut ne peut pas être nul.')]
    private ?StatusEnum $statut = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $total = null;

    /**
     * @var Collection<int, LigneFacture>
     */
    #[ORM\OneToMany(targetEntity: LigneFacture::class, mappedBy: 'id_facture', orphanRemoval: true)]
    private Collection $ligneFactures;

    #[ORM\ManyToOne(inversedBy: 'factures')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $id_client = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\Column(length: 510)]
    private ?string $adresse_livraison = null;

    public function __construct()
    {
        $this->ligneFactures = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateFacture(): ?\DateTimeImmutable
    {
        return $this->date_facture;
    }

    public function setDateFacture(\DateTimeImmutable $date_facture): static
    {
        $this->date_facture = $date_facture;

        return $this;
    }

    public function getStatut(): ?StatusEnum
    {
        return $this->statut;
    }

    public function setStatut(StatusEnum $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getTotal(): ?string
    {
        return $this->total;
    }

    public function setTotal(string $total): static
    {
        $this->total = $total;

        return $this;
    }

    /**
     * @return Collection<int, LigneFacture>
     */
    public function getLigneFactures(): Collection
    {
        return $this->ligneFactures;
    }

    public function addLigneFacture(LigneFacture $ligneFacture): static
    {
        if (!$this->ligneFactures->contains($ligneFacture)) {
            $this->ligneFactures->add($ligneFacture);
            $ligneFacture->setIdFacture($this);
        }

        return $this;
    }

    public function removeLigneFacture(LigneFacture $ligneFacture): static
    {
        if ($this->ligneFactures->removeElement($ligneFacture)) {
            // set the owning side to null (unless already changed)
            if ($ligneFacture->getIdFacture() === $this) {
                $ligneFacture->setIdFacture(null);
            }
        }

        return $this;
    }

    public function getIdClient(): ?Client
    {
        return $this->id_client;
    }

    public function setIdClient(?Client $id_client): static
    {
        $this->id_client = $id_client;

        return $this;
    }

    public function getItemCount(): int
    {
        $count = 0;

        foreach ($this->ligneFactures as $ligne) {
            $count += $ligne->getQuantity();
        }

        return $count;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    #[ORM\PreUpdate]
    public function updateTimestamp(PreUpdateEventArgs $event): void
    {
        if ($event->hasChangedField('statut')) {
            $this->updated_at = new \DateTimeImmutable();
        }
    }

    public function getAdresseLivraison(): ?string
    {
        return $this->adresse_livraison;
    }

    public function setAdresseLivraison(string $adresse_livraison): static
    {
        $this->adresse_livraison = $adresse_livraison;

        return $this;
    }
}

