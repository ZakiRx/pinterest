<?php


namespace App\Controller\Admin;


use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class CategoryController
 * @package App\Controller\Admin
 * @Route("/admin/category")
 */
class CategoryController extends AbstractController
{

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * CategoryController constructor.
     * @param EntityManagerInterface $entityManager
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(EntityManagerInterface $entityManager, CategoryRepository $categoryRepository)
    {
        $this->entityManager = $entityManager;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @Route("/",name="category_index",methods={"GET"})
     */
    public function index(): Response
    {
        $categories = $this->categoryRepository->findAll();
        return $this->render("Admin/category/index.html.twig", compact("categories"));
    }

    /**
     * @Route("/new",name="category_new")
     * @param Request $request
     */
    public function new(Request $request)
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($category);
            $this->entityManager->flush();
            $this->addFlash("success", "Category Has been Added");
            return $this->redirectToRoute("category_index");
        }
        return  $this->render("Admin/category/new.html.twig",[
            "form"=>$form->createView(),
            "category"=>$category
        ]);

    }
}