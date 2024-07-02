<?php

namespace App\Service;

use App\Entity\Team;
use App\Repository\TeamRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'dev')]
class TeamService extends AbstractController
{
    /** Менеджер сущностей
     * @param EntityManagerInterface $entityManager
     * @param TeamRepository $teamRepository
     */
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TeamRepository $teamRepository,
    ){}

    /** Проверка на идентичность
     * @param Team $team Данные команды
     * @return bool
     */
    public function identityVerification(Team $team):bool
    {
        $teamList = $this->teamRepository->findAll();
        foreach ($teamList as $t) {
            if ($t->getName() == $team->getName()){
                return false;
            }
        }
        return true;
    }

    /** Удаление команды из базы данных
     * @param Team $team Данные команды
     * @return void
     */
    function deleteTeam(Team $team):void
    {
            $this->entityManager->remove($team);
            $this->entityManager->flush();
    }

    /** Добавляет Команду в базу данных
     * @param Team $team Данные команды
     * @return void
     */
    function addTeam(Team $team):void
    {
        $this->entityManager->persist($team);
        $this->entityManager->flush();
    }
}