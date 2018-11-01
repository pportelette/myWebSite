<?php

namespace PP\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class UserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username',   TextType::class)
            ->add('password',   TextType::class)
            ->add('name',       TextType::class)
            ->add('surname',    TextType::class)
            ->add('birthDate',  DateType::class, array(
                'years' => range(1980, 2000)
            ))
            ->add('phone',      TextType::class)
            ->add('email',      TextType::class)
            ->add('city',       TextType::class)
            ->add('country',    TextType::class)
            ->add('save',       SubmitType::class)
        ;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PP\UserBundle\Entity\User'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'pp_userbundle_user';
    }


}
