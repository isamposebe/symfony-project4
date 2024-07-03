<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Integer;

#[ORM\Entity(repositoryClass: GameRepository::class)]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;


    /** Количество голов правой команды
     * @var int
     */
    #[ORM\Column(type: 'integer', length: 255, nullable: false)]
    private int $goalsScoredRight = 0;

    /** Количество голов левой команды
     * @var int
     */
    #[ORM\Column(type: 'integer', length: 255, nullable: false)]
    private int $goalsScoredLeft = 0;

    /** Правая команда
     * @var Team|Null
     */
    #[ORM\ManyToOne(targetEntity: Team::class, inversedBy: 'games')]
    private Team|Null $teamRight;

    /** Левая команда
     * @var Team|Null
     */
    #[ORM\ManyToOne(targetEntity: Team::class, inversedBy: 'games')]
    private Team|Null $teamLeft;

    /** Тур игры
     * @var Tour
     */
    #[ORM\ManyToOne(targetEntity: Tour::class, inversedBy: 'games')]
    #[ORM\JoinColumn(nullable: false)]
    private Tour $tour;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGoalsScoredRight(): int
    {
        return $this->goalsScoredRight;
    }

    public function setGoalsScoredRight(int $goalsScoredRight): static
    {
        $this->goalsScoredRight = $goalsScoredRight;

        return $this;
    }

    public function getGoalsScoredLeft(): int
    {
        return $this->goalsScoredLeft;
    }

    public function setGoalsScoredLeft(int $goalsScoredLeft): static
    {
        $this->goalsScoredLeft = $goalsScoredLeft;

        return $this;
    }


    public function getTour(): Tour
    {
        return $this->tour;
    }

    public function setTour(Tour $tour): static
    {
        $this->tour = $tour;

        return $this;
    }

    public function getTeamRight(): ?Team
    {
        return $this->teamRight;
    }

    public function setTeamRight(?Team $teamRight): void
    {
        $this->teamRight = $teamRight;
    }

    public function getTeamLeft(): ?Team
    {
        return $this->teamLeft;
    }

    public function setTeamLeft(?Team $teamLeft): void
    {
        $this->teamLeft = $teamLeft;
    }
}
