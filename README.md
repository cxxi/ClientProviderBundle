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

**The bundle does not require any specific configuration to be used.**  

However if you are using an older version of PHP that does not support [attributes](https://www.php.net/manual/en/language.attributes.overview.php), 
you should configure your classes using a configuration file ([more information here](https://www.todo.com))

Usage
-----

This bundle adds to Symfony the management of two classes [**Provider**](https://github.com/cxxi/ClientProviderBundle?tab=readme-ov-file#provider-class) and [**ClientProvider**](https://github.com/cxxi/ClientProviderBundle?tab=readme-ov-file#client-provider-class) and provides a [**ProviderRegistry**](https://github.com/cxxi/ClientProviderBundle?tab=readme-ov-file#provider-registry) that gives more possibilities to exploit clients.

### Provider Class

Providers are abstract classes that define the logic to be implemented by clients and the logic shared by those same clients.
They group together interchangeable clients that share a defined common role. In this example, we create a payment provider.

```php
// src/Provider/PaymentProvider.php

<?php 

namespace App\Provider;

use Cxxi\ClientProviderBundle\Contracts\ProviderInterface;
use Cxxi\ClientProviderBundle\Attribute\AsProvider;

#[AsProvider('payment')]
abstract class PaymentProvider implements ProviderInterface
{
	// Example method must be implemented by all provider's clients
	abstract public function makePayment(): bool;

	// Example method available for all provider's clients
	protected function getProduct(): Product
	{
	  // Specific code shared by all provider's clients
	}
}
```

Make command included to speed up and simplify the creation of provider class :

```bash
php bin/console make:provider payment
```

### Client Provider Class

Clients are specific implementations that share a common role within the application, they extend from the provider class representing the logic that is implemented. 
You can have multiple clients that inherit from the same provider and each client should be designed to be interchangeable.

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
	  // Specific code related to Stripe
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
	  // Specific code related to Adyen
	}
}
```

Make command included to speed up and simplify the creation of client provider class :

```bash
php bin/console make:provider:client stripe
```

### Provider Registry

```php
use Cxxi\ClientProviderBundle\Contracts\ProviderRegistryInterface;

public function __construct(
    private ProviderRegistryInterface $providerRegistry
){}
```

Alternatively you can use named injection to autowire the ProviderRegistry with a definite provider type :


```php
use Cxxi\ClientProviderBundle\Contracts\ProviderRegistryInterface;

public function __construct(
    private ProviderRegistryInterface $paymentProviderRegistry
){}
```

### Standalone Client

A client can be standalone and therefore not depend on a specific provider. 
This allows to keep the autowiring logic and benefit from the ProviderRegistry framework without depending on a Provider class in the situation where the client will not have an interchangeable competitor.

```php
// src/Provider/Client/Discord.php

<?php

namespace App\Provider\Client;

use Cxxi\ClientProviderBundle\Attribute\AsClientProvider;

#[AsClientProvider('discord', standalone: true)]
class Discord implements ProviderInterface
{

}
```

and inject as :

```php
use Cxxi\ClientProviderBundle\Contracts\ProviderInterface;

public function __construct(
    private ProviderInterface $discordProvider
){}
```

### Learn more

Read more about the usage of the clientProviderBundle.

- [Attributes Reference](https://www.todo.com)
- [ProviderRegistry Reference](https://www.todo.com)
- [Use without attribute](https://www.todo.com)

### TODO

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
