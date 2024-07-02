<?php

namespace App\Service;

use App\Entity\Game;
use App\Entity\Team;
use App\Entity\Tour;
use App\Entity\Tournament;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;


class TournamentService
{
    /** Менеджер сущностей
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ){}

    /** Проверка на идентичность (Проверяется Name)
     * - Поиск элемента по его классу (Team, Tournament, Tour, User)
     * @param $item - Данные элемента
     * @return bool Если есть совпадения, то false, иначе true
     */
    public function identityVerificationName($item):bool
    {
        $list = $this->entityManager->getRepository($item::class)->findAll();
        foreach ($list as $t) {
            if ($t->getName() == $item->getName()){
                return false;
            }
        }
        return true;
    }

    /** Удаление элемента из базы данных
     * @param Team $item Данные элемента
     * @return void
     */
    function deleteItem($item):void
    {
        $this->entityManager->remove($item);
        $this->entityManager->flush();
    }

    /** Добавляет элемент в базу данных
     * @param Team $item Данные элемента
     * @return void
     */
    function addItem($item):void
    {
        $this->entityManager->persist($item);
        $this->entityManager->flush();
    }

    /** Поиск турнира по ID
     * @param int $id ID турнира
     * @return Tournament Получаем турнир
     */
    public function searchTournamentID(int $id): Tournament
    {
        return $this->entityManager->getRepository(Tournament::class)->find($id);
    }

    /** Добавление команды в турнир
     * @param Team $team Данные команды
     * @param Tournament $tournament Данные турнира
     * @return void
     */
    public function addTeamTournament(Team $team, Tournament $tournament): Response
    {
        $game = new Game();
        $tour = new Tour();

        $tour->setTornament($tournament);
        $tour->setName('Тур 1');

        $game->setTour($tour);

        if ($this->checkTeamRighLeft())
        {
            $game->setTeamRight($team);
        }
        else
        {
            $game->setTeamLeft($team);
        }
        //Отдельная функция

        //Отдельная функция
        $this->entityManager->persist($game);
        $this->entityManager->flush();

    }

    /** Запрос на получение всех комментариев по определенной новости
     * @param Tournament $tournament
     * @return array Массив из сущностей Comment
     */
    private function listTeamInNumTournament(Tournament $tournament): array
    {
        /** Получаем из базы данных список комментариев по новости */
        return $this->entityManager->getRepository(Team::class)->findBy(
            ['tournament' => $tournament],
            ['id' => 'DESC']
        );
    }

    private function checkTeamRighLeft():bool
    {


        return true;
    }
}