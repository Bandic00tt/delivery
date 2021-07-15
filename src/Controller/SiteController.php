<?php
namespace App\Controller;

use App\Repository\ProductRepository;
use App\Service\Selection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SiteController extends AbstractController
{
    public function __construct(
        private ProductRepository $productRepository
    ) {}

    /**
     * @Route ("/")
     * @return Response
     */
    public function index(): Response
    {
        $products = $this->productRepository->findBySiteCode('avecafe');
        $productGroups = (new Selection($products, 500))->getProductGroups();

        return $this->render('site/index.html.twig', [
            'productGroups' => $productGroups
        ]);
    }
}