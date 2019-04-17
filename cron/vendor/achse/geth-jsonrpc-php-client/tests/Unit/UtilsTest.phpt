<?php

namespace Achse\GethJsonRpcPhpClient\Tests\Utils;

require_once __DIR__ . '/../bootstrap.php';

use Achse\GethJsonRpcPhpClient\Utils;
use Tester\Assert;
use Tester\TestCase;



class ClientTest extends TestCase
{

	/**
	 * @dataProvider getDataForBigHexToBigDec
	 *
	 * @param string $expectedBigDec
	 * @param string $bigHex
	 */
	public function testBigHexToBigDec($expectedBigDec, $bigHex)
	{
		Assert::equal($expectedBigDec, Utils::bigHexToBigDec($bigHex));
	}



	/**
	 * @return array
	 */
	public function getDataForBigHexToBigDec()
	{
		return [
			['0', ''],

			['1', '1'],
			['15', 'F'],
			['16', '10'],

			['100000000000000000', '0x16345785d8a0000'],

		];
	}

}



(new ClientTest())->run();
