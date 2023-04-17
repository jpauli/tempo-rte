<?php

namespace App\Controller;

use App\Model\TempoService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TempoController extends AbstractController
{
    #[Route('/', name: 'app_tempo')]
    public function index(TempoService $service): Response
    {
        return $this->render('tempo/index.html.twig', ['tempo' => $service->getTempoColor()]);
    }
}