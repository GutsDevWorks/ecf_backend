<?php

namespace App\Form;

use App\Entity\Room;
use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Reservations;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;


class ReservationsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startAt', DateTimeType::class, [
                'label' => "Début de la réservation",
                'widget' => 'single_text',
                'attr' => [
                'min' => (new \DateTime())->format('Y-m-d\TH:i'),  // pour empêcher la sélection passée côté navigateur
                    ],
            ])
            ->add('endAt', DateTimeType::class, [
                'label' => "Fin de la réservation",
                'widget' => 'single_text',
                // 'constraints' => [
                //     new Assert\GreaterThan([
                //         'propertyPath' => 'startAt',
                //         'message' => 'La date de fin doit être postérieure à la date de début.'
                //     ])
                // ],
                'attr' => [
                'min' => (new \DateTime())->format('Y-m-d\TH:i'),  // pour empêcher la sélection passée côté navigateur
                    ],
            ])
            // ->add('createdAt', null, [
            //     'widget' => 'single_text'
            // ])
            // ->add('updatedAt')
            // ->add('reminderSentAt', null, [
            //     'widget' => 'single_text'
            // ])
            // ->add('validatedAt', null, [
            //     'widget' => 'single_text'
            // ])
            // ->add('reservationStatus')
            // // ->add('userId', EntityType::class, [
            // //     'class' => User::class,
            // //     'choice_label' => 'firstname',
            // // ])
            ->add('roomId', EntityType::class, [
                'class' => Room::class,
                'label' => 'Salle',
                'choice_label' => 'name',
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
