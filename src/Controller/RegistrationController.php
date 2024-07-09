<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Service\PostgresqlDBService;
use App\Service\TournamentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    /** Страница регистрация нового пользователя
     * @param Request $request Тело страницы
     * @param UserPasswordHasherInterface $userPasswordHasher Хеширование пароля
     * @param Security $security Проверка пароля
     * @param PostgresqlDBService $service Сервис по работе базой данных
     * @return Response
     */
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, PostgresqlDBService $service): Response
    {
        /** Создаем пользователя */
        $user = new User();
        /** Создаем форму для регистрации */
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        /** Проверяем нажатие кнопки и валидность данных */
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            /** Записываем в базу данных пользователя */
            $service->addItem($user);
            /** Переходим обратно в Login */
            return $security->login($user, 'form_login', 'main');
        }

        /** Отправляем данные в шаблон
         * @registrationForm Данные формы регистрации
         */
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
