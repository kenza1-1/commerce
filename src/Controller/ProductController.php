<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    /**
     * @Route("/", name="produits")
     */
    public function index(ProductRepository $repo,CategoryRepository $repos)
    {
        // $repo = $this->getDoctrine()->getRepository(Product::class);
        $products = $repo->findBy(['online' => 1]);
        return $this->render('product/index.html.twig', [
            'products' => $products,
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
            'product' => $product 
        ]);
    }
}
