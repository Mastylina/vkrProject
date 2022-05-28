<?php

namespace App\Form;

use App\Entity\Reservation;
use App\Entity\Service;
use App\Entity\User;
use App\Entity\Worker;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InfoReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('service',TextType::class, [
                'attr' => ['readonly' => true],
                'label' => 'Сервис',
            ])
            ->add('worker', TextType::class, [
                'attr' => ['readonly' => true],
                'label' => 'Мастер',
            ])
            ->add('dateReservation', DateType::class, [
                'format' => 'yyyy-MM-dd',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
