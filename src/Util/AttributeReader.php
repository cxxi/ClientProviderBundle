<?php declare(strict_types=1);

namespace Cxxi\ClientProviderBundle\Util;

use Cxxi\ClientProviderBundle\Exception\ProviderAttributeException;
use Cxxi\ClientProviderBundle\Contracts\ProviderAttributeInterface;

class AttributeReader
{
    public static function get(\ReflectionClass $reflector, ?string $attributeClassName = null, ?string $property = null): ProviderAttributeInterface|array|string
    {
        $attributes = $reflector->getAttributes();

        if (is_null($attributeClassName)) return $attributes;

        if (empty($attributes)) {
            throw new ProviderAttributeException($attributeClassName, $reflector->getName());
        }

        $targetAttribute = array_values(array_filter($attributes, function(\ReflectionAttribute $attribute) use ($attributeClassName) {
            return $attribute->getName() === $attributeClassName;
        }))[0] ?? null;

        if (is_null($targetAttribute)) {
            throw new ProviderAttributeException($attributeClassName, $reflector->getName());
        }

        $attributeInstance = $targetAttribute->newInstance();

        if (!$attributeInstance instanceof ProviderAttributeInterface) {
            throw new \InvalidArgumentException(sprintf('Attribute "%s" does not implement "%s".', $attributeClassName, ProviderAttributeInterface::class));
        }

        if (is_null($property)) {
            return $attributeInstance;
        }

        if (!in_array(sprintf('get%s', ucfirst($property)), get_class_methods($attributeInstance))) {
            throw new \InvalidArgumentException(sprintf('Property "%s" of Attribute "%s" does not exists.', $property, $attributeClassName));
        }

        return $attributeInstance->{sprintf('get%s', ucfirst($property))}();
    }
}