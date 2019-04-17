<!DOCTYPE html>
<html lang="en">
<?php include '../lib/common.php';
        
    if (User::$info['locked'] == 'Y' || User::$info['deactivated'] == 'Y')
        Link::redirect('settings.php');
    elseif (User::$awaiting_token)
        Link::redirect('verify-token.php');
    elseif (!User::isLoggedIn())
        Link::redirect('login.php');
        
        if ((!empty($_REQUEST['c_currency']) && array_key_exists(strtoupper($_REQUEST['c_currency']),$CFG->currencies)))
    $_SESSION['ba_c_currency'] = $_REQUEST['c_currency'];
else if (empty($_SESSION['ba_c_currency']))
    $_SESSION['ba_c_currency'] = $_SESSION['c_currency'];


$c_currency = $_SESSION['ba_c_currency'];
API::add('BitcoinAddresses','get',array(false,$c_currency,false,30,1));
API::add('Content','getRecord',array('bitcoin-addresses'));
$query = API::send();

$bitcoin_addresses = $query['BitcoinAddresses']['get']['results'][0];
$content = $query['Content']['getRecord']['results'][0];
$page_title = Lang::string('bitcoin-addresses');

if (!empty($_REQUEST['action']) && $_REQUEST['action'] == 'add' && $_SESSION["btc_uniq"] == $_REQUEST['uniq']) {
    if (strtotime($bitcoin_addresses[0]['date']) >= strtotime('-1 day'))
        Errors::add('You can only add one new '.$CFG->currencies[$c_currency]['currency'] .' address every 24 hours.');
    
    if (!is_array(Errors::$errors)) {
        API::add('BitcoinAddresses','getNew',array($c_currency));
        API::add('BitcoinAddresses','get',array(false,$c_currency,false,30,1));
        $query = API::send();
        $bitcoin_addresses = $query['BitcoinAddresses']['get']['results'][0];
        
        Messages::add(Lang::string('bitcoin-addresses-added'));
        Link::redirect('cryptoaddress.php');

    }
}
include "includes/sonance_header.php"; 
$_SESSION["btc_uniq"] = md5(uniqid(mt_rand(),true));
        ?>
    <style>
        footer{
            margin-top: 0;
        }
    </style>
    <body id="wrapper">
        <?php include "includes/sonance_navbar.php"; ?>
        <header>
            <div class="banner row">
                <div class="container content">
                    <h1>API Documentation</h1>
                </div>
            </div>
        </header>
       <div class="page-container">
            <div class="container">
                <div class="cms-outer">
                    <h5 class="m-b-1em"><?= $CFG->exchange_name; ?> REST API Documentation</h5>
                    <p>Welcome to the <?= $CFG->exchange_name; ?> REST API documentation. This API (Application Programming Interface) will allow you to access the functionality of this exchange by means of HTTP requests, making integration with your own applications possible.</p>
                    <p><b>Connecting to the API</b></p>
                    <p>You can access the API at the following url:</p>
                    <pre>
                        <code>https://api.bitexchange.live/api</code>
                    </pre>
                    <p>You are permited to make up to 60 requests a minute</p>
                    <p><b>Usage</b></p>
                    <p>In order to use a <b>public API method</b>, you must make an HTTP request to the appropriate <b>endpoint</b> for that particular method, sending the appropriate <span class="text-primary">GET</span> or <span class="text-primary">POST</span> parameters for that method. You can also send them in the <span class="text-primary">PAYLOAD</span> of the request in JSON format.</p>
                    <p>To access protected API methods, you must obtain an API key/secret pair. Their usage is explained further ahead in this document.</p>
                    <p>Here are a few basic usage examples:</p>
                    <pre>
                        <code># Example request using CURL on the command line</code>
                        <code>curl <span class="text-success">"https://api.bitexchange.live/api/transactions"</span> \</code>
                        <code>-d currency=<span class="text-success">"ISK"</span> \</code>
                        <code>-d limit=<span class="
                        text-voilet"> 5</span></code>
                    </pre>
                    <pre>
                        <code>// Example valid response</code>
                        <code class="text-success">{"transactions": {</code>
                        <code class="text-success">"0":{"id":"131","date":"2014-11-13 10:42:46","aur":"1.00000000","maker_type":"buy","price":"10.00","amount":"10.00","currency":"ISK"},</code>
                        <code class="text-success">"1":{"id":"129","date":"2014-11-11 11:14:12","aur":"0.50000000","maker_type":"buy","price":"11.27","amount":"5.63","currency":"ISK"},</code>
                        <code class="text-success">"2":{"id":"128","date":"2014-11-11 11:13:49","aur":"0.50000000","maker_type":"buy","price":"10.91","amount":"5.46","currency":"ISK"},</code>
                        <code class="text-success">"3":{"id":"127","date":"2014-11-10 18:29:15","aur":"0.50000000","maker_type":"buy","price":"11.20","amount":"5.60","currency":"ISK"},</code>
                        <code class="text-success">"4":{"id":"126","date":"2014-11-10 18:25:21","aur":"0.50000000","maker_type":"buy","price":"11.20","amount":"5.60","currency":"ISK"},</code>
                        <code>"request_currency":"ISK"</code>
                        <code>&nbsp; }</code>
                        <code>}</code>
                    </pre>
                    <pre>
                        <code>// Example error response</code>
                        <code class="text-success">{"errors":[{"message":"Invalid currency.","code":"INVALID_CURRENCY"}]}</code>
                    </pre>
                    <h5 class="m-b-1em">Public API Methods</h5>
                    <p>These methods can be accessed without an account or API key.</p>
                    <p><b>Stats</b></p>
                    <p>Returns statistics about the current state of the exchange.</p>
                    <pre>
                        <code><span class="text-danger">GET</span> https://api.bitexchange.live/api/stats</code>
                    </pre>
                    <p><b>Parameters:</b></p>
                    <p>
                        <b class="text-primary">market</b> <u>(string)</u> - Three-letter currency code. If omitted, the exchange default will be returned. Must be a market supported by the exchange.
                    </p>
                    <p>
                        <b class="text-primary">currency</b> <u>(string)</u> - Three-letter currency code. If omitted, the exchange default will be returned. Must be a currency supported by the exchange. All stats returned will be in this currency unit
                    </p>
                    <p><b>Response:</b></p>
                    <p><b class="text-primary">market</b> <u>(string)</u> - The cryptocurrency market that was queried.</p>
                    <p><b class="text-primary">currency</b> <u> (string)</u> - The currency in which this information is presented.</p>
                    <p><b class="text-primary">bid</b> <u>(float) </u> - Current bid pric</p>
                    <p><b class="text-primary">ask</b> <u>(float)</u>- Current ask price.</p>
                    <p><b class="text-primary">last_price</b>
                    <u>(float) </u>- The price of the last transaction.</p>
                    <p><b class="text-primary">last_transaction_type</b> <u> (string)</u> - The action performed by the taker (initiator) of the last transaction. Can be "BUY" or "SELL".</p>
                    <p><b class="text-primary">last_transaction_currency</b> <u> (string)</u>- Three-letter currency code for the currency in which the last transaction took place.</p>
                    <p><b class="text-primary">daily_change</b><u> (float)</u>- The amount that the current price has fallen or risen from the last close.</p>
                    <p><b class="text-primary">daily_change_percent</b><u>(float)</u>- The percentage of the current price versus the last close.</p>
                    <p><b class="text-primary">max</b><u>(float)</u> - Today's maximum transaction price.</p>
                    <p><b class="text-primary">min</b><u>(float)</u> - Today's minimum transaction price.</p>
                    <p><b class="text-primary">open</b><u>(float)</u> - Today's open price <em>(note: since the market is always open, it is also yesterday's close price)</em>.</p>
                    <p><b class="text-primary">global_units</b><u>(int)</u> - Total units in existance for the specified cryptocurrency worldwide.</p>
                    <p><b class="text-primary">market_cap</b><u>(int)</u> - The global market cap for the specified cryptocurrency.</p>
                    <p><b class="text-primary">global_volume</b><u>(int)</u> - Global trade volume for the specified cryptocurrency.</p>
                    <p><b class="text-primary">24h_volume</b><u>(float)</u> - The exchange's 24 hour volume.</p>
                    <p><b class="text-primary">24h_volume_buy</b><u>(float)</u> - The exchange's 24 hour buy volume.</p>
                    <p><b class="text-primary">24h_volume_sell</b><u>(float)</u> - The exchange's 24 hour sell volume.</p>
                    <p><b class="text-primary">1h_volume</b><u>(float)</u> - The exchange's one hour volume.</p>
                    <p><b class="text-primary">1h_volume_buy</b><u>(float)</u> - The exchange's one hour buy volume.</p>
                    <p><b class="text-primary">1h_volume_sell</b><u>(float)</u> - The exchange's one hour sell volume.</p>
                    <p><b>Historical Prices</b></p>
                    <p>Gives daily market close prices for the selected period.</p>
                    <pre>
                        <code><span class="text-danger">GET</span> https://api.bitexchange.live/api/historical-prices</code>
                    </pre>
                    <p><b>Parameters:</b></p>
                    <p><b class="text-primary">market</b><u>(string)</u> - Three-letter currency code. If omitted, will return the exchange default. Must be a market supported by the exchange.</p>
                    <p><b class="text-primary">currency</b><u>(string)</u> - Three-letter currency code. If omitted, will return the exchange default. Must be a currency supported by the exchange.</p>
                    <p><b class="text-primary">timeframe</b><u>(string)</u>  - The timeframe for your request. Permitted values are "1mon", "3mon", "6mon", "1year" and "ytd". Default is "1mon".</p>
                    <p><b>Response:</b></p>
                    <p><b class="text-primary">market</b><u>(string)</u> - The currency code of the selected market.</p>
                    <p><b class="text-primary">currency</b><u>(string)</u> - The currency in which this information is presented.</p>
                    <p><b class="text-primary">date</b><u>(string)</u> - The date string in YYYY-MM-DD format.</p>
                    <p><b class="text-primary">price</b><u>(float)</u> - The closing price for the given date.</p>
                    <h5 class="m-b-1em">Order Book</h5>
                    <p>Returns information on all the orders currently in the order book. Return will be grouped into two different arrays for bid and ask respectively.</p>
                    <pre>
                        <code><span class="text-danger">GET</span> https://api.bitexchange.live/api/order-book</code>
                    </pre>
                    <p><b>Parameters:</b></p>
                    <p><b class="text-primary">market</b><u>(string)</u> - Three-letter currency code. If omitted, will return the exchange default. Must be a market supported by the exchange.</p>
                    <p><b class="text-primary">currency</b><u>(string)</u> - Three-letter currency code. Will return the exchange default if omitted. Must be a currency supported by the exchange.</p>
                    <p><b>Response:</b></p>
                    <p><b class="text-primary">market</b><u>(string)</u> - The currency code of the selected market.</p>
                    <p><b class="text-primary">currency</b><u>(string)</u> - The currency code for the selected currency.</p>
                    <p><b class="text-primary">price</b><u>(float)</u> - The limit price of the order.</p>
                    <p><b class="text-primary">order_amount</b><u>(float)</u> - The remaining amount in BTC.</p>
                    <p><b class="text-primary">order_value</b><u>(float)</u> - The remaining value of the order in your requested currency.</p>
                    <p><b class="text-primary">converted_from</b><u>(string)</u> - The original currency in which the order was placed, if not equal to the requested currency.</p>
                    <h5 class="m-b-1em">Transactions</h5>
                    <p>Get the latest transactions that ocurred in the exchange, ordered by date in descending order.</p>
                    <pre>
                        <code><span class="text-danger">GET</span> https://api.bitexchange.live/api/transactions</code>
                    </pre>
                    <p><b>Parameters:</b></p>
                    <p><b class="text-primary">market</b><u>(string)</u> - Three-letter currency code. <em>Please note: You can omit this parameter to receive all trades from all markets!</em></p>
                    <p><b class="text-primary">currency</b><u>(string)</u> - Three-letter currency code. <em>Please note: You can omit this parameter to receive all prices will be in their native currency!</em></p>
                    <p><b class="text-primary">limit</b><u>(int)</u> - The amount of records to receive. Default is 10.</p>
                    <p><b>Response:</b></p>
                    <p><b class="text-primary">market</b><u>(string)</u> - The currency code of the selected market.</p>
                    <p><b class="text-primary">currency</b><u>(string)</u>  - The currency in which this information is presented. Will return 'ORIGINAL' if amounts are in the original currency</p>
                    <p><b class="text-primary">id</b><u>(init)</u> - A unique identifier for the transaction.</p>
                    <p><b class="text-primary">date</b><u>(string)</u> - The date string in YYYY-MM-DD format.</p>
                    <p><b class="text-primary">btc</b><u>(float)</u> - The transaction amount in BTC.</p>
                    <p><b class="text-primary">price</b><u>(float)</u>  - The price at which the transaction ocurred. <em>Will be returned in the original currency if no currency parameter is sent in the request.</em></p>
                    <p><b class="text-primary">price1</b><u>(float)</u>  - <em>Only if no currency param sent</em> - The price at which the transaction ocurred for the second party (maker), in the original currency.</p>
                    <p><b class="text-primary">amount</b><u>(float)</u> - The transction amount in the requested currency. <em>Will be returned in the original currency if no currency parameter is sent in the request.</em></p>
                    <p><b class="text-primary">amount1</b><u>(float)</u> - <em>Only if no currency param sent</em> - The transaction amount in the second party's (maker's) original currency.</p>
                    <p><b class="text-primary">currency</b><u>(string)</u> - The currency in which the transaction ocurred.<em> Will be returned in the original currency if no currency parameter is sent in the request.</em></p>
                    <p><b class="text-primary">currency1</b><u>(string)</u> - Only if no currency param sent - The second party's (maker's) original currency.</p>
                    <h5 class="m-b-1em">Protected API Methods</h5>
                    <p>In order to access these methods, you must obtain an API key/secret pair to authenticate your request.</p>
                    <p><b>Obtaining An API Key</b></p>
                    <p>To get access to our API, you must generate an API key on the API Access page. You must have two-factor authentication enabled on your account to be able to view this page. Upon generating a new API key, you will be given an API secret code. <em>This value will only be shown to you once.</em> Please store it in a secure place, as you will need it to use it together with your API key.</p>
                    <p>Once you have generated an API key, you can allow or restrict it's holder's access to the parent account's functionality by checking or unchecking the checkboxes in the "permissions" line under the API key.</p>
                    <p><b>Authenticating Your Request</b></p>
                    <p>To authenticate a request with your API key/secret pair, you must include the following parameters in your <span class="text-danger">POST</span> parameters or the JSON <span class="text-danger">PAYLOAD</span> of your request:</p>
                    
                    <p><b class="text-primary">api_key</b><u>(string)</u> - The API key that you generated.</p>
                    <p><b class="text-primary">nonce</b><u> (int) </u> - A random integer. Each request must have a higher <span class="text-primary">nonce</span> than the last one. You can use the current UNIX timestamp for example.</p>
                    <p><b class="text-primary">signature</b><u>(string)</u>
                    - An HMAC-SHA256 signature of the <span class="text-primary">JSON-encoded parameters of the request</span>, signed using the <span class="text-primary">API secret</span> that was generated together with the api_key. These parameters include the api_key and nonce. This <span class="text-primary">signature</span> should then be added to the request parameters.</p>
                    <p>We know that generating a signature might be a bit intimidating if you're doing it for the first time, so please see the following examples:</p>
                    <pre>
                        <code class="text-muted">// Javascript Example</code>
                        <br/>
                        <code class="text-muted">// Uses http://crypto-js.googlecode.com/svn/tags/3.0.2/build/rollups/hmac-sha256.js</code>
                        <code class="text-muted">// ...and http://crypto-js.googlecode.com/svn/tags/3.0.2/build/components/enc-base64-min.js</code>
                        <br/>
                        <code class="text-muted">// we add our public key and nonce to whatever parameters we are sending</code>
                        <code><span class="text-primary">var</span> params = {};</code>
                        <code>params.currency = <span class="text-success">"eur";</span></code>
                        <code>params.price = <span class="text-voilet">200</span>;</code>
                        <code>params.api_key = api_key;</code>
                        <code>params.nonce = Math.round(<span class="text-primary">new</span> Date().getTime() / <span class="text-voilet">1000</span>);</code>
                        <br/>
                        <code>// create the signature</code><br/>
                        <code><span class="text-primary">var</span> hash = CryptoJS.HmacSHA256(CryptoJS.enc.Base64.stringify(CryptoJS.enc.Utf8.parse(JSON.stringify(data))), api_secret).toString()</code>
                        <br/>
                        <code>// add signature to request parameters</code>
                        <code>params.signature = hash;</code>
                        <code></code>
                    </pre>
                    <pre>
                        <code class="text-muted">// PHP Example</code><br/>
                        <code class="text-muted">// we add our public key and nonce to whatever parameters we are sending</code><br/>
                        <code>$commands[<span class="text-success">'side'</span>] = <span class="text-success">'sell'</span>;</code>
                        <code>$commands[<span class="text-success">'type'</span>] = <span class="text-success">'stop'</span>;</code>
                        <code>$commands[<span class="text-success">'api_key'</span>] = $api_key;</code>
                        <code>$commands[<span class="text-success">'nonce'</span>] = time();</code><br/>
                        <code class="text-muted">// create the signature</code>
                        <code>$signature = hash_hmac('sha256', base64_encode(json_encode($commands)), $api_secret);</code><br/>
                        <code class="text-muted">// add signature to request parameters</code>
                        <code>$commands[<span class="text-success">'signature'</span>] = $signature;</code>
                        </pre>
                        <pre>
                        <code class="text-muted"># Python Example</code><br/>
                        <code><span class="text-primary">import</span>hashlib</code>
                        <code><span class="text-primary">import</span>hmac</code><br/>
                        <code>// we add our public key and nonce to whatever parameters we are sending</code>
                        <code>params = {<span class="text-success">'currency':</span> <span class="text-success">'eur'</span>, <span class="text-success">'price'</span>: <span class="text-voilet">200</span>, <span class="text-success">'api_key'</span>: api_key, <span class="text-success">'nonce'</span>: time.time()}</code><br/>
                        <code>// create the signature</code><br/>
                        <code>message = bytes(json.dumps(params)).encode('<span class="text-success">utf-8</span>')</code>
                        <code>secret = bytes(api_secret).encode('<span class="text-success">utf-8</span>')</code>
                        <code>signature = hmac.new(secret, message, digestmod=hashlib.sha256).hexdigest()</code><br/>
                        <code>// add signature to request parameters</code>
                        <code><span class="text-success">params</span>['signature'] = signature</code>
                        </pre>
                        <pre>
                        <code class="text-muted">// C# Example</code><br/>
                        <code><span class="text-primary">using </span>System.Security.Cryptography;</code><br/>
                        <code class="text-muted">// we add our public key and nonce to whatever parameters we are sending</code>
                        <code><span class="text-primary">var</span> params1 = new List>();</code> 
                        <code>params1.Add(new KeyValuePair(<span class="text-success">"api_key"</span>, api_key));</code>
                        <code>params1.Add(new KeyValuePair(<span class="text-success">"nonce"</span>, (Int32)(DateTime.UtcNow.Subtract(new DateTime(1970, 1, 1))).TotalSeconds));</code><br/>
                        <code>// create the signature</code>
                        <code>JavaScriptSerializer serializer = new JavaScriptSerializer();</code>
                        <code><span class="text-primary">var</span> message = serializer.Serialize(params1);</code><br/>
                        <code>secret = secret ?? <span class="text-success">""</span>;</code>
                        <code><span class="text-primary">var</span> encoding = new System.Text.ASCIIEncoding();</code>
                        <code><span class="text-primary">byte[]</span> keyByte = encoding.GetBytes(secret);</code>
                        <code><span class="text-primary">byte[]</span> messageBytes = encoding.GetBytes(message);</code>
                        <code><span class="text-primary">using (var </span>hmacsha256 = <span class="text-primary">new</span> HMACSHA256(keyByte))</code>
                        <code>{</code>
                        <code>&nbsp; <span class="text-primary">byte[]</span> hashmessage = hmacsha256.ComputeHash(messageBytes);</code>
                        <code>&nbsp; <span class="text-primary">var</span> signature = BitConverter.ToString(hashmessage);</code>
                        <code>&nbsp; signature = signature.Replace(<span class="text-success">"-"</span>, <span class="text-success">""</span>); </code><br/>
                        <code>&nbsp; // add signature to request parameters</code>
                        <code>&nbsp; params1.Add(<span class="text-primary">new</span> KeyValuePair(<span class="text-success">"signature"</span>, signature));</code>
                        <code>}</code>
                    </pre>
                    <pre>
                        <code>/* Java Example */</code><br/>
                        <code>/* Dependent on Apache Commons Codec to encode in base64. */</code>
                        <code>import javax.crypto.Mac;</code>
                        <code>import javax.crypto.spec.SecretKeySpec;</code>
                        <code>import org.apache.commons.codec.binary.Hex;</code><br/>
                        <code>/* we add our public key and nonce to whatever parameters we are sending */</code>
                        <code>Map params = new HashMap();</code>
                        <code>params.put("api_key", "demo");</code>
                        <code>params.put("nonce", ((int) (System.currentTimeMillis() / 1000L)));</code><br/>
                        <code>/* create the signature */</code>
                        <code>String secret = "secret";</code>
                        <code>String message = new JSONObject(params).toString();</code><br/>
                        <code>Mac sha256_HMAC = Mac.getInstance("HmacSHA256");</code>
                        <code>SecretKeySpec secret_key = new SecretKeySpec(secret.getBytes(), "HmacSHA256");</code>
                        <code>sha256_HMAC.init(secret_key);</code><br/>
                        <code>String hash = Hex.encodeHexString(sha256_HMAC.doFinal(message.getBytes()));</code><br/>
                        <code>/* add signature to request parameters */</code>
                        <code>params.put("signature", hash);</code>
                    </pre>
                    <h5 class="m-b-1em">Balances and Info</h5>
                    <p>Obtain the account's balances and fee levels.</p>
                    <pre>
                        <code><span class="text-danger">POST</span>https://api.bitexchange.live/api/balances-and-info</code>
                    </pre>
                    <p><b class="text-primary">on_hold[currency][withdrawal]</b><u>(float)</u> - The amount of a currency pending withdrawal.</p>
                    <p><b class="text-primary">on_hold[currency][order]</b><u>(float)</u> - The amount of a currency in open orders.</p>
                    <p><b class="text-primary">on_hold[currency][total]</b><u>(float)</u> - The total amount of a currency that is on hold - the sum of the last two items.</p>
                    <p><b class="text-primary">available[currency]</b><u>(float)</u>  - The amount of that particular currency that is currently available.</p>
                    <p><b class="text-primary">usd_volume </b><u>(float)</u> - The account's 30-day trading volume converted to USD (or the exchange's default currency).</p>
                    <p><b class="text-primary">fee_bracket[maker] </b><u>(float)</u> - The account's transaction fee level (as percentage), when not initiating the transaction (i.e. acting as a maker).</p>
                    <p><b class="text-primary">fee_bracket[taker]</b><u>(float)</u>  - The account's transaction fee level (as percentage), when initiating the transaction (i.e. acting as a taker).</p>
                    <p><b class="text-primary">exchange_btc_volume</b><u>(float)</u> - Exchange-wide 24-hour transaction volume in BTC (or in the exchange's default cryptocurrency).</p>
                    <h5 class="m-b-1em">Open Orders</h5>
                    <p>Get the account's current open orders, grouped by order side (bid or ask).</p>
                    <pre>
                        <code><span class="text-danger">POST</span>&nbsp; https://api.bitexchange.live/api/open-orders</code>
                    </pre>
                    <p><b>Parameters:</b></p>
                    <p><b class="text-primary">market</b><u>(string)</u>  - Three-letter currency code. If omitted, all orders will be returned. Must be a market supported by the exchange.</p>
                    <p><b class="text-primary">currency</b><u>(string)</u>  - Three-letter currency code. Will filter by orders of this currency. When omitted, all open orders will be displayed.</p>
                    <p><b>Response:</b></p>
                    <p><b class="text-primary">id</b><u>(init)</u> - A unique identifier for the order.</p>
                    <p><b class="text-primary">side</b><u>(string)</u> - "buy" or "sell".</p>
                    <p><b class="text-primary">type</b><u>(string)</u> - "market", "limit" or "stop".</p>
                    <p><b class="text-primary">amount</b><u>(float)</u>  - The original order amount in BTC.</p>
                    <p><b class="text-primary">amount_remaining</b><u>(float)</u> - The amount that has not yet been filled in BTC.</p>
                    <p><b class="text-primary">price</b><u>(float)</u> - The current price of the order in its native currency.</p>
                    <p><b class="text-primary">avg_price_executed</b><u>(float)</u> - A weighted average of the prices at which the order has been filled, in it's native currency. Zero means it has not yet generated any transactions.</p>
                    <p><b class="text-primary">stop_price </b><u>(float)</u>  - If there is the order is a stop order, the price at which the stop will be triggered.</p>
                    <p><b class="text-primary">market</b><u>(string)</u> - The currency code of the selected market to which the order belongs.</p>
                    <p><b class="text-primary">currency</b><u>(string)</u> - The order's native currency.</p>
                    <p><b class="text-primary">status</b><u>(string)</u> - The order's current status. Possible values are 'ACTIVE','FILLED','CANCELLED_USER','OUT_OF_FUNDS','REPLACED'.</p>
                    <p><b class="text-primary">replaced</b><u>(init)</u> - If the order was edited, the order it replaced.</p>
                    <p><b class="text-primary">replaced_by</b><u>(init)</u> - If the order was replaced, the id of the order that replaced it.</p>
                    <h5 class="m-b-1em">User Transactions</h5>
                    <p>Get a list of the account's transactions, ordered by date, in descending order.</p>
                    <pre>
                    <code><span class="text-danger">POST</span> https://api.bitexchange.live/api/user-transactions</code>
                    </pre>
                    <p><b>Parameters:</b></p>
                    <p><b class="text-primary">market</b><u>(string)</u>  - Three-letter currency code. If omitted, all orders will be returned. Must be a market supported by the exchange.</p>
                    <p><b class="text-primary">currency</b><u>(string)</u> - Three-letter currency code. Will filter by transactions involving this currency. When omitted, all transactions will be displayed.</p>
                    <p><b class="text-primary">limit</b><u>(init)</u>  - The amount of transactions to return.</p>
                    <p><b class="text-primary">side</b><u>(string)</u> - Filters transactions by type ("buy" or "sell").</p>
                    <p><b>Response:</b></p>
                    <p><b class="text-primary">id</b><u>(init)</u> - A unique identifier for the transaction.</p>
                    <p><b class="text-primary">date</b><u>(string)</u> - The date string in YYYY-MM-DD format.</p>
                    <p><b class="text-primary">btc</b><u>(float)</u> - The transaction amount in BTC.</p>
                    <p><b class="text-primary">side</b><u>(string)</u>  - Can be "buy" or "sell".</p>
                    <p><b class="text-primary">price</b><u>(float)</u> - The price at which the transaction ocurred, in it's native currency.</p>
                    <p><b class="text-primary">amount</b><u>(float)</u> - The transction amount in it's native currency. </p>
                    <p><b class="text-primary">fee</b><u>(float)</u> - The transaction fee in the native currency.</p>
                    <p><b class="text-primary">market</b><u>(string)</u> - The currency code of the selected market to which the order belongs.</p>
                    <p><b class="text-primary">currency</b><u>(string)</u>  - The currency in which the transaction ocurred.</p>
                    <h5 class="m-b-1em">Existing Bitcoin Deposit Addresses</h5>
                    <p>Get a list of the account's existing addresses for receiving Bitcoin.</p>
                    <pre>
                        <code><span class="text-danger">POST</span> https://api.bitexchange.live/api/crypto-deposit-address/get</code>
                    </pre>
                    <p><b>Parameters:</b></p>
                    <p><b class="text-primary">market</b><u>(string)</u> - Three-letter currency code. Required to specify the cryptocurrency.</p>
                    <p><b class="text-primary">limit</b><u>(init)</u>  - The amount of addresses to return.</p>
                    <p><b>Response:</b></p>
                    <p><b class="text-primary">address</b><u>(string)</u> - The address for depositing Bitcoin.</p>
                    <p><b class="text-primary">date</b><u>(string)</u>  - The date created in YYYY-MM-DD HH:MM:SS format.</p>
                    <h5 class="m-b-1em">Get New Bitcoin Deposit Addresses</h5>
                    <p>Get a new Bitcoin deposit address for the account.</p>
                    <pre>
                        <code><span class="text-danger">POST</span> https://api.bitexchange.live/api/crypto-deposit-address/new</code>
                    </pre>
                    <p><b>Parameters:</b></p>
                    <p><b class="text-primary">market</b><u>(string)</u> - Three-letter currency code. Required to specify the cryptocurrency.</p>
                    <p><b>Response:</b></p>
                    <p><b class="text-primary">address</b><u>(string)</u> - The address for depositing Bitcoin.</p>
                    <h5 class="m-b-1em">Get Deposits</h5>
                    <p>Get a list of deposits (crypto or fiat) made to the account, ordered by date, in descending order.</p>
                    <pre>
                        <code><span class="text-danger">POST</span> https://api.bitexchange.live/api/deposits/get</code>
                    </pre>
                    <p><b>Parameters:</b></p>
                    <p><b class="text-primary">currency</b><u>(string)</u> - Three-letter currency code. Will filter by deposits involving this currency. When omitted, all deposits will be displayed.</p>
                    <p><b class="text-primary">limit</b><u>(init)</u> - The amount of deposits to return.</p>
                    <p><b class="text-primary">status</b><u>(string)</u>  - Filters deposits by status ("pending", "completed" or "cancelled").</p>
                    <p><b>Response:</b></p>
                    <p><b class="text-primary">id</b><u>(init)</u> - A unique identifier for the deposit.</p>
                    <p><b class="text-primary">date</b><u>(string)</u> - The date string in YYYY-MM-DD HH:MM:SS format.</p>
                    <p><b class="text-primary">currency</b><u>(string)</u>  - The currency of the deposit.</p>
                    <p><b class="text-primary">amount</b><u>(float)</u>  - The amount of the deposit, in the deposit currency. </p>
                    <p><b class="text-primary">status</b><u>(string)</u> - The current status of the transaction. Can be "PENDING", "COMPLETED" or "CANCELLED".</p>
                    <p><b class="text-primary">account_number</b><u>(init)</u>  - The account number from which the deposit was made (only for fiat deposits).</p>
                    <p><b class="text-primary">address</b><u>(string)</u>  - The Bitcoin address from which the deposit was made (only for BTC deposits).</p>
                    <h5 class="m-b-1em">Get Withdrawals</h5>
                    <p>Get a list of withdrawals (crypto or fiat) from the account, ordered by date, in descending order.</p>
                     <pre>
                        <code><span class="text-danger">POST</span> https://api.bitexchange.live/api/withdrawals/get</code>
                    </pre>
                    <p><b>Parameters:</b></p>
                    <p><b class="text-primary">currency</b><u>(string)</u> - Three-letter currency code. Will filter by withdrawals involving this currency. When omitted, all withdrawals will be displayed.</p>
                    <p><b class="text-primary">limit</b><u>(init)</u> - The amount of withdrawals to return.</p>
                    <p><b class="text-primary">status</b><u>(string)</u>  - Filters withdrawals by status ("pending", "completed" or "cancelled").</p>
                    <p><b>Response:</b></p>
                    <p><b class="text-primary">id</b><u>(init)</u> - A unique identifier for the withdrawal.</p>
                    <p><b class="text-primary">date</b><u>(string)</u>  - The date string in YYYY-MM-DD HH:MM:SS format.</p>
                    <p><b class="text-primary">currency</b><u>(float)</u> - The currency of the withdrawal.</p>
                    <p><b class="text-primary">amount</b><u>(string)</u> - The amount of the withdrawal, in the withdrawal currency. </p>
                    <p><b class="text-primary">status</b><u>(string)</u> - The current status of the transaction. Can be "PENDING", "COMPLETED" or "CANCELLED".</p>
                    <p><b class="text-primary">account_number</b><u>(init)</u> - The account number to which the withdrawal was made (only for fiat withdrawal).</p>
                    <p><b class="text-primary">address</b><u>(string)</u> - The Bitcoin address to which the withdrawal was made (only for BTC withdrawals).</p>
                    <h5 class="m-b-1em">Place One (or Many) New Orders</h5>
                    <p>Place one or many new orders from your account. To <b>place multiple orders</b>, you can send a multidimensional array called <b>orders</b>, which should contain all the parameters in each array element as specified below.</p>
                     <pre>
                        <code><span class="text-danger">POST</span> https://api.bitexchange.live/api/orders/new</code>
                    </pre>
                    <p><b>Parameters:</b></p>
                    <p><b class="text-primary">market</b><u>(string)</u>  - Three-letter currency code. Required. The market in which the order will be placed.</p>
                    <p><b class="text-primary">currency</b><u>(string)</u> - Three-letter currency code. The currency in which the order will be placed.</p>
                    <p><b class="text-primary">side</b><u>(string)</u> - Can be "buy" or "sell".</p>
                    <p><b class="text-primary">type</b><u>(string)</u> - Can be "market", "limit" or "stop". "stop" orders can contain both a stop_price and limit_price - they will be processed in an OCO (One Cancels the Other) fashion, which means that whichever one is executed first will cancel the other.</p>
                    <p><b class="text-primary">limit_price</b><u>(float)</u> - The limit price for the order, in the order currency.</p>
                    <p><b class="text-primary">stop_price</b><u>(float)</u> - The stop price for the order, in the order currency. A "stop" order can have both a stop and limit price as explained in "type".</p>
                    <p><b class="text-primary">amount</b><u>(float)</u>  - The amount of BTC to buy or sell.</p>
                    <p><b class="text-primary">order</b><u>(array)</u> - This parameter is used only if you intend to place multiple orders in one API request. It should be an array or JSON string containing all the previous parameters for each element, such that orders[n] = ['side'=>x,'type'=>y,...]. It can be a simple array of HTTP parameters, or can be formatted as JSON.</p>
                    <p><b>Response:</b></p>
                    <p><b class="text-primary">transactions</b><u>(init)</u>  - The amount of transactions that ocurred upon placing the order.</p>
                    <p><b class="text-primary">new_order</b><u>(init)</u> - The amount of new orders placed (will return 2 if a stop order has both a limit_price and stop_price defined).</p>
                    <p><b class="text-primary">id</b><u>(string)</u> - A unique identifier for the withdrawal.</p>
                    <p><b class="text-primary">side</b><u>(string)</u>  - Can be "buy" or "sell".</p>
                    <p><b class="text-primary">type</b><u>(float)</u> - Can be "market", "limit" or "stop".</p>
                    <p><b class="text-primary">amount</b><u>(float)</u> - The original BTC amount to buy or sell.</p>
                    <p><b class="text-primary">amount_remaining</b><u></u>  - The outstanding (yet to be filled) BTC amount on the order.</p>
                    <p><b class="text-primary">price</b><u>(float)</u> - The current price of the order in its native currency.</p>
                    <p><b class="text-primary">avg_price_executed</b><u>(float)</u> - A weighted average of the prices at which the order has been filled, in it's currency. Zero means it has not yet generated any transactions.</p>
                    <p><b class="text-primary">stop_price</b><u>(float)</u> - If there is the order is a stop order, the price at which the stop will be triggered.</p>
                    <p><b class="text-primary">market</b><u>(string)</u> - The market in which the order was placed.</p>
                    <p><b class="text-primary">currency</b><u>(string)</u> - The order's native currency.</p>
                    <p><b class="text-primary">status</b><u>(string)</u> - The order's current status. Possible values are 'ACTIVE','FILLED','CANCELLED_USER','OUT_OF_FUNDS','REPLACED'.</p>
                    <p><b class="text-primary">replaced</b><u>(init)</u> - The order it replaced.</p>
                    <p><b class="text-primary">oco</b><u>(boolean)</u> - If a stop order has both stop and limit prices, this will true since whichever is executed first will cancel the other.</p>
                    <h5 class="m-b-1em">Cancel One, Many or ALL Orders</h5>
                    <p>Cancel one or many active orders. To <b>cancel multiple orders</b>, you can send a multidimensional array called <b>orders</b>, which should contain all the parameters in each array element as specified below. To <b>cancel ALL orders</b>, simply send a parameter called all - there is no need to send anything else.</p>
                    <pre>
                        <code><span class="text-danger">POST</span> https://api.bitexchange.live/api/orders/cancel</code>
                    </pre>
                    <p><b>Parameters:</b></p>
                    <p><b class="text-primary">id</b><u>(init)</u> - The unique identifier of the order that you wish to edit.</p>
                    <p><b class="text-primary">order</b><u>(array)</u>  - This parameter is used only if you intend to get the status of multiple orders in one API call. It should be an array or JSON string containing an id parameter for each element, such that orders[n] = ['id'=>x]. It can be a simple array of HTTP parameters, or can be formatted as JSON.</p>
                    <p><b class="text-primary">all</b><u>(bool)</u>  - Sending this parameter will cancel ALL orders. Use with caution!</p>
                    <p><b>Response:</b></p>
                    <p><b class="text-primary">id</b><u>(init)</u> - A unique identifier for the order.</p>
                    <p><b class="text-primary">side</b><u>(string)</u>  - Can be "buy" or "sell".</p>
                    <p><b class="text-primary">type</b><u>(string)</u> - Can be "market", "limit" or "stop".</p>
                    <p><b class="text-primary">amount</b><u>(float)</u> - The original BTC amount when the order was placed or edited.</p>
                    <p><b class="text-primary">amount_remaining</b><u>(float)</u> - The outstanding (yet to be filled) BTC amount on the order.</p>
                    <p><b class="text-primary">price</b><u>(float)</u> - The current price of the order in its native currency.</p>
                    <p><b class="text-primary">avg_price_executed</b><u>(float)</u> - A weighted average of the prices at which the order has been filled, in it's currency. Zero means it has not yet generated any transactions.</p>
                    <p><b class="text-primary">stop_price</b><u>(float)</u>  - If there is the order is a stop order, the price at which the stop will be triggered.</p>
                    <p><b class="text-primary">market</b><u>(string)</u> - The market in which the order was placed.</p>
                    <p><b class="text-primary">currency</b><u>(string)</u>  - The order's currency.</p>
                    <p><b class="text-primary">status</b><u>(string)</u> - The order's current status. Possible values are 'ACTIVE','FILLED','CANCELLED_USER','OUT_OF_FUNDS','REPLACED'.</p>
                    <p><b class="text-primary">replaced</b><u>(init)</u> - The order it replaced, if it has been edited.</p>
                    <p><b class="text-primary">replaced_by</b><u>(init)</u> - The order that replaced it, if "REPLACED".</p>
                    <h5 class="m-b-1em">Status of One (or Many) Orders</h5>
                    <p>Obtain the current state of one or many of the orders that have been placed by the account. To <b>get multiple orders,</b> you can send a multidimensional array called orders, which should contain all the parameters in each array element as specified below.</p>
                    <pre>
                        <code><span class="text-danger">POST</span> https://api.bitexchange.live/api/orders/status</code>
                    </pre>
                    <p><b>Parameters:</b></p>

                    <p><b class="text-primary">id</b><u>(init)</u> - The unique identifier of the order that you wish to edit.</p>
                    <p><b class="text-primary">orders</b><u>(array)</u> - This parameter is used only if you intend to get the status of multiple orders in one API call. It should be an array or JSON string containing an id parameter for each element, such that orders[n] = ['id'=>x]. It can be a simple array of HTTP parameters, or can be formatted as JSON.</p>
                    <p><b>Response:</b></p>
                    <p><b class="text-primary">id </b><u>(int) </u>  - A unique identifier for the order.</p>
                    <p><b class="text-primary">side</b><u>(string)</u>  - Can be "buy" or "sell".</p>
                    <p><b class="text-primary">type</b><u>(string)</u> - Can be "market", "limit" or "stop".</p>
                    <p><b class="text-primary">amount</b><u>(float)</u> - The original BTC amount when the order was placed or edited.</p>
                    <p><b class="text-primary">amount_remaining</b><u>(float)</u> - The outstanding (yet to be filled) BTC amount on the order.</p>
                    <p><b class="text-primary">price</b><u>(float)</u> - The current price of the order in its native currency.</p>
                    <p><b class="text-primary">avg_price_executed</b><u>(float)</u>  - A weighted average of the prices at which the order has been filled, in it's currency. Zero means it has not yet generated any transactions.</p>
                    <p><b class="text-primary">stop_price</b><u>(float)</u> - If there is the order is a stop order, the price at which the stop will be triggered.</p>
                    <p><b class="text-primary">market </b><u>(string)</u> - The market in which the order was placed.</p>
                    <p><b class="text-primary">currency</b><u>(string)</u>  - The order's currency.</p>
                    <p><b class="text-primary">status</b><u>(string)</u> - The order's current status. Possible values are 'ACTIVE','FILLED','CANCELLED_USER','OUT_OF_FUNDS','REPLACED'.</p>
                    <p><b class="text-primary">replaced</b><u>(init)</u>  - The order it replaced, if it has been edited.</p>
                    <p><b class="text-primary">replaced_by</b><u>(init)</u> - The order that replaced it, if "REPLACED".</p>
                    <h5 class="m-b-1em">Make a Withdrawal</h5>
                    <p>To make a withdrawal from your account to an existing crypto address or fiat bank account. Please note, you must link the desired bank account number to your account in the bank accounts page in order for this feature to work. Otherwise, your withdrawals will fail.</p>
                    <pre>
                        <code><span class="text-danger">POST</span> https://api.bitexchange.live/api/withdrawals/new</code>
                    </pre>
                    <p><b>Parameters:</b></p>
                    <p><b class="text-primary">currency</b><u> (string)</u> - The three-letter abbreviation for the currency that you wish to withdraw. It must match the currency on the account if you are withdrawing fiat.</p>
                    <p><b class="text-primary">amount</b><u>(float)</u> - The amount that you wish to withdraw, in the above currency.</p>
                    <p><b class="text-primary">address</b><u>(string)</u> - For crypto withdrawals, the blockchain address to which you wish to withdraw.</p>
                    <p><b class="text-primary">account_number</b><u>(init)</u> - For Fiat withdrawals, the bank account to which you would like to withdraw your currency.</p>
                    <p><b>Response:</b></p>
                    <p><b class="text-primary">id</b><u>(init)</u> - A unique identifier for the withdrawal.</p>
                    <p><b class="text-primary">date</b><u>(string)</u>  - The date string in YYYY-MM-DD HH:MM:SS format.</p>
                    <p><b class="text-primary">currency</b><u>(string)</u> - The currency of the withdrawal.</p>
                    <p><b class="text-primary">amount</b><u>(float)</u> - The amount of the withdrawal, in the withdrawal currency. </p>
                    <p><b class="text-primary">status</b><u>(string)</u> - The current status of the transaction. Can be "PENDING", "COMPLETED" or "CANCELLED".</p>
                    <p><b class="text-primary">account_number</b><u>
                        (init)
                    </u> - The account number to which the withdrawal was made (only for fiat withdrawal).</p>
                    <p><b class="text-primary">address</b><u>(string)</u>  - The Bitcoin address to which the withdrawal was made (only for BTC withdrawals).</p>
                </div>
            </div>
       </div>
        <?php include "includes/sonance_footer.php"; ?>
        <script type="text/javascript" src="js/ops.js?v=20160210"></script>
</html>