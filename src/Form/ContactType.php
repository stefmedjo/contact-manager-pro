<?php

namespace App\Form;

use App\Entity\Contact;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fname', TextType::class,[
              'attr' => ['class' => 'form-control']
            ])
            ->add('lname')
            ->add('phone', TextType::class,[
              'attr' => ['class' => 'form-control'],
              'constraints' => [
                new NotBlank(['message' => "You need to provide a phone."])
              ]
            ])
            ->add('email', EmailType::class,[
              'attr' => ['class' => 'form-control'],
              'constraints' => [
                new NotBlank(['message' => "You need to provide an email."])
              ]
            ])
            ->add('category', EntityType::class, [
              'attr' => ['class' => 'form-control'],
              'class' => Category::class,
              'choice_label' => 'designation',
              'query_builder' => function (EntityRepository $er) use ($options) {
                  return $er->createQueryBuilder('u')
                      ->where('u.createdBy = :user')
                      ->setParameter("user", $options['user'])
                      ;
              }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
            ''
        ]);
    }
}
