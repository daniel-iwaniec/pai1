<?php

namespace PAI\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;

/**
 * Generates contact form.
 */
class FormType extends AbstractType
{
    /**
     * Builds form.
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'email',
                [
                    'label' => 'Email',
                    'required' => true,
                    'constraints' => [
                        new NotBlank,
                        new Email
                    ]
                ]
            )
            ->add('name', 'text',
                [
                    'label' => 'Name',
                    'required' => true,
                    'constraints' => [
                        new NotBlank,
                        new Length(['min' => 4, 'max' => 20])
                    ]
                ]
            )
            ->add('surname', 'text',
                [
                    'label' => 'Surname',
                    'required' => true,
                    'constraints' => [
                        new NotBlank,
                        new Length(['min' => 4, 'max' => 50])
                    ]
                ]
            )
            ->add('submit', 'submit', ['label' => 'Submit']);
    }

    /**
     * Returns form name.
     *
     * @return string
     */
    public function getName()
    {
        return 'pai_form';
    }
}
