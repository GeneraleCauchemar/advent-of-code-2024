<?php

namespace App\Service;

class MatrixHelper
{
    public static function rotateMatrix(array &$matrix): void
    {
        // Effectively, this NULL callback loops through all the arrays in parallel taking each
        // value from them in turn to build a new array:
        // @see https://stackoverflow.com/a/30082922
        $matrix = array_map(null, ...$matrix);
    }
}
