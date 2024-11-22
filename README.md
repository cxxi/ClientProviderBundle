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
	abstract public function makePayment(): bool;
}
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

Maintainers
-----------

lorem ipsum.

Credits
-------

lorem ipsum.
