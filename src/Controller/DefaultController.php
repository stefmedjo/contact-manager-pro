<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController {

    /**
     * @Route("/", name="home")
     *
     * @return void
     */
    public function index() {
        if($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->render('default/home.html.twig');
        } else {
            return $this->render('default/index.html.twig');
        }
    }

    /**
     * @Route("/about", name="about")
     */
    public function about() {
      return $this->render("default/about.html.twig");
    }

}