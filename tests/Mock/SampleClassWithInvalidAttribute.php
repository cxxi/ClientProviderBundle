<?php declare(strict_types=1);

namespace Cxxi\ClientProviderBundle\Tests\Mock;

use Cxxi\ClientProviderBundle\Tests\TestConstants;

#[SampleInvalidAttribute(name: TestConstants::MY_INVALID_ATTRIBUTE_NAME)]
class SampleClassWithInvalidAttribute {}