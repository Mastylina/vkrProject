<?php

namespace App\Form;

use App\Entity\Kpi;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Validator\Constraints\Range;
class KpiType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('weightVolumeSales', NumberType::class, [
                'label' => 'Вес объёма продаж',
                'constraints' => [
                    new Range([
                        'notInRangeMessage' => 'Значение поля должно быть в пределах от {{ min }} до {{ max }}',
                        'min' => 0.001,
                        'max' => 1,
                    ]),
                    new NotBlank([
                        'message' => 'Поле не может быть пустым',
                    ]),
                ],
            ])
            ->add('minVolumeSales', NumberType::class, [
                'label' => 'Минимальный объём продаж',
                'constraints' => [
                    new Range([
                        'notInRangeMessage' => 'Значение поля должно быть в пределах от {{ min }} до {{ max }}',
                        'min' => 1,
                        'max' => 1000000,
                    ]),
                    new NotBlank([
                        'message' => 'Поле не может быть пустым',
                    ]),
                ],
            ])
            ->add('plannedVolumeSales', NumberType::class, [
                'label' => 'Планируемый объём продаж',
                'constraints' => [
                    new Range([
                        'notInRangeMessage' => 'Значение поля должно быть в пределах от {{ min }} до {{ max }}',
                        'min' => 1,
                        'max' => 1000000,
                    ]),
                    new NotBlank([
                        'message' => 'Поле не может быть пустым',
                    ]),
                ],
            ])
            ->add('weightQualityService', NumberType::class, [
                'label' => 'Вес величины качества обслуживания',
                'constraints' => [
                    new Range([
                        'notInRangeMessage' => 'Значение поля должно быть в пределах от {{ min }} до {{ max }}',
                        'min' => 0.01,
                        'max' => 1,
                    ]),
                    new NotBlank([
                        'message' => 'Поле не может быть пустым',
                    ]),
                ],
            ])
            ->add('minQualityService', NumberType::class, [
                'label' => 'Минимальная величина качества обслуживания',
                'constraints' => [
                    new Range([
                        'notInRangeMessage' => 'Значение поля должно быть в пределах от {{ min }} до {{ max }}',
                        'min' => 1,
                        'max' => 1000000,
                    ]),
                    new NotBlank([
                        'message' => 'Поле не может быть пустым',
                    ]),
                ],
            ])
            ->add('plannedQualityService', NumberType::class, [
                'label' => 'Планируемая величина качества обслуживания',
                'constraints' => [
                    new Range([
                        'notInRangeMessage' => 'Значение поля должно быть в пределах от {{ min }} до {{ max }}',
                        'min' => 1,
                        'max' => 1000000,
                    ]),
                    new NotBlank([
                        'message' => 'Поле не может быть пустым',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Kpi::class,
        ]);
    }
}
