<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Validator\Constraints\Range;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numberPhone', TelType::class, [
                'label' => 'Номер телефона',
                'constraints' => [
                    new Length([
                        'max' => 12,
                        'maxMessage' => 'Превышена максимальная длина символов'
                    ]),
                    new NotBlank([
                        'message' => 'Поле не может быть пустым',
                    ]),
                ],
            ])
            ->add('birthdate', BirthdayType::class, [
                    'label' => 'Дата рождения',
                    'widget' => 'single_text',
                    'format' => 'yyyy-MM-dd',

                    'data' => new \DateTime()]
            )
            ->add('surname', TextType::class, [
                    'label' => 'Фамилия',
                    'constraints' => [
                        new Length([
                            'max' => 50,
                            'maxMessage' => 'Превышена максимальная длина символов'
                        ]),
                        new NotBlank([
                            'message' => 'Поле не может быть пустым',
                        ]),
                    ],
            ]

            )
            ->add('name', TextType::class, [
                    'label' => 'Имя','constraints' => [
                        new Length([
                            'max' => 50,
                            'maxMessage' => 'Превышена максимальная длина символов'
                        ]),
                        new NotBlank([
                            'message' => 'Поле не может быть пустым',
                        ]),
                    ],]
            )
            ->add('patronymic', TextType::class, [
                    'label' => 'Отчество','constraints' => [
                        new Length([
                            'max' => 50,
                            'maxMessage' => 'Превышена максимальная длина символов'
                        ]),
                        new NotBlank([
                            'message' => 'Поле не может быть пустым',
                        ]),
                    ],]
            )
            ->add('email', EmailType::class, [
                'label' => 'Электронная почта','constraints' => [
                    new Length([
                        'max' => 180,
                        'maxMessage' => 'Превышена максимальная длина символов'
                    ]),
                    new NotBlank([
                        'message' => 'Поле не может быть пустым',
                    ]),
                ],])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'label' => 'Пароль',
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Введите пароль',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Пароль не соответствует требованиям',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => 'Согласие на обработку персональных данных',
                'constraints' => [
                    new IsTrue([
                        'message' => 'Вы должны согласиться чтобы продолжить',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
