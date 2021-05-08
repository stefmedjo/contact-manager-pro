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
      
      // We link the user to the category he created
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
    // We check if the user is granted to edit the category
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
    // We check if the user can view the category
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

    // We will display a limited number of categories
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
   * The user has to submit a post request with a csrf token and the id
   * of the category he wants to delete. We first check if csrf token is valid and after 
   * if user is authorized to delete the category using voter.
   * 
   * @Route("/delete", name="category_delete")
   *
   * @param CategoryRepository $categoryRepository
   * @param Request $request
   * @return void
   */
  public function delete(CategoryRepository $categoryRepository, Request $request) {
    // Retrieve the csrf token
    $token = $request->request->get("token");

    // Check if the token is not valid, redirect to the category list with a flash message error
    if(!$this->isCsrfTokenValid('delete-contact', $token)) {
      $this->addFlash('danger',"Invalid credentials.");
      return $this->redirectToRoute('category_list');
    }

    // Find the category using the id in the request
    // if not found, redirect to the category list with an error flash message
    $foundCategory = $categoryRepository->findOneBy(['id' => $request->request->get("id")]);
    if(!$foundCategory) {
      $this->addFlash('danger',"Invalid credentials.");
      return $this->redirectToRoute('category_list');
    }

    // Check if user can delete a category using voter App\Voter\CategoryVoter
    $this->denyAccessUnlessGranted('delete',$foundCategory);

    // If yes, remove the category and redirect to category list with success flash message
    $this->_em->remove($foundCategory);
    $this->_em->flush();
    $this->addFlash("success", "Category successfully deleted.");
    return $this->redirectToRoute('category_list');

  }




}