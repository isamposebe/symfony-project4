<?php

namespace App\Service;

use App\Entity\Team;
use App\Entity\Tournament;
use Doctrine\ORM\EntityManagerInterface;

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
     * @return bool
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

    /** Поиск комментария по ID
     * @param int $id ID комментария
     * @return Tournament Получаем комментарий
     */
    public function searchTournamentID(int $id): Tournament
    {
        return $this->entityManager->getRepository(Tournament::class)->find($id);
    }
}