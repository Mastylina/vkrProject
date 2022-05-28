<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'text')]
    private $textMessage;

    #[ORM\Column(type: 'datetime')]
    private $dateAndTime;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTextMessage(): ?string
    {
        return $this->textMessage;
    }

    public function setTextMessage(string $textMessage): self
    {
        $this->textMessage = $textMessage;

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
}
