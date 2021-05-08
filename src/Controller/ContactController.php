<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/contact")
 */
class ContactController extends AbstractController {

  /** @var EntityManagerInterface $_em */
  private $_em;

  public function __construct(
    EntityManagerInterface $entityManagerInterface
  )
  {
    $this->_em = $entityManagerInterface;
  }

  /**
   * Create a new Contact
   * @Route("/create", name="contact_create")
   *
   * @param Request $request
   * @return void
   */
  public function create(Request $request) {
    $contact = new Contact();
    /** @var User $user */
    $user = $this->getUser();
    // send user as an option of the form to get all the categories he created
    // as a contact belongs to one category
    $form = $this->createForm(ContactType::class, $contact,['user' => $user]);
    $form->handleRequest($request);
    if($form->isSubmitted() && $form->isValid()) {  

      // link user to contact and save it
      $contact->setCreatedBy($user);
      $this->_em->persist($contact);
      $this->_em->flush();
      $this->addFlash("success","Contact created successfully.");
      return $this->redirectToRoute('contact_list');

    }
    return $this->render("contact/form.html.twig",['form' => $form->createView()]);
  }

  /**
   * Edit on of my Contact
   * @Route("/edit/{id}", name="contact_edit")
   *
   * @param Request $request
   * @return void
   */
  public function edit(Contact $contact, Request $request) {
    // Check if user can edit a contact using voter App\Voter\ContactVoter
    $this->denyAccessUnlessGranted("edit",$contact);
    /** @var User $user */
    $user = $this->getUser();
    $form = $this->createForm(ContactType::class, $contact,['user' => $user]);
    $form->handleRequest($request);
    if($form->isSubmitted() && $form->isValid()) {
      $this->_em->persist($contact);
      $this->_em->flush();
      $this->addFlash("success","Contact successfully updated.");
      return $this->redirectToRoute('contact_list');

    }
    return $this->render("contact/form.html.twig",['form' => $form->createView()]);
  }

  /**
   * View one of my contact
   * @Route("/view/{id}", name="contact_view")
   *
   * @param Contact $contact
   * @return void
   */
  public function view(Contact $contact) {
    // Check if user can view a contact using voter App\Voter\ContactVoter
    $this->denyAccessUnlessGranted('view',$contact);
    return $this->render("contact/view.html.twig",['contact' => $contact]);
  }

  /**
   * List all of my contacts
   * @Route("/list", name="contact_list")
   *
   * @param PaginatorInterface $paginator
   * @param Request $request
   * @return void
   */
  public function list(PaginatorInterface $paginator,Request $request) {
    /** @var User $user */
    $user = $this->getUser();

    $query = $user->getCreatedContacts();
    $contacts = $paginator->paginate(
        $query,
        $request->query->getInt('page', 1),
        10
    );
    return $this->render("contact/list.html.twig",['contacts' => $contacts]);
  }

  /**
   * Delete one of my contact
   * @Route("/delete", name="contact_delete")
   *
   * @param ContactRepository $contactRepository
   * @param Request $request
   * @return void
   */
  public function delete(ContactRepository $contactRepository, Request $request) {
    $token = $request->request->get("token");

    if(!$this->isCsrfTokenValid('delete-contact', $token)) {
      $this->addFlash('danger',"Invalid credentials.");
      return $this->redirectToRoute('contact_list');
    }

    $foundContact = $contactRepository->findOneBy(['id' => $request->request->get("id")]);
    if(!$foundContact) {
      $this->addFlash('danger',"Invalid credentials.");
      return $this->redirectToRoute('contact_list');
    }

    $this->denyAccessUnlessGranted('delete',$foundContact);

    $this->_em->remove($foundContact);
    $this->_em->flush();
    $this->addFlash("success","Contact successfully deleted.");

    return $this->redirectToRoute('contact_list');

  }




}