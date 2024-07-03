<?php

namespace App\Entity;

use App\Repository\TourRepository;
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
    private Tournament $tournament;



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

    public function getTournament(): Tournament
    {
        return $this->tournament;
    }

    public function setTournament(Tournament $tournament): static
    {
        $this->tournament = $tournament;

        return $this;
    }


}
