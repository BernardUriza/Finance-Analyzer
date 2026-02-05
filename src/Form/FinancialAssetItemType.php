<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FinancialAssetItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Ahorro / CETES' => 'savings',
                    'Afore' => 'afore',
                    'Fondo de inversión' => 'investment',
                    'Acciones / ETF' => 'stocks',
                    'Fondo indexado' => 'index_fund',
                    'Bonos' => 'bonds',
                    'Crypto' => 'crypto',
                    'Efectivo' => 'cash',
                    'Otro' => 'other',
                ],
                'label' => 'Tipo',
            ])
            ->add('label', TextType::class, [
                'label' => 'Descripción',
                'required' => false,
                'empty_data' => '',
            ])
            ->add('amount', MoneyType::class, [
                'currency' => 'MXN',
                'label' => 'Monto',
                'empty_data' => '0',
            ])
            ->add('rate_of_return', NumberType::class, [
                'label' => 'Rendimiento anual %',
                'required' => false,
                'empty_data' => '0',
                'scale' => 2,
            ])
            ->add('planned_duration_years', NumberType::class, [
                'label' => 'Plazo (años)',
                'required' => false,
                'empty_data' => '0',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => null]);
    }
}
