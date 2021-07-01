<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\Repository\RepositoryFactory;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CategoryController extends AbstractController
{
    protected $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }
    public function renderMenuList()
    {
        //1. Aller chercher les catégories dans la base de données

        $categories = $this->categoryRepository->findAll();

        return $this->render('category/_menu.html.twig', [
            'categories' => $categories
        ]);
    }
    /**
     * @Route("/admin/category/create", name="category_create")
     */
    public function create(EntityManagerInterface $em, Request $request, SluggerInterface $slugger)
    {
        $category = new Category;

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setSlug(strtolower($slugger->slug($category->getName())));
            $em->persist($category);
            $em->flush();
            return $this->redirectToRoute("homepage");
        }
        $formView = $form->createView();
        return $this->render('category/create.html.twig', [
            'formView' => $formView,
            'slug' => $category->getSlug()
        ]);
    }

    /**
     * @Route("/admin/category/{id}/edit", name="category_edit")
     */

    public function edit($id, CategoryRepository $categoryRepository, Request $request, EntityManagerInterface $em, SluggerInterface $slugger)
    {


        $category = $categoryRepository->find($id);
        if (!$category) {
            throw new NotFoundHttpException("Cette catégorie n'existe pas");
        }


        $form = $this->createForm(CategoryType::class, $category);

        if (!$category) {
            throw $this->createNotFoundException("La catégorie demandée n'existe pas !");
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setSlug(strtolower($slugger->slug($category->getName())));
            $em->flush();
            return $this->redirectToRoute("homepage");
        }
        $formView = $form->createView();
        return $this->render("category/edit.html.twig", [
            'category' => $category,
            'formView' => $formView
        ]);
    }
}
