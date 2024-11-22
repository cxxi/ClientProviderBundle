<?php declare(strict_types=1);

namespace Cxxi\ClientProviderBundle\Tests\Utils;

use Cxxi\ClientProviderBundle\Util\ProviderResolver;
use Cxxi\ClientProviderBundle\Tests\TestConstants;
use Cxxi\ClientProviderBundle\Tests\Mock\SampleValidAttribute;
use Cxxi\ClientProviderBundle\Tests\Mock\SampleInvalidAttribute;
use Cxxi\ClientProviderBundle\Tests\Mock\SampleNotFoundAttribute;
use Cxxi\ClientProviderBundle\Tests\Mock\SampleClassWithValidAttribute;
use Cxxi\ClientProviderBundle\Tests\Mock\SampleClassWithInvalidAttribute;
use PHPUnit\Framework\TestCase;

class ProviderResolverTest extends TestCase
{
    public function testFindClassesWithAttributeReturnsClassesWithMatchingAttribute(): void
    {
        $result = ProviderResolver::findClassesWithAttribute(SampleValidAttribute::class);

        $this->assertContains(SampleClassWithValidAttribute::class, $result);
        $this->assertNotContains(SampleClassWithInvalidAttribute::class, $result);
    }

    public function testFindClassesWithAttributeReturnsEmptyWhenNoClassesWithAttribute(): void
    {
        $result = ProviderResolver::findClassesWithAttribute(SampleNotFoundAttribute::class);
        
        $this->assertEmpty($result);
    }

    public function testFindClassesWithAttributeReturnsCorrectClassWithSpecificAttribute(): void
    {
        $result = ProviderResolver::findClassesWithAttribute(SampleValidAttribute::class);
        
        $this->assertContains(SampleClassWithValidAttribute::class, $result);
    }

    public function testFindClassesWithAttributeHandlesEmptyClasses(): void
    {
        $result = ProviderResolver::findClassesWithAttribute(SampleValidAttribute::class);
        
        $this->assertIsArray($result);
    }

    public function testFindClassesWithAttributeReturnsEmptyWhenAttributeIsNotFound(): void
    {
        $result = ProviderResolver::findClassesWithAttribute(TestConstants::NOT_EXISTS_ATTRIBUTE);
        
        $this->assertEmpty($result);
    }
}