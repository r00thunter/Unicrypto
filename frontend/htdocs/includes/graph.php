<?
$sma = (!empty($_SESSION['sma']) || !isset($_SESSION['sma']));
$sma1 = (!empty($_SESSION['sma1'])) ? preg_replace("/[^0-9]/", "",$_SESSION['sma1']) : 8;
$sma2 = (!empty($_SESSION['sma2'])) ? preg_replace("/[^0-9]/", "",$_SESSION['sma2']) : 30;
$ema = (!empty($_SESSION['ema']));
$ema1 = (!empty($_SESSION['ema1'])) ? preg_replace("/[^0-9]/", "",$_SESSION['ema1']) : 8;
$ema2 = (!empty($_SESSION['ema2'])) ? preg_replace("/[^0-9]/", "",$_SESSION['ema2']) : 30;
?>

<div class="graph_options ticker">
        	<div id="graph_time">
				<a href="#" <?= ($_SESSION['timeframe'] == '1min') ? 'class="selected"' : '' ?> data-option="1min">1m</a>
				<a href="#" <?= ($_SESSION['timeframe'] == '3min') ? 'class="selected"' : '' ?> data-option="3min">3m</a>
	        	<a href="#"  <?= ($_SESSION['timeframe'] == '5min') ? 'class="selected"' : '' ?> data-option="5min">5m</a>
	        	<a href="#" <?= ($_SESSION['timeframe'] == '15min') ? 'class="selected"' : '' ?> class="last" data-option="15min">15m</a>
	        	<a href="#" <?= ($_SESSION['timeframe'] == '30min') ? 'class="selected"' : '' ?> data-option="30min">30m</a>
	        	<a href="#" <?= ($_SESSION['timeframe'] == '1h') ? 'class="selected"' : '' ?> data-option="1h">1h</a>
	        	<a href="#" <?= ($_SESSION['timeframe'] == '2h') ? 'class="selected"' : '' ?> data-option="2h">2h</a>
	        	<a href="#" <?= ($_SESSION['timeframe'] == '4h') ? 'class="selected"' : '' ?> data-option="4h">4h</a>
	        	<a href="#" <?= ($_SESSION['timeframe'] == '6h') ? 'class="selected"' : '' ?> data-option="6h">6h</a>
	        	<a href="#" <?= ($_SESSION['timeframe'] == '12h') ? 'class="selected"' : '' ?> class="last" data-option="12h">12h</a>
	        	<a href="#" <?= ($_SESSION['timeframe'] == '1d') ? 'class="selected"' : '' ?> data-option="1d">1d</a>
	        	<a href="#" <?= (!$_SESSION['timeframe'] || $_SESSION['timeframe'] == '3d') ? 'class="selected"' : '' ?> data-option="3d">3d</a>
	        	<a href="#" <?= ($_SESSION['timeframe'] == '1w') ? 'class="selected"' : '' ?> class="last" data-option="1w">1w</a>
	        	<div class="repeat-line o1"></div>
	        	<div class="repeat-line o2"></div>
	        	<div class="repeat-line o3"></div>
	        	<div class="repeat-line o4"></div>
	        	<div class="repeat-line o5"></div>
	        	<div class="clear"></div>
        	</div>
        	<div id="graph_over">
        		<span class="g_over"><b>Open:</b> <span id="g_open"></span></span>
				<span class="g_over"><b>Close:</b> <span id="g_close"></span></span>
				<span class="g_over"><b>High:</b> <span id="g_high"></span></span>
				<span class="g_over"><b>Low:</b> <span id="g_low"></span></span>
				<span class="g_over"><b>Vol:</b> <span id="g_vol"></span></span>
				<div class="repeat-line o1"></div>
	        	<div class="repeat-line o2"></div>
	        	<div class="repeat-line o3"></div>
	        	<div class="repeat-line o4"></div>
	        	<div class="repeat-line o5"></div>
        		<div class="clear"></div>
        	</div>
        	<div class="clear"></div>
        </div>
        <div class="graph_contain">
        	<input type="hidden" id="is_crypto" value="<?= $currency_info['is_crypto'] ?>" />
        	<input type="hidden" id="graph_price_history_currency" value="<?= $currencies['currency'] ?>" />
        	<input type="hidden" id="graph_price_history_c_currency" value="<?= $currencies['c_currency'] ?>" />
        	<div id="graph_candles"></div>
	        <div class="clear_300"></div>
	        <div class="clear"></div>
	        <div id="graph_price_history"></div>
	        <div class="drag_zoom">
	        	<div class="contain">
		        	<div id="zl" class="handle"></div>
		        	<div id="zr" class="handle"></div>
		        	<div class="bg"></div>
	        	</div>
	        </div>
	        <a id="graph_settings" class="fa fa-gear"></a>
	        <div class="graph_settings">
	        	<div class="label"><?= Lang::string('indicators') ?></div>
	        	<div class="indicators">
	        		<div class="row">
	        			<input class="check" type="checkbox" id="sma" <?= ($sma) ? 'checked="checked"' : '' ?> />
	        			<a class="selected" href="#">SMA</a>
	        			<input id="sma1" class="indicator" value="<?= $sma1 ?>" type="text" style="background-color:#C4D5FF" />
	        			<input id="sma2" class="indicator" value="<?= $sma2 ?>" type="text" style="background-color:#FFEFC4" />
	        		</div>
	        		<div class="row">
	        			<input class="check" type="checkbox" id="ema" <?= ($ema) ? 'checked="checked"' : '' ?> />
	        			<a class="selected" href="#">EMA</a>
	        			<input id="ema1" class="indicator" value="<?= $ema1 ?>" type="text" style="background-color:#FCC4FF" />
	        			<input id="ema2" class="indicator" value="<?= $ema2 ?>" type="text" style="background-color:#C5FFC4" />
	        		</div>
	        	</div>
	        	<a class="highlight blue" href="#"><?= Lang::string('restore-defaults') ?></a>
	        </div>
	        <div class="clear_50"></div>
	        <div class="clear"></div>
        </div>