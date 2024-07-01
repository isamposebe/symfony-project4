<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameRepository::class)]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $goalsScoredRight = null;

    #[ORM\Column]
    private ?int $goalsScoredLeft = null;

    #[ORM\ManyToOne(inversedBy: 'games')]
    private ?Team $teamRight = null;

    #[ORM\ManyToOne(inversedBy: 'games')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Team $teamLeft = null;

    #[ORM\ManyToOne(inversedBy: 'games')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Tour $tour = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGoalsScoredRight(): ?int
    {
        return $this->goalsScoredRight;
    }

    public function setGoalsScoredRight(int $goalsScoredRight): static
    {
        $this->goalsScoredRight = $goalsScoredRight;

        return $this;
    }

    public function getGoalsScoredLeft(): ?int
    {
        return $this->goalsScoredLeft;
    }

    public function setGoalsScoredLeft(int $goalsScoredLeft): static
    {
        $this->goalsScoredLeft = $goalsScoredLeft;

        return $this;
    }

    public function getTeamRight(): ?Team
    {
        return $this->teamRight;
    }

    public function setTeamRight(?Team $teamRight): static
    {
        $this->teamRight = $teamRight;

        return $this;
    }

    public function getTeamLeft(): ?Team
    {
        return $this->teamLeft;
    }

    public function setTeamLeft(?Team $teamLeft): static
    {
        $this->teamLeft = $teamLeft;

        return $this;
    }

    public function getTour(): ?Tour
    {
        return $this->tour;
    }

    public function setTour(?Tour $tour): static
    {
        $this->tour = $tour;

        return $this;
    }
}
