<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Category;
use App\Event\ProductViewEvent;
use App\Form\ProductType;
use Faker\Provider\ar_JO\Text;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Expr\BinaryOp\Greater;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\FrameworkBundle\Console\Descriptor\TextDescriptor;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\LessThan;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductController extends AbstractController
{
    /**
     * @Route("/{slug}", name="product_category", priority = -1)
     */
    public function category($slug, CategoryRepository $categoryRepository): Response
    {

        $category = $categoryRepository->findOneBy([
            "slug" => $slug,
        ]);

        if (!$category) {
            throw $this->createNotFoundException("La cat??gorie demand??e n'existe pas");
        }

        return $this->render('product/category.html.twig', [
            'slug' => $slug,
            "category" => $category
        ]);
    }
    /**
     * @Route("/{category_slug}/{slug}", name="product_show", priority = -1)
     */
    public function show($slug, $prenom, ProductRepository $productRepository, Request $request, EventDispatcherInterface $eventDispatcherInterface)
    {
        $product = $productRepository->findOneBy([
            "slug" => $slug,
        ]);
        if (!$product) {
            throw $this->createNotFoundException("Le produit demand?? n'existe pas");
        }
        $productEvent = new ProductViewEvent($product);
        $eventDispatcherInterface->dispatch($productEvent, 'product.view');
        return $this->render("product/show.html.twig", [

            "product" => $product,

        ]);
    }
    /**
     * @Route("/admin/product/{id}/edit", name="product_edit")
     */
    public function edit($id, ProductRepository $productRepository, Request $request, EntityManagerInterface $em, ValidatorInterface $validator)
    {

        $product = $productRepository->find($id);
        $form = $this->createForm(ProductType::class, $product);


        if (!$product) {
            throw $this->createNotFoundException("Le produit demand?? n'existe pas");
        }
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug' => $product->getSlug()
            ]);
        }
        $formView = $form->createView();
        return $this->render(
            'product/edit.html.twig',
            ['product' => $product, 'formView' => $formView]
        );
    }
    /**
     * @Route("/admin/product/create", name="product_create")
     */
    public function create(Request $request, SluggerInterface $slugger, EntityManagerInterface $em)
    {
        $product = new Product;
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $product->setSlug(strtolower($slugger->slug($product->getName())));
            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug' => $product->getSlug()
            ]);
        }

        $formView = $form->createView();
        return $this->render('product/create.html.twig', [
            "formView" => $formView
        ]);
    }
}
