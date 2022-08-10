<?php

namespace App\DataFixtures;

use App\Entity\Ingredient;
use App\Entity\Recipe;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    /**
     * @var Generator
     */
    private Generator $faker;

    public function __construct() {
        $this->faker = Factory::create('fr_FR');
    }
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);


        // Users

        $users = [];
        for ($i= 0; $i<10; $i++) {
            $user = new User();
            $user->setFullName($this->faker->name())
                ->setPseudo(mt_rand(0, 1) === 1 ? $this->faker->firstName() : null)
                ->setEmail($this->faker->email())
                ->setRoles(['ROLE_USER'])
                ->setPassword("password")
                ->setUpdatedAt(new \DateTimeImmutable());
            $user->setPlainPassword("password");
            $users[] = $user;
            $manager->persist($user);
        }
        // Ingredients
        $ingredients = [];
        for ($i=1; $i<=50; $i++){
            $ingredient = new Ingredient();
            $ingredient->setName($this->faker->word())
                ->setPrice(mt_rand(0, 200))
                ->setUser($users[mt_rand(0, count($users) -1)]);
            $ingredients[] = $ingredient;
            $manager->persist($ingredient);
        }

        // Recipes

        for ($i=1; $i<=25; $i++){
            $recipe = new Recipe();
            $recipe->setName($this->faker->word())
                ->setTime(mt_rand(1, 1440))
                ->setNbPeople(mt_rand(1, 50))
                ->setDifficulty(mt_rand(1, 5))
                ->setDescription($this->faker->text(300))
                ->setPrice(mt_rand(1, 1000))
                ->setUser($users[mt_rand(0, count($users) -1)])
                ->setIsFavorite(true);

            for ($k=0; $k< mt_rand(5, 15); $k++){
                $recipe->addIngredient($ingredients[mt_rand(0, count($ingredients)-1)]);
            }
            $manager->persist($recipe);
        }
        $manager->flush();
    }
}
