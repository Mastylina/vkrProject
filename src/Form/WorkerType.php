<?php

namespace App\Form;

use App\Entity\Service;
use App\Entity\Worker;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Validator\Constraints\Range;
class WorkerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
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
            ->add('salary', NumberType::class, [
                'label' => 'Оклад',
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
            ->add('post')
            ->add('services')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Worker::class,
        ]);
    }
}
