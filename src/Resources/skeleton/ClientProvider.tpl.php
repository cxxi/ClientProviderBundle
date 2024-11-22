<?= "<?php\n"; ?>

namespace <?= $namespace; ?>;

<?= $use_statements; ?>

<?= $attribute."\n"; ?>
class <?= $class_name; ?> <?= $ancestor; ?>
{
    public function methodThatMustBeImplementedByTheClient(): void
    {

    }
}