<?php

namespace App\Service;

use App\Entity\Game;
use App\Entity\Team;
use App\Entity\Tour;
use App\Entity\Tournament;
use Doctrine\ORM\EntityManagerInterface;

class PostgresqlDBService
{
    /** Менеджер сущностей
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ){}
    /** Удаление элемента из базы данных
     * @param $item - Данные элемента
     * @return void
     */
    function deleteItem($item):void
    {
        $this->entityManager->remove($item);
        $this->entityManager->flush();
    }

    /** Добавляет элемент в базу данных
     * @param $item - Данные элемента
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

    /** Поиск команды по имени
     * @param string $nameTeam Название команды
     * @return Team Получаем команду из базы данных
     */
    public function searchTeam(string $nameTeam): Team
    {
        //return $this->entityManager->getRepository(Team::class)->find($nameTeam);
        $trams = $this->entityManager->getRepository(Team::class)->findBy(['name' => $nameTeam]);
        return $trams[0];
    }

    public function searchGameByID(int $idGame): Game
    {
        return $this->entityManager->getRepository(Game::class)->find($idGame);
    }


    /** Запрос на получение всех Туров по определенному турниру
     * @param Tournament $tournament Данные турнира
     * @return array Массив из сущностей Tour
     */
    public function listTourNumTournament(Tournament $tournament): array
    {
        /** Получаем из базы данных список комментариев по новости */
        return $this->entityManager->getRepository(Tour::class)->findBy(
            ['tournament' => $tournament],
            ['id' => 'DESC']
        );
    }

    /** Выбрать список игр по туру
     * @param Tour $tour Данные тура
     * @return array Массив игр по определенному туру
     */
    public function listGame(Tour $tour): array
    {
        return $this->entityManager->getRepository(Game::class)->findBy(['tour' => $tour]);
    }
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
}