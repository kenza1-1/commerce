<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoryController extends AbstractController
{
     /**
     * @Route("/category", name="category")
     */
    public function index(CategoryRepository $repo)
    {
        $categories = $repo->findAll(); //selectionner tout les cetegorie dans le repo
        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
            'categories' => $categories
        ]);
    }
     /**
     * @Route("/category/{category}", name="category_products") {id} est l'identifiant
     */
    public function category(ProductRepository $repo, $category) //On fait passer l'identifiant a la fonction category
    {
        $products = $repo->findByCategory($category); // Afficher les produits par leur categories
        return $this->render('product/index.html.twig', [
            'controller_name' => 'CategoryController',
            'products' => $products
        ]);
    }
}

