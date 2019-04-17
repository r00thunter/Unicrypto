<?php

namespace Achse\GethJsonRpcPhpClient\JsonRpc;



interface IHttpClient
{

	/**
	 * @param string $body
	 * @return string
	 * @throws RequestFailedException
	 */
	public function post($body);

}
