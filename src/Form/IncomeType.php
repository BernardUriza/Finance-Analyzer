<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IncomeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $moneyOpts = ['currency' => 'MXN', 'required' => false, 'empty_data' => '0'];

        $builder
            ->add('employment', MoneyType::class, $moneyOpts + ['label' => 'Ingreso por empleo'])
            ->add('investments', MoneyType::class, $moneyOpts + ['label' => 'Ingreso por inversiones'])
            ->add('other', MoneyType::class, $moneyOpts + ['label' => 'Otros ingresos']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => null]);
    }
}
