<?php

namespace App\Entity;

use App\Repository\KpiRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: KpiRepository::class)]
class Kpi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'float')]
    private $weightVolumeSales;

    #[ORM\Column(type: 'float')]
    private $minVolumeSales;

    #[ORM\Column(type: 'float')]
    private $factVolumeSales;

    #[ORM\Column(type: 'float')]
    private $plannedVolumeSales;

    #[ORM\Column(type: 'float')]
    private $weightQualityService;

    #[ORM\Column(type: 'float')]
    private $minQualityService;

    #[ORM\Column(type: 'float')]
    private $factQualityService;

    #[ORM\Column(type: 'float')]
    private $plannedQualityService;

    #[ORM\OneToOne(inversedBy: 'kpi', targetEntity: Worker::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private $worker;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWeightVolumeSales(): ?float
    {
        return $this->weightVolumeSales;
    }

    public function setWeightVolumeSales(float $weightVolumeSales): self
    {
        $this->weightVolumeSales = $weightVolumeSales;

        return $this;
    }

    public function getMinVolumeSales(): ?float
    {
        return $this->minVolumeSales;
    }

    public function setMinVolumeSales(float $minVolumeSales): self
    {
        $this->minVolumeSales = $minVolumeSales;

        return $this;
    }

    public function getFactVolumeSales(): ?float
    {
        return $this->factVolumeSales;
    }

    public function setFactVolumeSales(float $factVolumeSales): self
    {
        $this->factVolumeSales = $factVolumeSales;

        return $this;
    }

    public function getPlannedVolumeSales(): ?float
    {
        return $this->plannedVolumeSales;
    }

    public function setPlannedVolumeSales(float $plannedVolumeSales): self
    {
        $this->plannedVolumeSales = $plannedVolumeSales;

        return $this;
    }

    public function getWeightQualityService(): ?float
    {
        return $this->weightQualityService;
    }

    public function setWeightQualityService(float $weightQualityService): self
    {
        $this->weightQualityService = $weightQualityService;

        return $this;
    }

    public function getMinQualityService(): ?float
    {
        return $this->minQualityService;
    }

    public function setMinQualityService(float $minQualityService): self
    {
        $this->minQualityService = $minQualityService;

        return $this;
    }

    public function getFactQualityService(): ?float
    {
        return $this->factQualityService;
    }

    public function setFactQualityService(float $factQualityService): self
    {
        $this->factQualityService = $factQualityService;

        return $this;
    }

    public function getPlannedQualityService(): ?float
    {
        return $this->plannedQualityService;
    }

    public function setPlannedQualityService(float $plannedQualityService): self
    {
        $this->plannedQualityService = $plannedQualityService;

        return $this;
    }

    public function getWorker(): ?Worker
    {
        return $this->worker;
    }

    public function setWorker(Worker $worker): self
    {
        $this->worker = $worker;

        return $this;
    }
}
