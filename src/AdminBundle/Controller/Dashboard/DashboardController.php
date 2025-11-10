<?php

namespace App\AdminBundle\Controller\Dashboard;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{

    private $orgManager;
    private $requeststack;
    private $manager;

    public function __construct(RequestStack $requeststack, EntityManagerInterface $manager) {
        $this->requeststack = $requeststack;
        $this->manager = $manager;
    }

    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(): Response {

        return $this->render('@admin/dashboard.html.twig', [
        ]);

    }
}
