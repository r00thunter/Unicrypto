[![Downloads this Month](https://img.shields.io/packagist/dm/achse/geth-jsonrpc-php-client.svg)](https://packagist.org/packages/achse/geth-jsonrpc-php-client)
[![Latest Stable Version](https://poser.pugx.org/achse/geth-jsonrpc-php-client/v/stable)](https://github.com/achse/geth-jsonrpc-php-client/releases)
[![Build Status](https://travis-ci.org/Achse/geth-jsonrpc-php-client.svg?branch=master)](https://travis-ci.org/Achse/geth-jsonrpc-php-client)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Achse/geth-jsonrpc-php-client/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Achse/geth-jsonrpc-php-client/?branch=master)
[![Coverage Status](https://coveralls.io/repos/github/Achse/geth-jsonrpc-php-client/badge.svg?branch=master)](https://coveralls.io/github/Achse/geth-jsonrpc-php-client?branch=master)

# Introduction
This API client lib is used to communicate with `geth` (go-ethereum) node.

Last Updated: 01/04/2018 (works perfect with last geth-1.7.3-4bb3c89d).

Via this client lib you can easily run operation on the node such is:
* Get account balance,
* sign transactions,
* deploy transactions,
* ...

Full documentation of all methods that can be run on `geth` node are
described here: https://github.com/ethereum/wiki/wiki/JSON-RPC#json-rpc-methods


# Install
```
composer require achse/geth-jsonrpc-php-client
```

# Usage
```php
// Create HTTP client instance (you can use something simplier just wrap it by using IHttpClient interface)
// Create JsonRpc client which can run any operation on your geth node
$httpClient = new GuzzleClient(new GuzzleClientFactory(), 'localhost', 8545);
$client = new Client($httpClient);

// Run operation (all are described here: https://github.com/ethereum/wiki/wiki/JSON-RPC#json-rpc-methods)
$result = $client->callMethod('eth_getBalance', ['0xf99ce9c17d0b4f5dfcf663b16c95b96fd47fc8ba', 'latest']);

// $result->result ==='0x16345785d8a0000'
```
