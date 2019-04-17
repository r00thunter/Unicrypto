<?php

namespace Achse\GethJsonRpcPhpClient\JsonRpc;

use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Exception\RequestException;



class GuzzleClient implements IHttpClient
{

	/**
	 * @var GuzzleHttpClient|NULL
	 */
	private $client;

	/**
	 * @var string[]
	 */
	private $options;

	/**
	 * @var GuzzleClientFactory
	 */
	private $guzzleClientFactory;



	/**
	 * @param GuzzleClientFactory $guzzleClientFactory
	 * @param string $url
	 * @param int $port
	 */
	public function __construct(GuzzleClientFactory $guzzleClientFactory, $url, $port)
	{
		$this->guzzleClientFactory = $guzzleClientFactory;

		$this->options = [
			'base_uri' => sprintf('%s:%d', $url, $port),
		];
	}



	/**
	 * @inheritdoc
	 */
	public function post($body)
	{
		try {
			$this->openClient();
			$response = $this->client->post('', ['body' => $body, 'headers' => ['Content-Type' => 'application/json']]);
		} catch (RequestException $exception) {
			throw new RequestFailedException(
				sprintf('Request failed due to Guzzle exception: "%s".', $exception->getMessage()),
				$exception->getCode(),
				$exception
			);
		}

		return $response->getBody()->getContents();
	}



	private function openClient()
	{
		if ($this->client === NULL) {
			$this->client = $this->guzzleClientFactory->create($this->options);
		}
	}

}
