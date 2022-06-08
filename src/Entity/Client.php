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

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Feedback::class)]
    private $feedbacks;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: FeedbackWorker::class)]
    private $feedbackWorkers;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Diary::class, orphanRemoval: true)]
    private $diaries;


    public function __construct()
    {
        $this->reservations = new ArrayCollection();
        $this->feedbacks = new ArrayCollection();
        $this->feedbackWorkers = new ArrayCollection();
        $this->diaries = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->id;
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

    /**
     * @return Collection<int, Feedback>
     */
    public function getFeedbacks(): Collection
    {
        return $this->feedbacks;
    }

    public function addFeedback(Feedback $feedback): self
    {
        if (!$this->feedbacks->contains($feedback)) {
            $this->feedbacks[] = $feedback;
            $feedback->setClient($this);
        }

        return $this;
    }

    public function removeFeedback(Feedback $feedback): self
    {
        if ($this->feedbacks->removeElement($feedback)) {
            // set the owning side to null (unless already changed)
            if ($feedback->getClient() === $this) {
                $feedback->setClient(null);
            }
        }

        return $this;
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
            $feedbackWorker->setClient($this);
        }

        return $this;
    }

    public function removeFeedbackWorker(FeedbackWorker $feedbackWorker): self
    {
        if ($this->feedbackWorkers->removeElement($feedbackWorker)) {
            // set the owning side to null (unless already changed)
            if ($feedbackWorker->getClient() === $this) {
                $feedbackWorker->setClient(null);
            }
        }

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
            $diary->setClient($this);
        }

        return $this;
    }

    public function removeDiary(Diary $diary): self
    {
        if ($this->diaries->removeElement($diary)) {
            // set the owning side to null (unless already changed)
            if ($diary->getClient() === $this) {
                $diary->setClient(null);
            }
        }

        return $this;
    }

}
