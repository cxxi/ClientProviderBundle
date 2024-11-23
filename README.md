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
    // ...
    Cxxi\ClientProviderBundle\ClientProviderBundle::class => ['all' => true],
    // ...
];
```
Configuration
-------------

**The bundle does not require any specific configuration to be used.**  

However if you are using an older version of PHP that does not support [attributes](https://www.php.net/manual/en/language.attributes.overview.php), 
you should configure your classes using a configuration file ([more information here](https://www.todo.com))

Usage
-----

This bundle enhances your Symfony application by adding the notion of multiple and/or interchangeable clients provider through two interdependent classes [**Provider**](https://github.com/cxxi/ClientProviderBundle?tab=readme-ov-file#provider-class) and [**ClientProvider**](https://github.com/cxxi/ClientProviderBundle?tab=readme-ov-file#client-provider-class) and a [**ProviderRegistry**](https://github.com/cxxi/ClientProviderBundle?tab=readme-ov-file#provider-registry) that gives more possibilities to exploit clients.

### Provider Class

Providers are abstract classes that define the logic to be implemented by ClientProviders and the logic shared by those same ClientProviders.
They group together interchangeable ClientProviders that share a defined common role. In this example, we create a payment provider.

#### Definition

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

#### Make Command

Included to speed up and simplify the creation of Provider class :

```bash
php bin/console make:provider payment
```

### Client Provider Class

Clients are specific implementations that share a common role within the application, they extend from the Provider class representing the logic that is implemented. 
You can have multiple ClientProviders that inherit from the same Provider and each ClientProvider should be designed to be interchangeable within the same Provider.

#### Definition

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

#### Make Command

Included to speed up and simplify the creation of ClientProvider class :

```bash
php bin/console make:provider:client stripe
```

#### Autowiring

To use a ClientProvider in the application, just inject your ClientProvider like this :

```php
use Cxxi\ClientProviderBundle\Contracts\ProviderInterface;

public function __construct(
    private ProviderInterface $stripePaymentProvider
){}
```

> ***GOOD TO KNOW***  
> *ClientProviderBundle use the concept of "argument name-based autowiring"*  

#### Default Client

Providers support the definition of a default ClientProvider. 
This will result in mounting the default ClientProvider when injecting the provider it depends on.  
  
If a default ClientProvider is defined :
```php
#[AsProvider(name: 'payment', default: 'stripe')]
```

The Provider can be injected like a ClientProvider :
```php
public function __construct(
    private ProviderInterface $paymentProvider
){}

// same as :

public function __construct(
    private ProviderInterface $stripePaymentProvider
){}
```

The default parameter supports mapping from services.yaml

```yaml
# config/services.yaml
parameters:
  app.default.payment_provider: stripe
```

```php
#[AsProvider(name: 'payment', default: '%app.default.payment_provider%')]
```

#### Standalone Client

A ClientProvider can be standalone and therefore not depend on a specific Provider. 
This allows to keep the autowiring logic and benefit from the ProviderRegistry framework without depending on a Provider class in the situation where the ClientProvider will not have an interchangeable competitor.

```php
// src/Provider/Client/Discord.php

<?php

namespace App\Provider\Client;

use Cxxi\ClientProviderBundle\Attribute\AsClientProvider;

#[AsClientProvider('discord', standalone: true)]
class Discord implements ProviderInterface
{
    // ...
}

// And inject as :

use Cxxi\ClientProviderBundle\Contracts\ProviderInterface;

public function __construct(
    private ProviderInterface $discordProvider
){}
```

### Provider Registry

#### Autowiring

```php
use Cxxi\ClientProviderBundle\Contracts\ProviderRegistryInterface;

public function __construct(
    private ProviderRegistryInterface $providerRegistry
){}


```

Alternatively you can use name-based autowiring to inject the ProviderRegistry with a definite Provider type :


```php
use Cxxi\ClientProviderBundle\Contracts\ProviderRegistryInterface;

public function __construct(
    private ProviderRegistryInterface $paymentProviderRegistry
){}


```

#### Overview

```php
$paymentProviderRegistry === $this->providerRegistry->use('payment');
```

```php
use Cxxi\ClientProviderBundle\Contracts\ProviderRegistryInterface;

class Example
{
    public function __construct(
        private ProviderRegistryInterface $providerRegistry
    ){}

    public function demo()
    {
        // Get ClientProvider instance from Registry
        $stripeClient = $this->providerRegistry->use('payment')->get('stripe')->getInstance();

        // Get default ClientProvider instance from Registry
        $defaultClient = $this->providerRegistry->use('payment')->getDefault();
        $defaultClient = $this->providerRegistry->getDefault('payment');

        // Lorem ipsum
        $paymentResponse = $this->providerRegistry->use('payment')
        	->get('stripe')->call('makePayment', $args);

        // Lorem ipsum
        $paymentResponse = $this->providerRegistry->use('payment')
        	->get('stripe', 'adyen')
        	->callUntilSuccess('makePayment', $args, PaymentException::class);

        // Lorem ipsum
        $paymentResponse = $this->providerRegistry->use('payment')
        	->get('stripe', 'adyen')
        	->callAndAggregate('makePayment', $args, AggregationLogicEnum::CONCAT);
    }
}

```

```php
use Cxxi\ClientProviderBundle\Contracts\ProviderRegistryInterface;

class Example
{
    public function __construct(
        private ProviderRegistryInterface $paymentProviderRegistry
    ){}

    public function demo()
    {
        // Get ClientProvider instance from Registry
        $stripeClient = $this->paymentProviderRegistry->get('stripe')->getInstance();

        // Get default ClientProvider instance from Registry
        $defaultClient = $this->paymentProviderRegistry->getDefault();

        // Lorem ipsum
        $paymentResponse = $this->paymentProviderRegistry
        	->get('stripe')->call('makePayment', $args);

        // Lorem ipsum
        $paymentResponse = $this->paymentProviderRegistry
        	->get('stripe', 'adyen')
        	->callUntilSuccess('makePayment', $args, PaymentException::class);

        // Lorem ipsum
        $paymentResponse = $this->paymentProviderRegistry
        	->get('stripe', 'adyen')
        	->callAndAggregate('makePayment', $args, AggregationLogicEnum::CONCAT);
    }
}

```

### Learn more

Read more about the usage of the ClientProviderBundle.

- [Attributes](https://www.todo.com)
- [ProviderRegistry](https://www.todo.com)
- [Use without attribute](https://www.todo.com)


Maintainers
-----------

lorem ipsum.

Credits
-------

lorem ipsum.
