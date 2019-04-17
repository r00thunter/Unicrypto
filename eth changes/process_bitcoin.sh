#!/bin/sh

DIR=$(cd "$( dirname "$0" )" && pwd)

${DIR}/receive_bitcoin.php
${DIR}/send_bitcoin.php
${DIR}/receive_ethereum.php
