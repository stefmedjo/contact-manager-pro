<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fname', TextType::class,[
                'attr' => ['class' => 'form-control form-item'],
                'constraints' => [
                    new NotBlank(['message' => 'Vous devez fournir votre prénom.'])
                ],
                'required' => true,
                'label' => 'Prénoms'
            ])
            ->add('lname', TextType::class,[
                'attr' => ['class' => 'form-control form-item'],
                'constraints' => [
                    new NotBlank(['message' => 'Vous devez fournir votre nom.'])
                ],
                'required' => true,
                'label' => 'Noms'
            ])
            ->add('email', EmailType::class,[
                'attr' => ['class' => 'form-control form-item'],
                'constraints' => [
                    new Email(['message' => 'Vous devez fournir votre adresse email.'])
                ],
                'required' => true,
                'label' => 'Adresse email'
            ])
            ->add('password',RepeatedType::class,[
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe ne sont pas identiques.',
                'options' => array('attr' => array('class' => 'form-control form-item')),
                'required' => true,
                'first_options'  => array('label' => 'Mot de passe'),
                'second_options' => array('label' => 'Confirmer le mot de passe'),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
