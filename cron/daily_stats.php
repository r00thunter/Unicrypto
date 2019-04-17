#!/usr/bin/php
<?php
include 'common.php';
echo date('Y-m-d H:i:s').' Beginning Daily Report processing...'.PHP_EOL;

/* should run at the very start of every day */

$main = Currencies::getMain();
$cryptos = Currencies::getCryptos();
$wallets = Wallets::get();

// compile historical data
if ($wallets) {
	foreach ($wallets as $wallet) {
		$sql = "INSERT IGNORE INTO historical_data (`date`,usd,c_currency) (SELECT '".(date('Y-m-d',strtotime('-1 day')))."',(transactions.btc_price * currencies.usd_ask) AS btc_price, transactions.c_currency FROM transactions LEFT JOIN currencies ON (transactions.currency = currencies.id) WHERE transactions.c_currency = ".$wallet['c_currency']." AND transactions.date <= (CURDATE()) ORDER BY transactions.date DESC LIMIT 0,1) ";
		$result = db_query($sql);
	}
}

// get total of each currency
$sql = 'SELECT COUNT(DISTINCT site_users.id) AS total_users, SUM(IF(site_users_balances.currency IN ('.implode(',',$cryptos).'),site_users_balances.balance * currencies.usd_ask,0)) AS btc, SUM(IF(site_users_balances.currency NOT IN ('.implode(',',$cryptos).'),site_users_balances.balance * currencies.usd_ask,0)) AS usd FROM site_users LEFT JOIN site_users_balances ON (site_users.id = site_users_balances.site_user) LEFT JOIN currencies ON (currencies.id = site_users_balances.currency)';
$result = db_query_array($sql);
if ($result) {
	$total_users = $result[0]['total_users'];
	$total_btc = number_format(round($result[0]['btc'] / $CFG->currencies[$main['crypto']]['usd_ask'],8,PHP_ROUND_HALF_UP),8,'.','');
	$total_usd = round($result[0]['usd'] / $CFG->currencies[$main['fiat']]['usd_ask'],2,PHP_ROUND_HALF_UP);
	$btc_per_user = number_format(round($total_btc / $total_users,8,PHP_ROUND_HALF_UP),8,'.','');
	$usd_per_user = round($total_usd / $total_users,2,PHP_ROUND_HALF_UP);
}

// get open orders BTC
$sql = 'SELECT SUM(orders.btc * currencies.usd_ask) AS btc FROM orders LEFT JOIN currencies ON (orders.c_currency = currencies.id)';
$result = db_query_array($sql);
$open_orders_btc = number_format(round($result[0]['btc'] / $CFG->currencies[$main['crypto']]['usd_ask'],8,PHP_ROUND_HALF_UP),8,'.','');

// get total transactions for the day
$sql = 'SELECT SUM(transactions.btc * c_currencies.usd_ask) AS total_btc, AVG(transactions.btc * c_currencies.usd_ask) AS avg_btc, SUM((transactions.fee + transactions.fee1)  * transactions.btc_price * currencies.usd_ask) AS total_fees FROM transactions LEFT JOIN currencies ON (transactions.currency = currencies.id) LEFT JOIN currencies c_currencies ON (transactions.c_currency = c_currencies.id) WHERE DATE(transactions.date) = (CURDATE() - INTERVAL 1 DAY)';
$result = db_query_array($sql);
$transactions_btc = round($result[0]['total_btc'] / $CFG->currencies[$main['crypto']]['usd_ask'],2,PHP_ROUND_HALF_UP);
$avg_transaction = round($result[0]['avg_btc'] / $CFG->currencies[$main['crypto']]['usd_ask'],2,PHP_ROUND_HALF_UP);
$trans_per_user = number_format(round($transactions_btc / $total_users,8,PHP_ROUND_HALF_UP),8,'.','');
$total_fees = round($result[0]['total_fees'] / $CFG->currencies[$main['fiat']]['usd_ask'],2,PHP_ROUND_HALF_UP);
$fees_per_user = number_format(round($total_fees / $total_users,8,PHP_ROUND_HALF_UP),8,'.','');

// get fees incurred from crypto networks for internal movements
$sql = 'SELECT SUM(fees.fee*currencies.usd_ask) AS fees_incurred FROM fees LEFT JOIN currencies ON (currencies.id = fees.c_currency) WHERE DATE(fees.date) = (CURDATE() - INTERVAL 1 DAY)';
$result = db_query_array($sql);
$gross_profit = $total_fees - round($result[0]['fees_incurred'] / $CFG->currencies[$main['fiat']]['usd_ask'],2,PHP_ROUND_HALF_UP);

db_insert('daily_reports',array('date'=>date('Y-m-d',strtotime('-1 day')),'total_btc'=>$total_btc,'total_fiat_usd'=>$total_usd,'btc_per_user'=>$btc_per_user,'usd_per_user'=>$usd_per_user,'open_orders_btc'=>$open_orders_btc,'transactions_btc'=>$transactions_btc,'avg_transaction_size_btc'=>$avg_transaction,'transaction_volume_per_user'=>$trans_per_user,'total_fees_btc'=>$total_fees,'fees_per_user_btc'=>$fees_per_user,'gross_profit_btc'=>$gross_profit));

db_update('status',1,array('cron_daily_stats'=>date('Y-m-d H:i:s')));
echo 'done'.PHP_EOL;
