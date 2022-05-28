<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Reservation::class)]
    private $reservations;

    #[ORM\OneToOne(mappedBy: 'client', targetEntity: User::class, cascade: ['persist', 'remove'])]
    private $userClient;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations[] = $reservation;
            $reservation->setClient($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getClient() === $this) {
                $reservation->setClient(null);
            }
        }

        return $this;
    }

    public function getUserClient(): ?User
    {
        return $this->userClient;
    }

    public function setUserClient(?User $userClient): self
    {
        // unset the owning side of the relation if necessary
        if ($userClient === null && $this->userClient !== null) {
            $this->userClient->setClient(null);
        }

        // set the owning side of the relation if necessary
        if ($userClient !== null && $userClient->getClient() !== $this) {
            $userClient->setClient($this);
        }

        $this->userClient = $userClient;

        return $this;
    }
}
