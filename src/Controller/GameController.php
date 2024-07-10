<?php

namespace App\Controller;

use App\Entity\Game;
use App\Form\GameType;
use App\Repository\GameRepository;
use App\Service\PostgresqlDBService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/game')]
#[IsGranted('ROLE_USER')]
class GameController extends AbstractController
{
    /** Список всех игр
     * @param GameRepository $gameRepository Данные игр
     * @return Response
     */
    #[Route('/', name: 'app_game_index', methods: ['GET'])]
    public function index(GameRepository $gameRepository): Response
    {
        /** Отправляем данные в шаблон
         * @games Список игр
         */
        return $this->render('game/index.html.twig', [
            'games' => $gameRepository->findAll(),
        ]);
    }

    /** Страница редактирование игры
     * @param Request $request Данные странницы
     * @param Game $game Данные игры
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/{id}/edit', name: 'app_game_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Game $game, EntityManagerInterface $entityManager): Response
    {
        /** Форма редактирование игры */
        $form = $this->createForm(GameType::class, $game);
        $form->handleRequest($request);

        /** Обработка кнопки */
        if ($form->isSubmitted() && $form->isValid()) {
            /** Запись данных игры в базу данных  */
            $entityManager->flush();

            /** Переходим обратно в тур после записи
             * @id ID турнира
             * @numTour Номер тура
             */
            return $this->redirectToRoute('app_tournament_edit', [
                'id' => $game->getTour()->getTournament()->getId(),
                'numTour' => $game->getTour()->getNum()
            ], Response::HTTP_SEE_OTHER);
        }

        /** Отправляем данные в шаблон
         * @game Игра для редактирования
         * @form Форма игры для редактирования
         */
        return $this->render('game/edit.html.twig', [
            'game' => $game,
            'form' => $form,
        ]);
    }

    /** Удаление игры через $request
     * @param Request $request Данные request
     * @param PostgresqlDBService $serviceDB Работа с турниром
     * @return Response
     */
    #[Route('/delete/', name: 'app_game_delete', methods: ['POST'])]
    public function delete(Request $request, PostgresqlDBService $serviceDB): Response
    {
        /** Достаем из страницы ID игры */
        $idGame = $request->request->get('gameID');
        /** Берем игру из базы данных по ID */
        $game = $serviceDB->searchGameByID($idGame);
        /** Удаляем игру из базы данных */
        $serviceDB->deleteItem($game);
        /** Выводим id игры, которая была удалена */
        return new Response($idGame, Response::HTTP_OK);
    }
}
