<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PhysicalAssetItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Vehículo' => 'vehicle',
                    'Inmueble' => 'real_estate',
                    'Electrónicos' => 'electronics',
                    'Muebles' => 'furniture',
                    'Otro' => 'other',
                ],
                'label' => 'Tipo',
            ])
            ->add('label', TextType::class, [
                'label' => 'Descripción',
                'required' => false,
                'empty_data' => '',
            ])
            ->add('current_value', MoneyType::class, [
                'currency' => 'MXN',
                'label' => 'Valor actual',
                'empty_data' => '0',
            ])
            ->add('annual_depreciation_rate', NumberType::class, [
                'label' => 'Depreciación anual %',
                'required' => false,
                'empty_data' => '0',
                'scale' => 2,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => null]);
    }
}
