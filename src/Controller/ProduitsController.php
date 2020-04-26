<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProduitsController extends AbstractController
{
    /**
     * @Route("/", name="produits")
     */
    public function index(ProductRepository $repo,CategoryRepository $repos)
    {
        // $repo = $this->getDoctrine()->getRepository(Product::class);
        $products = $repo->findBy(['online' => 1]);
        $categories= $repos->findAll();
        return $this->render('produits/index.html.twig', [
            'controller_name' => 'ProduitsController',
            'products' => $products,
            'categories' => $categories
        ]);
    }
     /**
     * @Route("/Produit/{id}", name="products_show")  //dans une route qui est /quelque chose(ceqlq chose c'est un identifiant)
     */
    // public function show(ProductRepository $repo,$id) Pour récuperer cet identifinat 
    public function show(ProductRepository $repo, $id) //Product $product
    {
        $product = $repo->find($id);
        return $this->render('show/index.html.twig', [
            'controller_name' => 'ProduitsController',
            'product' => $product 
        ]);
    }
}
