<?php

namespace App\Form;

use App\Entity\Options;
use App\Entity\Room;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class RoomType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Champ texte : nom de la salle
            ->add('name')

            // Champ texte : localisation (bâtiment, étage...)
            ->add('location')

            // Champ numérique : capacité de la salle
            ->add('capacity', IntegerType::class, [
                'constraints' => [
                    // Validation : valeur >= 0
                    new GreaterThanOrEqual([
                        'value'   => 0,
                        'message' => 'Veuillez saisir un nombre positif',
                    ]),
                ],
                'html5' => true, // active les contrôles HTML5 natifs (flèches ↑↓)
                'attr' => [
                    'min' => 0, // empêche la saisie de valeurs négatives côté navigateur
                ],
            ])

            // Champ texte long : description de la salle
            ->add('description')

            // Champ fichier : upload d'une photo de la salle
            ->add('photo', FileType::class, [
                'mapped' => false, // non lié directement à l'entité (traité dans le contrôleur)
                'constraints' => [
                    new Image() // Vérifie que le fichier est bien une image
                ]
            ])

            // Champ relationnel : sélection d'options disponibles pour la salle
            ->add('options', EntityType::class, [
                'class' => Options::class,       // Entité cible
                'choice_label' => 'name',        // Affiche le champ "name" des options
                'multiple' => true,              // Autorise plusieurs choix
                'expanded' => true,              // Affichage sous forme de cases à cocher
                'label' => 'Options disponibles' // Label affiché dans le formulaire
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        // L'entité Room est liée à ce formulaire
        $resolver->setDefaults([
            'data_class' => Room::class,
        ]);
    }
}
