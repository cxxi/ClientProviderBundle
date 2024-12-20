<?php declare(strict_types=1);

namespace Cxxi\ClientProviderBundle\Registry;

use Cxxi\ClientProviderBundle\Registry\Model\ClientProviderModel;
use Cxxi\ClientProviderBundle\Registry\Model\ProviderTypeModel;
use Cxxi\ClientProviderBundle\Contracts\ProviderRegistryInterface;
use Cxxi\ClientProviderBundle\Exception\ClientProviderTypeException;
use Cxxi\ClientProviderBundle\Exception\ProviderTypeException;
use Cxxi\ClientProviderBundle\Contracts\ProviderInterface;
use Cxxi\ClientProviderBundle\Enum\AggregationLogicEnum;
use Symfony\Component\VarExporter\LazyObjectInterface;

final class ProviderRegistry implements ProviderRegistryInterface
{
	private readonly array $providerTypes;
	private readonly array $clients;
	private ?string $currentType = null;
	private array $accessibleClients = [];

	public function __construct(array $clients, ?string $providerType = null)
	{
		$this->buildRegistryModels($clients);
		$this->setCurrentType($providerType);
	}

	public function use(?string $providerType): self
	{
		$this->setCurrentType($providerType);

		return $this;
	}

	public function get(string $clientName): ProviderInterface
	{
		if (!in_array($clientName, array_keys($this->accessibleClients))) {
			throw new ClientProviderTypeException($clientName, $this->accessibleClients, $this->currentType);
		}

		return $this->accessibleClients[$clientName];
	}

	public function getDefault(): ProviderInterface
	{
		// if (is_null($currentType)) {
		// 	throw new \Exception('Any providerType was set on registry'); // TODO
		// }

		// $clientReflection = new ReflectionClass($client);
		// $asProvider = AttributeReader::get($clientReflection->getParentClass(), AsProvider::class);

		// return;
	}

	public function hasProviderType(string $providerType): bool
	{
		return in_array($providerType, array_keys($this->providerTypes));
	}

	public function getCurrentType(): ?string
	{
		return $this->currentType;
	}

	public function call(string $method, string $client): mixed
	{

	}

	public function callWithFallback(string $method, ?string $exceptionClassExpected = null): mixed
	{

	}

	public function callUntilSuccess(string $method, array $clients, ?string $exceptionClassExpected = null): mixed
	{

	}

	public function callAndAggregate(string $method, array $clients, AggregationLogicEnum $aggregationLogic = AggregationLogicEnum::CONCAT): mixed
	{

	}

    private function setCurrentType(?string $providerType = null): self
    {
    	if (!is_null($providerType) && !in_array($providerType, array_keys($this->providerTypes))) {
			throw new ProviderTypeException($providerType, $this->providerTypes);
		}

        $this->currentType = $providerType;
        $this->updateAccessibleClients($providerType);

        return $this;
    }

    private function updateAccessibleClients(?string $providerType = null): void
    {
    	$this->accessibleClients = array_reduce($this->clients, function($carry, $client) use ($providerType) {

		    if (is_null($providerType) || $client->getProviderType() === $providerType) {
		        $carry[$client->getName()] = $client->getInstance();
		    }

		    return $carry;
		
		}, []);
	}

	private function buildRegistryModels(array $clients): void
	{
		$clientObjects = $providerTypeObjects = [];

		foreach($clients as $client)
		{
			$reflector = new \ReflectionClass($client);

			if ($reflector->implementsInterface(LazyObjectInterface::class)) {
				$reflector = $reflector->getParentClass();
			}

			$providerClass = $reflector->getParentClass();

			if ($providerClass !== false) {
				$providerTypeModel = new ProviderTypeModel($providerClass);
				if (!isset($providerTypeModels[$providerTypeModel->getName()])) {
					$providerTypeModels[$providerTypeModel->getName()] = $providerTypeModel;
				}
			}

			$clientModels[] = new ClientProviderModel($client);			
		}

		$this->providerTypes = $providerTypeModels;
		$this->clients = $clientModels;
	}
}