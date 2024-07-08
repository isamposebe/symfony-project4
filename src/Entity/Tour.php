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
     * @var int
     */
    #[ORM\Column(type: 'integer', length: 255, nullable: false)]
    private int $num;

    /** Данные об турнире
     * @var Tournament
     */
    #[ORM\ManyToOne(targetEntity: Tournament::class, inversedBy: 'tours')]
    private Tournament $tournament;



    public function getId(): int
    {
        return $this->id;
    }

    public function getNum(): string
    {
        return $this->num;
    }

    public function setNum(string $num): static
    {
        $this->num = $num;

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
