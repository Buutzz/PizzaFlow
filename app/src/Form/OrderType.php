<?php

namespace App\Form;

use App\Entity\Order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('pizzaType', ChoiceType::class, [
                'choices' => [
                    'Margherita' => 'Margherita',
                    'Pepperoni' => 'Pepperoni',
                    'Capricciosa' => 'Capricciosa',
                    'Hawaiian' => 'Hawaiian',
                    'Diavola' => 'Diavola',
                    'Vegetarian' => 'Vegetarian'
                ]
            ])

            ->add('quantity', IntegerType::class)

            ->add('email', EmailType::class)

            ->add('address', TextType::class);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Order::class
        ]);
    }
}