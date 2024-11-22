ClientProviderBundle
===============

![Build Status](https://github.com/cxxi/ClientProviderBundle/actions/workflows/ci.yaml/badge.svg)
[![Latest Stable Version](http://poser.pugx.org/cxxi/ClientProviderBundle/v)](https://packagist.org/packages/cxxi/ClientProviderBundle) [![Total Downloads](http://poser.pugx.org/cxxi/ClientProviderBundle/downloads)](https://packagist.org/packages/cxxi/ClientProviderBundle) [![Latest Unstable Version](http://poser.pugx.org/cxxi/ClientProviderBundle/v/unstable)](https://packagist.org/packages/cxxi/ClientProviderBundle) [![License](http://poser.pugx.org/cxxi/ClientProviderBundle/license)](https://packagist.org/packages/cxxi/ClientProviderBundle) [![PHP Version Require](http://poser.pugx.org/cxxi/ClientProviderBundle/require/php)](https://packagist.org/packages/cxxi/ClientProviderBundle)

Client provider abstraction integration for Symfony.

Installation
------------

With [composer](https://getcomposer.org), require:

```bash
composer require cxxi/client-provider-bundle
```

If you are not using Flex, enable it in your kernel :

```php
// config/bundles.php
<?php

return [
    //...
    Cxxi\ClientProviderBundle\ClientProviderBundle::class => ['all' => true],
    //...
];
```
Configuration
-------------

lorem ipsum.

Usage
-----

lorem ipsum.

```php
// src/Provider/PaymentProvider.php

<?php 

namespace App\Provider;

use Cxxi\ClientProviderBundle\Contracts\ProviderInterface;
use Cxxi\ClientProviderBundle\Attribute\AsProvider;

#[AsProvider('payment')]
abstract class PaymentProvider implements ProviderInterface
{
	// Example method must be implemented by all clients
	abstract public function makePayment(): bool;

	// Example method available for all clients
	protected function getProduct(): Product;
}
```

```bash
php bin/console make:provider payment
```


```php
// src/Provider/Client/Stripe.php

<?php

namespace App\Provider\Client;

use Cxxi\ClientProviderBundle\Attribute\AsClientProvider;
use App\Provider\PaymentProvider;


#[AsClientProvider('stripe')]
class Stripe extends PaymentProvider
{
	public function makePayment(): bool
	{
		// code
	}
}
```

```php
// src/Provider/Client/Adyen.php

<?php

namespace App\Provider\Client;

use Cxxi\ClientProviderBundle\Attribute\AsClientProvider;
use App\Provider\PaymentProvider;


#[AsClientProvider('adyen')]
class Adyen extends PaymentProvider
{
	public function makePayment(): bool
	{
		// code
	}
}
```

```bash
php bin/console make:provider:client stripe
```

```php
#[AsProvider(name: 'payment', default: 'stripe')]
```

```yaml
# config/services.yaml
parameters:
  app.default.payment_provider: stripe
```

```php
#[AsProvider(name: 'payment', default: '%app.default.payment_provider%')]
```

```php
#[AsClientProvider(name: 'adyen', standalone: true)]
```

```php
use Cxxi\ClientProviderBundle\Contracts\ProviderInterface;

public function __construct(
    private ProviderInterface $stripePaymentProvider
){}
```

```php
use Cxxi\ClientProviderBundle\Contracts\ProviderInterface;

public function __construct(
	private ProviderInterface $paymentProvider
){}
```

```php
use Cxxi\ClientProviderBundle\Contracts\ProviderRegistryInterface;

public function __construct(
    private ProviderRegistryInterface $providerRegistry
){}
```

```php
use Cxxi\ClientProviderBundle\Contracts\ProviderRegistryInterface;

public function __construct(
    private ProviderRegistryInterface $paymentProviderRegistry
){}
```

```php

$stripeClient = $providerRegistry->get('stripe');
$stripeClient = $paymentProviderRegistry->get('stripe');

$defaultClient = $providerRegistry->use('payment')->getDefault();
$defaultClient = $providerRegistry->getDefault('payment');
$defaultClient = $paymentProviderRegistry->getDefault();

$providerRegistry->hasProviderType('payment'); // true

$providerRegistry->use('payment')->getCurrentType(); // 'payment'
$paymentProviderRegistry->getCurrentType(); // 'payment'

// ----

$providerRegistry->use('payment', 'stripe');
$paymentProviderRegistry->use('stripe');

$providerRegistry->use('payment', 'stripe')->call('makePayment');
$paymentProviderRegistry->use('stripe')->call('makePayment');

$providerRegistry
	->use('payment')
	->callWithFallback('makePayment', PaymentException::class);

$paymentProviderRegistry
	->use('stripe')
	->callWithFallback('makePayment', PaymentException::class);

$providerRegistry
	->use('payment')
	->callUntilSuccess('makePayment', ['stripe', 'adyen'], PaymentException::class);

$paymentProviderRegistry
	->callUntilSuccess('makePayment', ['stripe', 'adyen'], PaymentException::class);

$providerRegistry
	->use('payment')
	->callAndAggregate('makePayment', ['stripe', 'adyen'], AggregationLogicEnum::CONCAT);

$paymentProviderRegistry
	->callAndAggregate('makePayment', ['stripe', 'adyen'], AggregationLogicEnum::CONCAT);

```

Maintainers
-----------

lorem ipsum.

Credits
-------

lorem ipsum.
