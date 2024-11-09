<?= "<?php\n"; ?>

namespace <?= $namespace; ?>;

<?= $use_statements; ?>

#[AsProvider(name: '<?= $provider_name; ?>')]
abstract class <?= $class_name; ?> implements ProviderInterface
{
    abstract public function methodThatMustBeImplementedByTheClient(): void;

    protected function methodThatIsAccessibleFromTheClient(): void
    {

    }
}