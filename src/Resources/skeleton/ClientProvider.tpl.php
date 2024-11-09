<?= "<?php\n"; ?>

namespace <?= $namespace; ?>;

<?= $use_statements; ?>

#[AsClientProvider(name: '<?= $client_provider_name; ?>')]
class <?= $class_name; ?> extends <?= $provider_class_name; ?>
{
    public function methodThatMustBeImplementedByTheClient(): void
    {

    }
}