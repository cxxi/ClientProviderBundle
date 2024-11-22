<?php declare(strict_types=1);

namespace Cxxi\ClientProviderBundle\Tests\Utils;

use Cxxi\ClientProviderBundle\Util\AttributeReader;
use Cxxi\ClientProviderBundle\Exception\ProviderAttributeException;
use Cxxi\ClientProviderBundle\Contracts\ProviderAttributeInterface;
use Cxxi\ClientProviderBundle\Tests\TestConstants;
use Cxxi\ClientProviderBundle\Tests\Mock\SampleClassWithAttribute;
use Cxxi\ClientProviderBundle\Tests\Mock\SampleInvalidAttribute;
use Cxxi\ClientProviderBundle\Tests\Mock\SampleValidAttribute;
use PHPUnit\Framework\TestCase;

class AttributeReaderTest extends TestCase
{
    public function testGetThrowsExceptionWhenAttributeNotFound(): void
    {
        $reflector = new \ReflectionClass(SampleClassWithAttribute::class);

        $this->expectException(ProviderAttributeException::class);

        AttributeReader::get($reflector, TestConstants::NOT_EXISTS_ATTRIBUTE);
    }

    public function testGetThrowsExceptionWhenAttributeDoesNotImplementInterface(): void
    {
        $reflector = new \ReflectionClass(SampleClassWithAttribute::class);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('Attribute "%s" does not implement "%s".', SampleInvalidAttribute::class, ProviderAttributeInterface::class));

        AttributeReader::get($reflector, SampleInvalidAttribute::class);
    }

    public function testGetReturnsAllAttributesWhenNoClassNameProvided(): void
    {
        $reflector = new \ReflectionClass(SampleClassWithAttribute::class);

        $result = AttributeReader::get($reflector);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
    }

    public function testGetReturnsAttributeWhenFoundAndValid(): void
    {
        $reflector = new \ReflectionClass(SampleClassWithAttribute::class);

        $result = AttributeReader::get($reflector, SampleValidAttribute::class);
        
        $this->assertInstanceOf(ProviderAttributeInterface::class, $result);
        $this->assertEquals(TestConstants::MY_VALID_ATTRIBUTE_NAME, $result->getName());
    }

    public function testGetThrowsExceptionWhenPropertyNotFound(): void
    {
        $reflector = new \ReflectionClass(SampleClassWithAttribute::class);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('Property "%s" of Attribute "%s" does not exists.', TestConstants::NOT_EXISTS_PROPERTY, SampleValidAttribute::class));

        AttributeReader::get($reflector, SampleValidAttribute::class, TestConstants::NOT_EXISTS_PROPERTY);
    }

    public function testGetReturnsPropertyFromAttribute(): void
    {
        $reflector = new \ReflectionClass(SampleClassWithAttribute::class);

        $result = AttributeReader::get($reflector, SampleValidAttribute::class, 'name');

        $this->assertEquals(TestConstants::MY_VALID_ATTRIBUTE_NAME, $result);
    }
}