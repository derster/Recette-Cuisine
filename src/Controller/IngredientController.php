<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IngredientController extends AbstractController
{
    /**
     * This function display all ingredients
     * @param IngredientRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */

    #[Route('/ingredient', name: 'ingredient.index', methods:['GET'])]
    public function index(IngredientRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $ingredients = $paginator->paginate(
            $repository->findAll(), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
        return $this->render('pages/ingredient/index.html.twig', [
            'ingredients' => $ingredients,
        ]);
    }
    /**
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/ingredient/new', name: 'ingredient.new', methods:['GET', 'POST'])]
    public function newForm(Request $request, EntityManagerInterface $manager) : Response
    {
        $ingredient = new Ingredient();
        $form = $this->createForm(IngredientType::class, $ingredient);


        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $ingredient = $form->getData();
            $manager->persist($ingredient);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre ingrédient a été créé avec succès !'
            );

            return $this->redirectToRoute('ingredient.index');

        }
        return $this->render('pages/ingredient/new.html.twig',
            ['form'=> $form->createView()]
        );
    }


    #[Route('/ingredient/edit/{id}', name: 'ingredient.edit', methods:['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $manager, Ingredient $ingredient): Response
    {

        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()){
            $ingredient = $form->getData();
            $manager->persist($ingredient);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre ingrédient a été modifié avec succès !'
            );

            return $this->redirectToRoute('ingredient.index');

        }

        return $this->render('pages/ingredient/edit.html.twig',
            [
                "form" => $form->createView(),
            ]);
    }


    #[Route('/ingredient/delete/{id}', name: 'ingredient.delete', methods:['GET', 'POST'])]
    public function delete(EntityManagerInterface $manager, Ingredient $ingredient){

        if (!$ingredient){
            $this->addFlash(
                "warning",
                "L'ingrédient n'a pas été trouvé !"
            );
            return $this->redirectToRoute('ingredient.index');
        }

        $manager->remove($ingredient);
        $manager->flush();

        $this->addFlash(
            'success',
            'Votre ingrédient a été supprimé avec succès !'
        );
        return $this->redirectToRoute('ingredient.index');
    }
}