<?php

namespace App\Controller;

use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home.index', methods:['GET'])]
    public function index(RecipeRepository $repository): Response
    {
        return $this->render('pages/home/index.html.twig', [
            'recipes' => $repository->findPublicRecipe(6),
        ]);
    }
}
