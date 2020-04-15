<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProduitsController extends AbstractController
{
    /**
     * @Route("/produits", name="produits")
     */
    public function index(ProductRepository $repo)
    {
        // $repo = $this->getDoctrine()->getRepository(Product::class);
        $products = $repo->findBy(['online' => 1]);
        return $this->render('produits/index.html.twig', [
            'controller_name' => 'ProduitsController',
            'products' => $products
        ]);
    }
     /**
     * @Route("/Products/{id}", name="products_show")  //dans une route qui est /quelque chose(ceqlq chose c'est un identifiant)
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
