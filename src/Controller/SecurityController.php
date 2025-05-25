<?php

namespace App\Controller;

use App\Form\LoginTypeForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\SecurityRequestAttributes;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(Request $request, AuthenticationUtils $utils): Response
    {
        $error = $utils->getLastAuthenticationError();
        $errorMessage = $error?->getMessage();
        $form = $this->createForm(LoginTypeForm::class, ['username' => $utils->getLastUsername()]);
        $form->handleRequest($request);

        $exception = $request->getSession()->get(SecurityRequestAttributes::AUTHENTICATION_ERROR);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->redirectToRoute('app_main');
        }

        return $this->render('security/login.html.twig', [
            'loginForm' => $form->createView(),
            'error' => $errorMessage,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(Request $request): void
    {
        $request->cookies->set('logout', true);
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
