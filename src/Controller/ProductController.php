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
     * @Route("/", name="products")
     */
    public function index(ProductRepository $repo)
    {
        // $repo = $this->getDoctrine()->getRepository(Product::class);//
        $products = $repo->findBy(['online' => 1]);// selectionner les produits (dans le repo) avec leur disponibilités
        return $this->render('product/index.html.twig', [
            'products' => $products, //on envoit les produits selectionnés au fichier twig
        ]);
    }
    

     /**
     * @Route("/Produit/{id}", name="products_show")  // {id} est l'identifiant
     */
    // public function show(ProductRepository $repo,$id) Pour récuperer cet identifinat 
    public function show(ProductRepository $repo, $id) //On fait passer l'identifiant a la fonction show
    {
        $product = $repo->find($id); // trouver le produit qui a l'identifiant qu'on a envoyé dans l'adresse en haut 
        return $this->render('show/index.html.twig', [
            'product' => $product 
        ]);
    }
    
}
