<?= "<?php\n" ?>

namespace <?= $namespace ?>;

<?= $use_statements ?>

/**
 * ❄️ Day <?= (int) $day ?>: ... ❄️
 * @see <?= $see . PHP_EOL ?>
 */
final class <?= $class_name ?> extends AbstractConundrumSolver
{
    public function __construct()
    {
        parent::__construct('<?= $year ?>', '<?= $day ?>');
    }

    ////////////////
    // PART 1
    ////////////////

    public function partOne(): string|int
    {
        return parent::partOne();
    }

    ////////////////
    // PART 2
    ////////////////

    public function partTwo(): string|int
    {
        return parent::partTwo();
    }

    ////////////////
    // METHODS
    ////////////////

}
