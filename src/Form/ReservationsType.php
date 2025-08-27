<?php

namespace App\Form;

use App\Entity\Room;
use App\Entity\User;
use App\Entity\Reservations;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

class ReservationsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startAt', DateTimeType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'constraints' => [
                    new GreaterThanOrEqual([ 
                    'value' => 'now', // refuse une date passée même si l’utilisateur modifie le HTML
                    ]),
                ],
                'attr' => [
                'min' => (new \DateTime())->format('Y-m-d\TH:i'),  // pour empêcher la sélection passée côté navigateur
                    ],
            ])
            ->add('endAt', DateTimeType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'constraints' => [
                    new GreaterThanOrEqual([ 
                    'value' => 'now', // refuse une date passée même si l’utilisateur modifie le HTML
                    ]),
                ],
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
            ->add('reservationStatus')
            // ->add('userId', EntityType::class, [
            //     'class' => User::class,
            //     'choice_label' => 'firstname',
            // ])
            ->add('roomId', EntityType::class, [
                'class' => Room::class,
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
