<?php

namespace App\Controller;

use App\Entity\Team;
use App\Form\TeamType;
use App\Repository\TeamRepository;
use App\Service\TeamService;
use App\Service\TournamentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/team')]
#[IsGranted('ROLE_USER')]
class TeamController extends AbstractController
{
    #[Route('/', name: 'app_team_index', methods: ['GET'])]
    public function index(TeamRepository $teamRepository): Response
    {
        return $this->render('team/index.html.twig', [
            'teams' => $teamRepository->findAll(),
        ]);
    }

    /** Создание новой команды
     * @param Request $request Реквест для работы с формой
     * @param TournamentService $service Сервис работы с командой
     * @return Response
     */
    #[Route('/new', name: 'app_team_new', methods: ['GET', 'POST'])]
    public function new(Request $request,TournamentService $service): Response
    {
        /** Создаем новую команду */
        $team = new Team();
        /** Строим форму для регистрации команды */
        $form = $this->createForm(TeamType::class, $team);
        $form->handleRequest($request);

        /** Проверяем нажатие кнопки и валидность данных */
        if ($form->isSubmitted() && $form->isValid()) {
            /** Проверяем имя на повторы */
            if ($service->identityVerificationName(item: $team)){
                /** Записываем в базу данных */
                $service->addItem($team);
                $this->addFlash(
                    'notice',
                    'Your changes were saved!'
                );
            }else{
                $this->addFlash(
                    'notice',
                    'Команда с таким именем уже существует'
                );
            }

            /** Пустая форма для повторного ввода*/
            $form = $this->createForm(TeamType::class, $team);
        }

        return $this->render('team/new.html.twig', [
            'team' => $team,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_team_delete', methods: ['POST'])]
    public function delete(TournamentService $service, Request $request, Team $team): Response
    {
        if ($this->isCsrfTokenValid('delete'.$team->getId(), $request->getPayload()->getString('_token'))) {
             $service->deleteItem($team);
        }
        return $this->redirectToRoute('app_team_index', [], Response::HTTP_SEE_OTHER);
    }
}
