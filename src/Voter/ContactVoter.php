<?php

namespace App\Voter;

use App\Entity\User;
use App\Entity\Contact;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ContactVoter extends Voter {

   const VIEW = 'view';
   const EDIT = 'edit';

   protected function supports(string $attribute, $subject): bool
   {

       if (!in_array($attribute, [self::VIEW, self::EDIT])) {
           return false;
       }

       if (!$subject instanceof Contact) {
           return false;
       }

       return true;
   }

   protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
   {
       $user = $token->getUser();

       if (!$user instanceof User) {
           return false;
       }

       /** @var Contact $contact */
       $contact = $subject;

       switch ($attribute) {
           case self::VIEW:
               return $this->canView($contact, $user);
           case self::EDIT:
               return $this->canEdit($contact, $user);
       }

       throw new \LogicException('An error occured.');
   }

   private function canView(Contact $contact, User $user): bool
   {
       return $contact->getCreatedBy() === $user;
   }

   private function canEdit(Contact $contact, User $user): bool
   {
      return $contact->getCreatedBy() === $user;
   }

}