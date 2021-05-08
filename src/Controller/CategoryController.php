<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/category")
 */
class CategoryController extends AbstractController {

  /** @var EntityManagerInterface $_em */
  private $_em;

  public function __construct(
    EntityManagerInterface $entityManagerInterface
  )
  {
    $this->_em = $entityManagerInterface;
  }

  /**
   * Create a new category
   * @Route("/create", name="category_create")
   *
   * @param Request $request
   * @return void
   */
  public function create(Request $request) {
    $category = new Category();
    $form = $this->createForm(CategoryType::class, $category);
    $form->handleRequest($request);
    if($form->isSubmitted() && $form->isValid()) {

      /** @var User $user */
      $user = $this->getUser();

      $category->setCreatedBy($user);
      $this->_em->persist($category);
      $this->_em->flush();
      return $this->redirectToRoute('category_list');

    }
    return $this->render("category/form.html.twig",['form' => $form->createView()]);
  }

  /**
   * Edit on of my category
   * @Route("/edit/{id}", name="category_edit")
   *
   * @param Request $request
   * @return void
   */
  public function edit(Category $category, Request $request) {
    $this->denyAccessUnlessGranted("edit",$category);
    $form = $this->createForm(CategoryType::class, $category);
    $form->handleRequest($request);
    if($form->isSubmitted() && $form->isValid()) {
      $this->_em->persist($category);
      $this->_em->flush();
      return $this->redirectToRoute('category_list');

    }
    return $this->render("category/form.html.twig",['form' => $form->createView()]);
  }

  /**
   * View one of my category
   * @Route("/view/{id}", name="category_view")
   *
   * @param Category $category
   * @return void
   */
  public function view(Category $category) {
    $this->denyAccessUnlessGranted('view',$category);
    return $this->render("category/view.html.twig",['category' => $category]);
  }

  /**
   * List all of my categories
   * @Route("/list", name="category_list")
   *
   * @param PaginatorInterface $paginator
   * @param Request $request
   * @return void
   */
  public function list(PaginatorInterface $paginator,Request $request) {
    /** @var User $user */
    $user = $this->getUser();

    $query = $user->getCreatedCategories();
    $categories = $paginator->paginate(
        $query,
        $request->query->getInt('page', 1),
        10
    );
    return $this->render("category/list.html.twig",['categories' => $categories]);
  }

  /**
   * Delete one of my category
   * @Route("/delete", name="category_delete")
   *
   * @param CategoryRepository $categoryRepository
   * @param Request $request
   * @return void
   */
  public function delete(CategoryRepository $categoryRepository, Request $request) {
    $token = $request->request->get("token");

    if(!$this->isCsrfTokenValid('delete-contact', $token)) {
      $this->addFlash('danger',"Invalid credentials.");
      return $this->redirectToRoute('category_list');
    }

    $foundCategory = $categoryRepository->findOneBy(['id' => $request->request->get("id")]);
    if(!$foundCategory) {
      $this->addFlash('danger',"Invalid credentials.");
      return $this->redirectToRoute('category_list');
    }

    $this->denyAccessUnlessGranted('delete',$foundCategory);

    $this->_em->remove($foundCategory);
    $this->_em->flush();

    return $this->redirectToRoute('category_list');

  }




}