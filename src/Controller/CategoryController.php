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
        $categories = $repo->findAll();
        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
            'categories' => $categories
        ]);
    }
     /**
     * @Route("/category/{category}", name="category_products")
     */
    public function category(ProductRepository $repo, $category)
    {
        $products = $repo->findByCategory($category);
        return $this->render('product/index.html.twig', [
            'controller_name' => 'CategoryController',
            'products' => $products
        ]);
    }
}

