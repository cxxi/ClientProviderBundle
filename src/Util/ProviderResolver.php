<?php declare(strict_types=1);

namespace Cxxi\ClientProviderBundle\Util;

class ProviderResolver
{
	public static function findClassesWithAttribute(string $attributeClassName): array
	{
		return array_filter(get_declared_classes(), function($className) use ($attributeClassName) {

			$reflector = new \ReflectionClass($className);

            if (empty($reflector->getAttributes())) return false;

            $classAttributes = array_map(fn(\ReflectionAttribute $attribute) => $attribute->getName(), $reflector->getAttributes());
            
            if (!in_array($attributeClassName, $classAttributes)) return false;

            return true;
		});

		$classes = [];

		foreach (get_declared_classes() as $className)
        {
            $reflector = new \ReflectionClass($className);

            if (empty($reflector->getAttributes())) continue;

            $classAttributes = array_map(fn(\ReflectionAttribute $attribute) => $attribute->getName(), $reflector->getAttributes());
            
            if (!in_array($attributeClassName, $classAttributes)) continue;

		    $classes[$className] = $container->getDefinition($className);
        }

        return $classes;
	}
}