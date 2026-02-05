<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExpensesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $moneyOpts = ['currency' => 'MXN', 'required' => false, 'empty_data' => '0'];

        $builder
            ->add('housing', MoneyType::class, $moneyOpts + ['label' => 'Vivienda (renta/hipoteca)'])
            ->add('transportation', MoneyType::class, $moneyOpts + ['label' => 'Transporte'])
            ->add('food', MoneyType::class, $moneyOpts + ['label' => 'Comida y despensa'])
            ->add('utilities', MoneyType::class, $moneyOpts + ['label' => 'Servicios (luz, agua, gas)'])
            ->add('insurance', MoneyType::class, $moneyOpts + ['label' => 'Seguros'])
            ->add('healthcare', MoneyType::class, $moneyOpts + ['label' => 'Salud'])
            ->add('entertainment', MoneyType::class, $moneyOpts + ['label' => 'Entretenimiento'])
            ->add('personal', MoneyType::class, $moneyOpts + ['label' => 'Gastos personales'])
            ->add('other', MoneyType::class, $moneyOpts + ['label' => 'Otros']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => null]);
    }
}
