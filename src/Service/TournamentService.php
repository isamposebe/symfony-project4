<?php

namespace App\Service;

use App\Entity\Game;
use App\Entity\Team;
use App\Entity\Tour;
use App\Entity\Tournament;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Null_;
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

    /** Запрос на получение всех комментариев по определенной новости
     * @param Tournament $tournament
     * @return array Массив из сущностей Comment
     */
    private function listTourNumTournament(Tournament $tournament): array
    {
        /** Получаем из базы данных список комментариев по новости */
        return $this->entityManager->getRepository(Tour::class)->findBy(
            ['tournament' => $tournament],
            ['id' => 'DESC']
        );
    }

    /** Добавление команды в турнир
     * @param Team $team Данные команды
     * @param Tournament $tournament Данные турнира
     * @return Tour Данные первого тура
     */
    public function addTeamTournament(Team $team, Tournament $tournament): Tour
    {
        $tour = $this->addOneTour($tournament);

        $this->addGame($team, $tour);
        return $tour;
    }

    /** Добавляем в базу данных тур или берем его из базы
     * @param Tournament $tournament Данные турнира
     * @return Tour Данные первого тура
     */
    private function addOneTour(Tournament $tournament):Tour
    {
        /** Создаем новый первый тур */
        $strNameTour = 'Тур 1';
        $tour = new Tour();

        /** Берем всех туров по tournament */
        $listTour = $this->entityManager->getRepository(Tour::class)->findBy(['tournament' => $tournament]);

        foreach ($listTour as $item) {
            /** Проверяем на существование в базе данных тура */
            if ($item->getName() == $strNameTour){
                $tour = $item;
            }
            else{
                $tour->setTornament($tournament);
                $tour->setName($strNameTour);
                $this->entityManager->persist($tour);
                $this->entityManager->flush();
            }
        }

        return $tour;
    }

    /** Добавление команды в тур
     * @param Team $team Данные команды
     * @param Tour $tour Данные тура
     * @return Game Данные игры
     */
    private function addGame(Team $team, Tour $tour):Game
    {
        $game = new Game();
        $game->setTour($tour);

        /** Берем всех туров по tournament */
        $listGame = $this->entityManager->getRepository(Game::class)->findBy([
            'tour' => $tour,
        ]);
        /** Если есть игра с командой $team, то вытаскиваем эту игру */
        foreach ($listGame as $item) {
            /** Проверяем на существование в базе данных тура */
            if ($item->getTeamLeft() === $team ){
                return $item;
            }
            else{
                if ($item->getTeamRight() === $team){
                    return $item;
                }
            }
        }
        /** Записываем игру в свободные элементы */
        foreach ($listGame as $item) {
            //Возможно надо удалять элементы
            if ($item->getTeamLeft() === Null){
                $item->setTeamLeft($team);
                $game = $item;
            }else{
                if ($item->getTeamRight() === Null){
                    $item->setTeamRight($team);
                    $game = $item;
                }
                else{
                    $game->setTeamLeft($team);
                }
            }
        }
        /** Записываем в базу данных игру*/
        $this->entityManager->persist($game);
        $this->entityManager->flush();

        return $game;
    }
}