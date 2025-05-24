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
use App\Service\UserService;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationController extends AbstractController
{
    /**
     * @throws \JsonException
     */
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
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
                    $userService->createUser($user);

                    return $this->redirectToRoute('app_confirmation_instructions');
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

    #[Route('/register/instructions', name: 'app_confirmation_instructions')]
    public function instructions(): Response
    {
        return $this->render('security/instructions.html.twig');
    }

    #[Route('/register/verify', name: 'app_verify')]
    public function verify(): Response
    {
        return $this->render('security/verify.html.twig');
    }
}