<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function Symfony\Bundle\MakerBundle\Util\decrementDepth;

class RecipeController extends AbstractController
{
    #[Route('/recipe', name: 'recipe.index', methods:['GET'])]
    public function index(RecipeRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {

        $recipes = $paginator->paginate(
            $repository->findAll(),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('pages/recipe/index.html.twig', [
            'recipes' => $recipes,
        ]);
    }

    #[Route('/recipe/new', name: 'recipe.new', methods:['GET', 'POST'])]
    public function newForm(Request $request, EntityManagerInterface $manager) : Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){

            $recipe = $form->getData();
            $manager->persist($recipe);
            $manager->flush();
            $this->addFlash(
                'success',
                'Votre recette a été créé avec succès !'
            );
            return $this->redirectToRoute('recipe.index');

        }
        return $this->render('pages/recipe/new.html.twig',
            ['form' => $form->createView()]
        );
    }


    #[Route('/recipe/edit/{id}', name: 'recipe.edit', methods:['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $manager, Recipe $recipe): Response
    {
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()){
            $recipe = $form->getData();

            $manager->persist($recipe);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre recette a été modifié avec succès !'
            );

            return $this->redirectToRoute('recipe.index');
        }

        return $this->render('pages/recipe/edit.html.twig',
            [
                "form" => $form->createView()
            ]);
    }


    #[Route('/recipe/delete/{id}', name: 'recipe.delete', methods:['GET', 'POST'])]
    public function delete(EntityManagerInterface $manager, Recipe $recipe){

        $manager->remove($recipe);
        $manager->flush();

        $this->addFlash(
            'success',
            'Votre recette a été supprimée avec succès !'
        );
        return $this->redirectToRoute('recipe.index');
    }
}
