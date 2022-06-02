<?php

namespace App\Entity;

use App\Repository\FeedbackWorkerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FeedbackWorkerRepository::class)]
class FeedbackWorker
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'text')]
    private $text;

    #[ORM\Column(type: 'datetime')]
    private $dateAndTime;

    #[ORM\Column(type: 'integer')]
    private $estimation;

    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'feedbackWorkers')]
    #[ORM\JoinColumn(nullable: false)]
    private $client;

    #[ORM\ManyToOne(targetEntity: Worker::class, inversedBy: 'feedbackWorkers')]
    #[ORM\JoinColumn(nullable: false)]
    private $worker;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getDateAndTime(): ?\DateTimeInterface
    {
        return $this->dateAndTime;
    }

    public function setDateAndTime(\DateTimeInterface $dateAndTime): self
    {
        $this->dateAndTime = $dateAndTime;

        return $this;
    }

    public function getEstimation(): ?int
    {
        return $this->estimation;
    }

    public function setEstimation(int $estimation): self
    {
        $this->estimation = $estimation;

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

    public function getWorker(): ?Worker
    {
        return $this->worker;
    }

    public function setWorker(?Worker $worker): self
    {
        $this->worker = $worker;

        return $this;
    }
}
