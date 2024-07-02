<?php

namespace App\Entity;

use App\Repository\TourRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TourRepository::class)]
class Tour
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    /** Название тура
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    private string $name;

    /** Данные об турнире
     * @var Tournament
     */
    #[ORM\ManyToOne(targetEntity: Tournament::class, inversedBy: 'tours')]
    private Tournament $tornament;

    /** Игра для турнира
     * @var Game
     */
    #[ORM\OneToMany(targetEntity: Game::class, mappedBy: 'tours')]
    private Game $games;

    public function __construct()
    {
        $this->games = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getTornament(): ?Tournament
    {
        return $this->tornament;
    }

    public function setTornament(?Tournament $tornament): static
    {
        $this->tornament = $tornament;

        return $this;
    }

    /**
     * @return Collection<int, Game>
     */
    public function getGames(): Collection
    {
        return $this->games;
    }

    public function addGame(Game $game): static
    {
        if (!$this->games->contains($game)) {
            $this->games->add($game);
            $game->setTour($this);
        }

        return $this;
    }

    public function removeGame(Game $game): static
    {
        if ($this->games->removeElement($game)) {
            // set the owning side to null (unless already changed)
            if ($game->getTour() === $this) {
                $game->setTour(null);
            }
        }

        return $this;
    }

    public function setGames(Game $games): void
    {
        $this->games = $games;
    }
}
