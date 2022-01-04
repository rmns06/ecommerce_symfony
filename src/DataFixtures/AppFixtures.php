<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Product;
use App\Entity\Category;
use Bluemmb\Faker\PicsumPhotosProvider;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    protected $slugger;
    protected $encoder;

    public function __construct(SluggerInterface $slugger, UserPasswordHasherInterface $encoder)
    {
        $this->slugger = $slugger;
        $this->encoder = $encoder;
    }
    
    public function load(ObjectManager $manager): void
    {
        //create products and add them to the database
        // for($p = 0; $p < 100; $p ++) {
        //     $product = new Product();
        //     $product
        //         ->setName('Product '.$p)
        //         ->setPrice(mt_rand(100, 1000))
        //         ->setSlug('product-'.$p);
        //     $manager->persist($product);

        //create products and add them to the database with faker and some providers
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new \Liior\Faker\Prices($faker));
        $faker->addProvider(new \Bezhanov\Faker\Provider\Commerce($faker));
        $faker->addProvider(new \Bluemmb\Faker\PicsumPhotosProvider ($faker));
        
        $admin = new User;
        $admin
            ->setFullName("Admin")
            ->setEmail("Admin@gmail.com")
            ->setPassword($this->encoder->hashPassword($admin, $faker->password))
            ->setRoles(["ROLE_ADMIN"]);
        $manager->persist($admin);

        for ($i=0; $i < 5; $i++) { 
            $user = new User;
            $user   
                ->setEmail($faker->email())
                ->setFullName($faker->name())
                ->setPassword($this->encoder->hashPassword($user, $faker->password(15,15)));
            $manager->persist($user);
        }

        for($c = 0; $c < 3; $c ++){
            $category = new Category;
            $category
                ->setName($faker->department)
                ->setSlug(strtolower($this->slugger->slug($category->getName())));
            $manager->persist($category);

            for($p = 0; $p < mt_rand(15,20); $p ++) {
                $product = new Product();
                $product
                    ->setName($faker->productName())
                    ->setPrice($faker->price(4000,10000))
                    ->setCategory($category)
                    ->setSlug(strtolower($this->slugger->slug($product->getName())))
                    ->setShortDescription($faker->paragraph())
                    ->setMainPicture($faker->imageUrl(400,400, true));
                $manager->persist($product);
            }
        }
        
        $manager->flush();
    }
}
