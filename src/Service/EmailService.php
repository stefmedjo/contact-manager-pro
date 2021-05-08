<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\Mailer\MailerInterface;

class EmailService {

    private $_mailer;

    public function __construct(
        MailerInterface $mailer
    )
    {
        $this->_mailer = $mailer;
    }

    public function sendEmail(User $user) {
        $email = (new TemplatedEmail())
            ->from('noreply@lullabike.com')
            ->to($user->getEmail())
            ->subject("Activation de votre compte")
            ->htmlTemplate('registration/email_activation.html.twig')
            ->context([
                'user' => $user
            ])
            ;
        try{
          $this->_mailer->send($email);
          return ['success' => true];
        } catch(TransportException $exception) {
          return ['success' => false, "message" => $exception->getMessage()];
        }
    }

    public function sendEmailForPasswordReset(User $user) {
        $email = (new TemplatedEmail())
            ->from('noreply@lullabike.com')
            ->to($user->getEmail())
            ->subject("Réinitialisation de votre mot de passe.")
            ->htmlTemplate('registration/password_reinit.html.twig',[
                'user' => $user
            ])
            ;
        $this->_mailer->send($email);
    }

}