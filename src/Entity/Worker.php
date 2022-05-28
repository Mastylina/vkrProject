<?php

namespace App\Entity;

use App\Repository\WorkerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorkerRepository::class)]
class Worker
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $photo;

    #[ORM\ManyToOne(targetEntity: Post::class, inversedBy: 'workers')]
    #[ORM\JoinColumn(nullable: false)]
    private $post;

    #[ORM\ManyToMany(targetEntity: Service::class, inversedBy: 'workers')]
    private $services;

    #[ORM\OneToMany(mappedBy: 'worker', targetEntity: Reservation::class)]
    private $reservations;

    #[ORM\OneToOne(mappedBy: 'worker', targetEntity: User::class, cascade: ['persist', 'remove'])]
    private $userWorker;

    public function __construct()
    {
        $this->services = new ArrayCollection();
        $this->reservations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
    {
        $this->post = $post;

        return $this;
    }

    /**
     * @return Collection<int, Service>
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(Service $service): self
    {
        if (!$this->services->contains($service)) {
            $this->services[] = $service;
        }

        return $this;
    }

    public function removeService(Service $service): self
    {
        $this->services->removeElement($service);

        return $this;
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
            $reservation->setWorker($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getWorker() === $this) {
                $reservation->setWorker(null);
            }
        }

        return $this;
    }

    public function getUserWorker(): ?User
    {
        return $this->userWorker;
    }

    public function setUserWorker(?User $userWorker): self
    {
        // unset the owning side of the relation if necessary
        if ($userWorker === null && $this->userWorker !== null) {
            $this->userWorker->setWorker(null);
        }

        // set the owning side of the relation if necessary
        if ($userWorker !== null && $userWorker->getWorker() !== $this) {
            $userWorker->setWorker($this);
        }

        $this->userWorker = $userWorker;

        return $this;
    }
    public function __toString(): string
    {
        return $this->userWorker->getName() ;
    }
}
