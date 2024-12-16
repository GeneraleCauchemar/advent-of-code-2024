<?php

declare(strict_types=1);

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class DayType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label'       => false,
            'placeholder' => 'Day',
            'html5'       => false,
            'constraints' => [
                new Assert\Positive(),
                new Assert\GreaterThanOrEqual(['value' => 1]),
                new Assert\LessThanOrEqual(['value' => 25]),
            ],
        ]);
    }

    public function getParent(): string
    {
        return NumberType::class;
    }
}
