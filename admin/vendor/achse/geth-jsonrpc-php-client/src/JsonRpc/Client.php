<?php

namespace Achse\GethJsonRpcPhpClient\JsonRpc;

use Nette\SmartObject;
use Nette\Utils\Json;
use stdClass;



class Client
{

	use SmartObject;

	const JSON_RPC_VERSION = '2.0';

	/**
	 * @var string
	 */
	private $jsonRpcVersion;

	/**
	 * @var IHttpClient
	 */
	private $client;

	/**
	 * @var int
	 */
	private $id;



	/**
	 * @param IHttpClient $client
	 * @param string $jsonRpcVersion
	 */
	public function __construct(IHttpClient $client, $jsonRpcVersion = self::JSON_RPC_VERSION)
	{
		$this->client = $client;
		$this->jsonRpcVersion = $jsonRpcVersion;
		$this->id = 1;
	}



	/**
	 * @param string $method
	 * @param array $params
	 * @return string
	 * @throws RequestFailedException
	 */
	public function callMethod($method, array $params)
	{
		$request = [
			'jsonrpc' => $this->jsonRpcVersion,
			'method' => $method,
			'params' => array_values($params),
			'id' => $this->id,
		];

		$body = Json::encode($request);
		$rawResponse = $this->client->post($body);

		/** @var stdClass $response */
		$response = Json::decode($rawResponse);

		if ($response->id !== $this->id) {
			throw new RequestFailedException(
				sprintf('Given ID %d, differs from expected %d', $response->id, $this->id)
			);
		}

		return $response;
	}

}
