<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LiabilityItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Hipoteca' => 'mortgage',
                    'Crédito automotriz' => 'car_loan',
                    'Crédito educativo' => 'student_loan',
                    'Tarjeta de crédito' => 'credit_card',
                    'Línea de crédito' => 'line_of_credit',
                    'Préstamo personal' => 'personal_loan',
                    'Otro' => 'other',
                ],
                'label' => 'Tipo',
            ])
            ->add('label', TextType::class, [
                'label' => 'Descripción',
                'required' => false,
                'empty_data' => '',
            ])
            ->add('balance', MoneyType::class, [
                'currency' => 'MXN',
                'label' => 'Saldo',
                'empty_data' => '0',
            ])
            ->add('interest_rate', NumberType::class, [
                'label' => 'Tasa de interés %',
                'required' => false,
                'empty_data' => '0',
                'scale' => 2,
            ])
            ->add('monthly_payment', MoneyType::class, [
                'currency' => 'MXN',
                'label' => 'Pago mensual',
                'required' => false,
                'empty_data' => '0',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => null]);
    }
}
