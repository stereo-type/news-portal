<?php
/**
 * @package    RegistrationController.php
 * @copyright  2025 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormTypeForm;
use App\Form\VerifyTypeForm;
use App\Message\SendTelegramConfirmationCode;
use App\Service\TelegramBotService;
use App\Service\TelegramCodeGenerator;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Throwable;

class RegistrationController extends AbstractController
{
    /**
     * @throws \JsonException
     */
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        SessionInterface $session,
        UserService $userService,
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormTypeForm::class, $user);

        if (!$form->isSubmitted() && $session->has('registration_form_data')) {
            $savedFormData = $session->get('registration_form_data');
            $session->remove('registration_form_data');
            $user->setEmail($savedFormData['email'] ?? '');
        }

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                try {
                    $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));
                    $created = $userService->createUser($user);

                    return $this->redirectToRoute('app_confirmation_instructions', ['id' => $created->getId()]);
                } catch (\Throwable $e) {
                    $this->addFlash('error', $e->getMessage());

                    return $this->redirectToRoute('app_register');
                }
            } else {
                foreach ($form->getErrors(true) as $error) {
                    if ($error instanceof FormError) {
                        $this->addFlash('error', $error->getMessage());
                    }
                }
                $requestContent = $request->request->all();
                $data = $requestContent[$form->getName()] ?? [];
                $session->set('registration_form_data', $data);

                return $this->redirectToRoute('app_register');
            }
        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/register/instructions/{id}', name: 'app_confirmation_instructions')]
    public function instructions(int $id): Response
    {
        return $this->render('security/instructions.html.twig', ['id' => $id]);
    }

    #[Route('/register/verify/{id}', name: 'app_verify')]
    public function verify(int $id, Request $request, UserService $userService): Response
    {
        $form = $this->createForm(VerifyTypeForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                try {
                    $requestContent = $request->request->all();
                    $data = $requestContent[$form->getName()] ?? [];
                    if (isset($data['code'])) {
                        $userService->verifyUser($id, $form->getData()['code']);

                        return $this->redirectToRoute('app_login');
                    }

                    throw new \RuntimeException('Code is required.');
                } catch (Throwable $e) {
                    $this->addFlash('error', $e->getMessage());

                    return $this->redirectToRoute('app_verify', ['id' => $id]);
                }
            } else {
                $this->addFlash('error', 'Unexpected error');

                return $this->redirectToRoute('app_verify', ['id' => $id]);
            }
        }

        return $this->render('security/verify.html.twig', ['verifyForm' => $form->createView(), 'id' => $id]);
    }

    #[Route('/register/resent/{id}', name: 'app_resend')]
    public function resendTelegramKey(
        int $id,
        EntityManagerInterface $entityManager,
        TelegramCodeGenerator $codeGenerator,
        MessageBusInterface $bus,
    ): Response {
        $success = true;
        $msg = null;
        try {
            $user = $entityManager->getRepository(User::class)->find($id);
            if (!$user) {
                throw $this->createNotFoundException();
            }
            $code = $codeGenerator->generateFor($user);
            $bus->dispatch(new SendTelegramConfirmationCode($user->getEmail(), $code->getCode()));
        } catch (\Throwable $e) {
            $success = false;
            $msg = $e->getMessage();
        }

        return $this->json(['success' => $success, 'message' => $msg]);
    }

}