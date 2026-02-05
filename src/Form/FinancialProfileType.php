<?php

namespace App\Form;

use App\Entity\FinancialProfile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FinancialProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Nombre del perfil'])
            ->add('age', IntegerType::class, ['label' => 'Edad'])
            ->add('income', IncomeType::class, ['label' => false])
            ->add('expenses', ExpensesType::class, ['label' => false])
            ->add('financialAssets', CollectionType::class, [
                'entry_type' => FinancialAssetItemType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => false,
                'prototype' => true,
                'prototype_name' => '__fa_name__',
            ])
            ->add('physicalAssets', CollectionType::class, [
                'entry_type' => PhysicalAssetItemType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => false,
                'prototype' => true,
                'prototype_name' => '__pa_name__',
            ])
            ->add('liabilities', CollectionType::class, [
                'entry_type' => LiabilityItemType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => false,
                'prototype' => true,
                'prototype_name' => '__li_name__',
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Guardar perfil',
                'attr' => ['class' => 'btn btn-primary btn-lg'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FinancialProfile::class,
        ]);
    }
}
