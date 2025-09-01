<?php

namespace App\Form;

use App\Entity\Options;
use App\Entity\Room;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OptionsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Champ pour le nom de l’option (input texte)
            ->add('name')

            // Champ pour la description de l’option (textarea)
            ->add('description')

            // Champ pour le type d’option (ex: Technique, Confort...)
            ->add('type')

            // Champ relationnel : associer l’option à une ou plusieurs salles
            ->add('rooms', EntityType::class, [
                'class' => Room::class,             // Entité cible
                'choice_label' => 'name',           // Affiche le champ "name" de Room dans la liste
                'multiple' => true,                 // Sélection multiple autorisée
                'required' => false,                // Le champ n’est pas obligatoire
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        // Définit l’entité liée à ce formulaire (Options)
        $resolver->setDefaults([
            'data_class' => Options::class,
        ]);
    }
}
