<?php

namespace App\Console;

use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ChristmasStyle extends AbstractChristmasStyle
{
    public function __construct(InputInterface $input, OutputInterface $output)
    {
        parent::__construct($input, $output);

        $this->styles = [
            'christmas_red'   => new OutputFormatterStyle(null, '#ff0000'),
            'christmas_green' => new OutputFormatterStyle(null, '#009930'),
        ];

        $this->defineStyles();
    }
}
