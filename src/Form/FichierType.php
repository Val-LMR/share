<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Fichier;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class FichierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [ //Nom du Doc
                'label' => 'Nom du document',
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'form-label mt-4'],
            ])
            ->add('categorie', EntityType::class, [ //Liste déroulante contenant les catégorie
                'class' => Categorie::class,
                'choice_label' => 'nom',        //La liste affiche le nom de chaque categorie
                'placeholder' => 'Choisir une catégorie',
                'label' => 'Catégorie',
                'attr' => ['class' => 'form-select'],
                'label_attr' => ['class' => 'form-label mt-4'],
            ])
            ->add('fichier', FileType::class, [ //Fichier à déposer ici 
                'label' => 'Fichier',
                'mapped' => false,
                'required' => true,
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'form-label mt-4'],
                'constraints' => [
                    new NotBlank(message: 'Veuillez choisir un fichier.'),
                    new File(
                        maxSize: '5M',
                        mimeTypes: [
                            'application/pdf',
                            'application/msword',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/zip',
                            'image/jpeg',
                            'image/png',
                            'text/plain',
                        ],
                        mimeTypesMessage: 'Format non autorisé (PDF, Word, Excel, images, zip, texte).',        //Lorsque le fichier envoyé ne respecte pas le format
                        maxSizeMessage: 'Le fichier est trop volumineux (5 Mo maximum).',       //Quand la taille du fichier dépasse les 5 Mo
                    ),
                ],
            ])
            ->add('Deposer', SubmitType::class, [       //Bouton pour le dépot
                'label' => 'Déposer',
                'attr' => ['class' => 'btn btn-primary'],
                'row_attr' => ['class' => 'text-center mt-4'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Fichier::class,
        ]);
    }
}