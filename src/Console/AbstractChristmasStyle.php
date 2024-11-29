<?php

namespace App\Console;

use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class AbstractChristmasStyle extends SymfonyStyle
{
    protected array $styles = [];

    protected function defineStyles(): void
    {
        $this->styles['christmas_white'] = new OutputFormatterStyle('black', '#fff');

        foreach ($this->styles as $name => $style) {
            $this->getFormatter()->setStyle($name, $style);
        }
    }
}
