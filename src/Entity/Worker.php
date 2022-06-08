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

    #[ORM\OneToMany(mappedBy: 'worker', targetEntity: Reservation::class, cascade: ['persist', 'remove'])]
    private $reservations;

    #[ORM\OneToOne(mappedBy: 'worker', targetEntity: User::class, cascade: ['persist', 'remove'])]
    private $userWorker;

    #[ORM\OneToMany(mappedBy: 'worker', targetEntity: FeedbackWorker::class, cascade: ['persist', 'remove'])]
    private $feedbackWorkers;

    #[ORM\OneToOne(mappedBy: 'worker', targetEntity: Kpi::class, cascade: ['persist', 'remove'])]
    private $kpi;

    #[ORM\OneToMany(mappedBy: 'worker', targetEntity: Diary::class, orphanRemoval: true)]
    private $diaries;




    public function __construct()
    {
        $this->services = new ArrayCollection();
        $this->reservations = new ArrayCollection();
        $this->feedbacks = new ArrayCollection();
        $this->feedbackWorkers = new ArrayCollection();
        $this->diaries = new ArrayCollection();
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

    /**
     * @return Collection<int, FeedbackWorker>
     */
    public function getFeedbackWorkers(): Collection
    {
        return $this->feedbackWorkers;
    }

    public function addFeedbackWorker(FeedbackWorker $feedbackWorker): self
    {
        if (!$this->feedbackWorkers->contains($feedbackWorker)) {
            $this->feedbackWorkers[] = $feedbackWorker;
            $feedbackWorker->setWorker($this);
        }

        return $this;
    }

    public function removeFeedbackWorker(FeedbackWorker $feedbackWorker): self
    {
        if ($this->feedbackWorkers->removeElement($feedbackWorker)) {
            // set the owning side to null (unless already changed)
            if ($feedbackWorker->getWorker() === $this) {
                $feedbackWorker->setWorker(null);
            }
        }

        return $this;
    }

    public function getKpi(): ?Kpi
    {
        return $this->kpi;
    }

    public function setKpi(Kpi $kpi): self
    {
        // set the owning side of the relation if necessary
        if ($kpi->getWorker() !== $this) {
            $kpi->setWorker($this);
        }

        $this->kpi = $kpi;

        return $this;
    }

    /**
     * @return Collection<int, Diary>
     */
    public function getDiaries(): Collection
    {
        return $this->diaries;
    }

    public function addDiary(Diary $diary): self
    {
        if (!$this->diaries->contains($diary)) {
            $this->diaries[] = $diary;
            $diary->setWorker($this);
        }

        return $this;
    }

    public function removeDiary(Diary $diary): self
    {
        if ($this->diaries->removeElement($diary)) {
            // set the owning side to null (unless already changed)
            if ($diary->getWorker() === $this) {
                $diary->setWorker(null);
            }
        }

        return $this;
    }

}
