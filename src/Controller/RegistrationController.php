<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\EmailService;
use App\Entity\ActivationToken;
use App\Entity\ResetPasswordToken;
use App\Form\RegistrationFormType;
use App\Repository\ActivationTokenRepository;
use App\Repository\ResetPasswordTokenRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController {
    
    /** @var EntityManagerInterface $_em */
    private $_em;

    /** @var UsernamePasswordInterface $_encoder */
    private $_encoder;

    public function __construct(
        EntityManagerInterface $entityManagerInterface,
        UserPasswordEncoderInterface $userPasswordEncoderInterface

    )
    {
        $this->_em = $entityManagerInterface;
        $this->_encoder = $userPasswordEncoderInterface;
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(
        UserRepository $userRepository,
        EmailService $emailService,
        Request $request
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class,$user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {

            $foundUser = $userRepository->findOneBy(['email' => $user->getEmail()]);
            if($foundUser) {
              $this->addFlash('danger', "This email address is already used. Please provide another one.");
            } else {              
              $password = $this->_encoder->encodePassword($user, $user->getPassword());
              $user->setPassword($password);
              $this->_em->persist($user);
              $this->_em->flush();
  
              $activationToken = new ActivationToken();
              $activationToken->setUser($user);
              $this->_em->persist($activationToken);
              $this->_em->flush();
              
              $user->setToken($activationToken);
              $this->_em->persist($user);
              $this->_em->flush();
  
              $result = $emailService->sendEmail($user);
              if($result['success']) {
                $this->addFlash('success', "We sent you a mail for you to activate your account.");
              } else {
                $this->addFlash('danger', $result['message']);
              }
              return $this->redirectToRoute('app_register');
            }
        }
        return $this->render('registration/register.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/activate/{token}", name="activate")
     */
    public function activate($token, ActivationTokenRepository $activationTokenRepository,Request $request) {
        $foundActivationToken = $activationTokenRepository->findOneBy(['value' => $token]);
        if(!$foundActivationToken) {
            return $this->addFlash('danger',"An error occured.");
            return $this->redirectToRoute('app_login');
        } else {
            if($foundActivationToken->getUser()->getIsVerified()) {
                $this->addFlash('danger',"This account has already been verified. You can just log in.");
                return $this->redirectToRoute('app_login');
            } else {
                if($foundActivationToken->hasExpired()) {
                    $this->addFlash('danger',"This link has already expired. Click on the link below to get another one.");
                    return $this->render('registration/resend_email_activation.html.twig');
                } else {
                    $user = $foundActivationToken->getUser();
                    $user->setIsVerified(true);
                    $this->_em->persist($user);
                    $this->_em->flush();
                    return $this->redirectToRoute('app_login');
                }
            }
        }
    }

    /**
     * @Route("/resend-email-activation", name="resend_email_activation")
     */
    public function resendEmailActivation(
        UserRepository $userRepository, 
        EmailService $emailService,
        Request $request
        ) {
        $token = $request->request->get("token");
        if($this->isCsrfTokenValid('activate',$token)) {
            $email = $request->request->get("email");
            $foundUser = $userRepository->findOneBy(['email' => $email]);
            if($foundUser->getIsVerified()) {
                $this->addFlash('danger','An error occured.');
            } else {
                $emailService->sendEmail($foundUser);
                $this->addFlash('success',"The mail was successfully sent.");
            }
        } else {
            $this->addFlash('danger','An error occured.');
        }
        return $this->redirectToRoute('resend_email_activation');
    }

    /**
     * @Route("/reset-password-link", name="reset_password_link")
     */
    public function resetPasswordLink(
        UserRepository $userRepository, 
        EmailService $emailService,
        Request $request) {
        $token = $request->request->get("token");
        if($this->isCsrfTokenValid('reset',$token)) {
            $email = $request->request->get("email");
            $foundUser = $userRepository->findOneBy(['email' => $email]);
            

            $resetPasswordToken = new ResetPasswordToken();
            $resetPasswordToken->setUser($foundUser);
            $this->_em->persist($resetPasswordToken);
            $this->_em->flush();

            $foundUser->setResetPasswordToken($resetPasswordToken);
            $this->_em->persist($foundUser);
            $this->_em->flush();

            $emailService->sendEmailForPasswordReset($foundUser);
            $this->addFlash('success',"The email to reset your password was sent successfully.");
        } else {
            $this->addFlash('danger','An error occured.');
        }
        return $this->redirectToRoute('send_email_password_reset');
    }

    /**
     * @Route("/reset-password/{token}", name="reset_password")
     */
    public function resetPassword(
        $token,
        ResetPasswordTokenRepository $resetPasswordTokenRepository,
        Request $request) {
        $t = $request->request->get("token");
        if($this->isCsrfTokenValid('reset-password',$t)) {

            $resetPasswordToken = $resetPasswordTokenRepository->findOneBy(['value' => $token]);
            if(!$resetPasswordToken) {
                $this->addFlash('danger','An error occured.');
            } elseif (!$resetPasswordToken->getIsActive()) {
                $this->addFlash('danger','Passwords submitted are different.');
            } else {
                $password_1 = $request->request->get("password_1");
                $password_2 = $request->request->get("password_2");
    
                if($password_1 != $password_2) {
                    $this->addFlash('danger','Passwords submitted are different.');
                } elseif(strlen($password_1) < 6) {
                    $this->addFlash('danger','Your password need to have more than 6 characters.');
                } else {
                    $user = $resetPasswordToken->getUser();
                    $user->setPassword($this->_encoder->encodePassword($user,$password_1));
                    $this->_em->persist($user);
                    $this->_em->flush();

                    $resetPasswordToken->setIsActive(false);
                    $this->_em->persist($resetPasswordToken);
                    $this->_em->flush();

                    return $this->redirectToRoute('app_login');
                }
            }

            
        } else {
            $this->addFlash('danger','An error occured.');
        }
        return $this->redirectToRoute('reset_password',['token' => $token]);
    }

    /**
     * @Route("/send-email-password-reset", name="send_email_password_reset")
     *
     * @return void
     */
    public function sendEmailPasswordReset() {
        return $this->render('registration/send_email_password_reset.html.twig',[

        ]);
    }

}