<?php

namespace App\Voter;

use App\Entity\User;
use App\Entity\Category;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class CategoryVoter extends Voter {

   const VIEW = 'view';
   const EDIT = 'edit';

   protected function supports(string $attribute, $subject): bool
   {

       if (!in_array($attribute, [self::VIEW, self::EDIT])) {
           return false;
       }

       if (!$subject instanceof Category) {
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

       /** @var Category $contact */
       $category = $subject;

       switch ($attribute) {
           case self::VIEW:
               return $this->canView($category, $user);
           case self::EDIT:
               return $this->canEdit($category, $user);
       }

       throw new \LogicException('An error occured.');
   }

   private function canView(Category $category, User $user): bool
   {
       return $category->getCreatedBy() === $user;
   }

   private function canEdit(Category $category, User $user): bool
   {
      return $category->getCreatedBy() === $user;
   }

}