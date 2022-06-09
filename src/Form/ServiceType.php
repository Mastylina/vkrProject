<?php

namespace App\Form;

use App\Entity\Service;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Validator\Constraints\Range;
class ServiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Название услуги',
                'constraints' => [
                    new Length([
                        'max' => 100,
                        'maxMessage' => 'Превышена максимальная длина символов'
                    ]),
                    new NotBlank([
                        'message' => 'Поле не может быть пустым',
                    ]),
                ],
            ])
            ->add('price', NumberType::class, [
                'label' => 'Цена',
                'constraints' => [
                    new Range([
                        'notInRangeMessage' => 'Значение поля должно быть в пределах от {{ min }} до {{ max }}',
                        'min' => 1,
                        'max' => 100000,
                    ]),
                    new NotBlank([
                        'message' => 'Поле не может быть пустым',
                    ]),
                ],
            ])
            ->add('executionTime')
            ->add('photo' , FileType::class, [
                'label' => 'Фотография (файл png, jpg)',

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes

            ])
            ->add('description', TextareaType::class, [
                'label' => 'Описание',
                'constraints' => [
                    new Length([
                        'max' => 1000,
                        'maxMessage' => 'Превышена максимальная длина символов'
                    ]),
                ],
            ])
            ->add('workers')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Service::class,
        ]);
    }
}
