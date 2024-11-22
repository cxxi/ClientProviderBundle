<?php declare(strict_types=1);

namespace Cxxi\ClientProviderBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TmpTest extends KernelTestCase
{
	public function testTmp()
	{
		$kernel = self::bootKernel();
		$container = $kernel->getContainer();
		dd($container);
	}
}