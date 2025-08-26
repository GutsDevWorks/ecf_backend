<?php

namespace App\Form;

use App\Entity\Reservations;
use App\Entity\Room;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startAt')
            ->add('endAt')
            ->add('createdAt', null, [
                'widget' => 'single_text'
            ])
            ->add('updatedAt')
            ->add('reminderSentAt', null, [
                'widget' => 'single_text'
            ])
            ->add('validatedAt', null, [
                'widget' => 'single_text'
            ])
            ->add('reservationStatus')
            ->add('userId', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('roomId', EntityType::class, [
                'class' => Room::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservations::class,
        ]);
    }
}
