<?php

namespace PP\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ProfessionalType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startDate', DateType::class, array(
                'years' => range(2000, 2020)
            ))
            ->add('endDate', DateType::class, array(
                'years' => range(2000, 2020)
            ))
            ->add('city', TextType::class)
            ->add('country', TextType::class)
            ->add('description', TextAreaType::class)
            ->add('companyName', TextType::class)
            ->add('position', TextType::class)
            ->add('validate', SubmitType::class);
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PP\CoreBundle\Entity\Professional'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'pp_corebundle_professional';
    }


}
