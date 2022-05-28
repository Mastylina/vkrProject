<?php

namespace App\Form;

use App\Entity\Reservation;
use App\Entity\Service;
use App\Entity\User;
use App\Entity\Worker;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('service', EntityType::class, [
                'class' => Service::class,
                'label' => 'Выберите услугу'
            ])
            ->add('worker', EntityType::class, [
                'class' => Worker::class,
                'label' => 'выберите Мастера'
            ])
            ->add('dateReservation', DateType::class, [
                'widget'=> 'choice',
                'format' => 'yyyy-MM-dd',
                'data' => new \DateTime()
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
