<?php

namespace App\Controller;

use App\Form\SecurityControllerType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /** Страница авторизации пользователя
     * @param AuthenticationUtils $authenticationUtils Log in через AuthenticationUtils
     * @return Response
     */
    #[Route(path: '/login', name: 'app_login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        /** Записываем ошибки если они есть */
        $error = $authenticationUtils->getLastAuthenticationError();

        /** Строим форму для ввода Логина и Пароля */
        $form = $this->createForm(SecurityControllerType::class);

        /** Проверка на авторизацию пользователя */
        $this->checkUserAuthenticationToRoute($this->getUser());

        /** Отправляем данные в шаблон
         * @error Ошибки если они есть
         * @form Форма для ввода
         */
        return $this->render('security/login.html.twig', [
            'error' => $error,
            'form' => $form
        ]);
    }

    /** Перенаправление с "/" на главную или на авторизацию
     * @return Response
     */
    #[Route(path: '/', name: 'app_loginOn', methods: ['GET', 'POST'])]
    public function loginOn(): Response
    {
        return $this->checkUserAuthenticationToRoute($this->getUser());
    }

    /** Выход из авторизации
     * @return Response
     */
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): Response
    {
        return $this->redirectToRoute('app_login');
    }

    /** Проверка на авторизациею
     * @param UserInterface|null $userInterface - $this->getUser()
     * @return Response
     */
    public function checkUserAuthenticationToRoute(null|UserInterface $userInterface):Response
    {
        if ($userInterface){
            return $this->redirectToRoute('app_tournament');
        }
        return $this->redirectToRoute('app_login');
    }
}
