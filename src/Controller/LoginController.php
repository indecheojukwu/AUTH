<?php

namespace App\Controller;

use App\Entity\Person;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\FormLoginAuthenticator;

class LoginController extends AbstractController
{

    private $manager;
    private $hasher;

    public  function __construct(EntityManagerInterface $manager, UserPasswordHasherInterface $hasher) {
        $this->manager = $manager;
        $this->hasher = $hasher;
    }

    #[Route(path: '/signin', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response {

        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('app_dashboard');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('auth/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/register', name: 'app_register')]
    public function register(UserAuthenticatorInterface $auth, FormLoginAuthenticator $formLoginAuthenticator, Request $request): Response {

        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('app_dashboard');
        }


        if($request->isMethod('POST')){

            if($request->get('_password') !== $request->get('c_password')){

                $error = 'Passwords do not match';

                return $this->render('auth/register.html.twig', [
                    'error' => $error
                ]);
            }

            $person = new Person();
            $person->setName($request->get('full_name'));
            $person->setPhonenumber($request->get('phone_number'));
            $person->setUsername($request->get('username'));
            $this->manager->persist($person);
            $this->manager->flush();

            $user = new User();
            $user->setPerson($person);
            $user->setEmail($request->get('email'));
            $password = $this->hasher->hashPassword($user, $request->get('_password'));
            $user->setPassword($password);
            $this->manager->persist($user);
            $this->manager->flush();

            $auth->authenticateUser($user, $formLoginAuthenticator, $request);

            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('auth/register.html.twig', [
            'error' => null
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
