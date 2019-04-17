//options and strings
var browser_w = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
var candle_w = (browser_w < 1000) ? 4 : 8;
var candle_line_w = (browser_w < 1000) ? 1 : 2;

var month_abbr = {
	0: $('#javascript_mon_0').val(),
	1: $('#javascript_mon_1').val(),
	2: $('#javascript_mon_2').val(),
	3: $('#javascript_mon_3').val(),
	4: $('#javascript_mon_4').val(),
	5: $('#javascript_mon_5').val(),
	6: $('#javascript_mon_6').val(),
	7: $('#javascript_mon_7').val(),
	8: $('#javascript_mon_8').val(),
	9: $('#javascript_mon_9').val(),
	10: $('#javascript_mon_10').val(),
	11: $('#javascript_mon_11').val(),
};

var candle_options = {
	'1min': [60, '1mon'],
	'3min': [180, '1mon'],
	'5min': [300, '1mon'],
	'15min': [900, '1mon'],
	'30min': [1800, '3mon'],
	'1h': [3600, '3mon'],
	'2h': [7200, '3mon'],
	'4h': [14400, '3mon'],
	'6h': [21600, '3mon'],
	'12h': [43200, '6mon'],
	'1d': [86400, '6mon'],
	'3d': [259200, '1year'],
	'1w': [604800, '1year']
};

var plot, plot1, timeframe, data_res, data_zoom, data, data_vol, data1, series, series1, axes1, axes1_max, axes1_total, axes_total, p_min_x, p_max_x, p_diff, zl_r, zr_l, min, max, min1, max1, min_y, max_y, bar_width, first_id, last_id, loaded_remaining, data_min, loaded_min, data_loading, last_w, candlestick_options, volume_options, indicators_options;
function graphPriceHistory(refresh) {
	var currency = $('#graph_price_history_currency').val();
	var c_currency = $('#c_currency').val();
	timeframe = ($('#graph_time').is('select')) ? $('#graph_time').val() : $('#graph_time a.selected').attr('data-option');
	$("#graph_candles").append('<div class="tp-loader"></div>');

	$.getJSON("includes/ajax.graph.php?timeframe=" + timeframe + '&timeframe1=' + candle_options[timeframe][1] + '&currency=' + currency + '&c_currency=' + c_currency, function (json_data) {
		if (plot && refresh) {
			plot.shutdown();
			plot1.shutdown();
			plot = false;
			plot1 = false;
		}

		// get indicators
		if (!plot)
			graphSettings();

		// parse data
		parsed = graphFillGaps(json_data.candles, candle_options[timeframe][0], 100);
		data = parsed[0];
		data_vol = parsed[1];
		data1 = (json_data.history) ? json_data.history : [];
		c = (data) ? data.length - 1 : 0;
		c_half = Math.ceil(c - 50);
		c1 = data1.length;
		data1.push([data[c][0], data[c][2]]);
		max = data[c][0];
		min = data[c_half][0];
		max_y = parsed[2];
		min_y = parsed[3];
		max1 = data1[c1][0];
		min1 = data1[0][0];
		bar_width = (candle_options[timeframe][0] * 1000 / 3);
		first_id = json_data.first_id;
		last_id = json_data.last_id;
		loaded_remaining = parsed[4];
		loaded_min = parsed[0][0];
		data_min = data[0][0];
		data_res = data;
		data_zoom = 1;

		// setup candles series
		candlestick_options = { show: true, lineWidth: candle_w + 'px', rangeWidth: candle_line_w, downColor: '#e51919', upColor: '#16e758', rangeColor: '#848484', neutralColor: '#848484' };
		series = $.plot.candlestick.createCandlestick({
			data: data,
			candlestick: candlestick_options
		});

		// add volume
		volume_options = { data: data_vol, bars: { show: true, lineWidth: 0, barWidth: bar_width, fillColor: "#f0f0f0", fill: true, align: "center" }, yaxis: 2 };
		series.push(volume_options);
		series.reverse();

		// add indicators
		indicators_options = {};
		indicators_data = {};
		for (i in data) {
			var j = 4;
			for (h in window.indicators) {
				j++;
				if (!indicators_data[h])
					indicators_data[h] = [];

				indicators_data[h].push([data[i][0], data[i][j]]);
			}
		}
		for (i in window.indicators) {
			indicators_options[i] = { data: indicators_data[i], color: window.indicators[i].color, lines: { show: window.indicators[i].active, fill: false, lineWidth: 1 }, yaxis: 1, shadowSize: 0 };
			series.push(indicators_options[i]);
		}

		series1 = [{ data: data1, color: '#00bdbd', lines: { show: true, lineWidth: 1 }, shadowSize: 0 }];

		if (plot) {
			plot1.getAxes().xaxis.options.min = min1;
			plot1.getAxes().xaxis.options.max = max1;
			plot1.setData(series1);
			plot1.setupGrid();
			plot1.draw();

			axes1 = plot1.getAxes();
			axes1_max = Math.round(axes1.xaxis.p2c(max1));
			axes1_total = max1 - min1;
			axes_total = max - min;
			p_min_x = Math.max(Math.ceil(axes1.xaxis.p2c(min)) - 14, 0);
			p_max_x = Math.ceil(axes1.xaxis.p2c(max)) - 7;
			p_diff = p_max_x - (p_min_x + 7);
			zl_r = p_min_x + 7;
			zr_l = p_max_x - 7;
			last_w = candle_w;
			$('.drag_zoom .bg').css('left', zl_r + 'px').css('width', ((zr_l + 7) - zl_r) + 'px');
			$('.drag_zoom #zl').css('left', p_min_x + 'px');
			$('.drag_zoom #zr').css('left', p_max_x + 'px');

			plot.getAxes().xaxis.options.min = min;
			plot.getAxes().xaxis.options.max = max;
			plot.getAxes().yaxis.options.min = min_y;
			plot.getAxes().yaxis.options.max = max_y;
			plot.setData(series);
			plot.setupGrid();
			plot.draw();
			$("#graph_candles .tp-loader").remove();
			return false;
		}

		// candlestick chart
		var is_crypto = ($('#is_crypto').val() == 'Y');
		var last_d = new Date();
		plot = $.plot($("#graph_candles"), series, {
			series: { candlestick: { active: true } },
			xaxis: {
				mode: "time",
				max: max,
				min: min,
				tickLength: 5,
				tickFormatter: function (val, axis) {
					d = new Date(val);
					ret = false;

					if ((d - last_d) >= (86400 * 1000)) {
						year = d.getFullYear();
						month = d.getMonth();
						day = d.getDate();

						if (year != last_d.getFullYear())
							ret = year;
						else
							ret = month_abbr[month] + ' ' + day;

					}
					else {
						month = d.getMonth();
						day = d.getDate();
						mins = d.getMinutes().toString();
						pad = "00";
						ret = d.getHours() + ':' + pad.substring(0, pad.length - mins.length) + mins + (day != last_d.getDate() ? '/' + month_abbr[d.getMonth()] + ' ' + d.getDate() + '' : '');
					}

					last_d = d;
					return ret;
				}
			},
			yaxes: [{
				labelWidth: 0,
				position: "right",
				zoomRange: [1, 1],
				max: max_y,
				min: min_y,
				tickFormatter: function (val, axis) {
					if (!is_crypto)
						return val.toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
					else
						return val.toFixed(8);
				}
			},
			{
				show: false
			}],
			grid: {
				borderWidth: 0,
				hoverable: true
			},
			crosshair: {
				mode: "x",
				color: "#aaaaaa",
				lineWidth: 1
			}
		});

		// price history
		plot1 = $.plot($("#graph_price_history"), series1, {
			xaxis: {
				mode: "time",
				max: max1,
				min: min1,
				show: false
			},
			yaxis: {
				show: false
			},
			grid: {
				show: false
			}
		});

		// zooming and panning
		axes1 = plot1.getAxes();
		axes1_max = Math.round(axes1.xaxis.p2c(max1));
		axes1_total = max1 - min1;
		axes_total = max - min;
		p_min_x = Math.max(Math.ceil(axes1.xaxis.p2c(min)) - 14, 0);
		p_max_x = Math.ceil(axes1.xaxis.p2c(max)) - 7;
		p_diff = p_max_x - (p_min_x + 7);
		zl_r = p_min_x + 7;
		zr_l = p_max_x - 7;
		last_w = candle_w;

		$('.drag_zoom .bg').css('left', zl_r + 'px').css('width', ((zr_l + 7) - zl_r) + 'px');
		$('.drag_zoom #zl').css('left', p_min_x + 'px');
		$('.drag_zoom #zr').css('left', p_max_x + 'px');
		$('.drag_zoom #zl').draggable({
			axis: "x", containment: '.graph_contain', drag: function (e, ui) {
				e.stopPropagation();
				ui.position.left = Math.min(zr_l - p_diff, ui.position.left);
				zl_r = ui.position.left + 7;
				p = Math.ceil(axes1.xaxis.c2p(zl_r + 7));
				min = (max - p > axes_total) ? p : max - axes_total;

				if (min < data_min && !data_loading) {
					data_loading = true;
					if (loaded_remaining && loaded_remaining.length > 0) {
						new_data = graphFillGaps(loaded_remaining, candle_options[timeframe][0], 100, data_min);
						data_res = new_data[0].concat(data_res);
						data_vol = new_data[1].concat(data_vol);
						data_min = data_res[0][0];
						loaded_remaining = new_data[4];
						data_loading = false;
						return true;
					}
					else {
						graphLoadNew(first_id, function (json_data) {
							new_data = graphFillGaps(json_data.candles, candle_options[timeframe][0], 100, data_min);
							data_res = new_data[0].concat(data_res);
							data_vol = new_data[1].concat(data_vol);
							data_min = data_res[0][0];
							first_id = json_data.first_id;
							loaded_remaining = new_data[4];
							data_loading = false;
							return true;
						});
					}
				}

				data_zoom = Math.ceil((max - min) / (axes_total * 8));
				thinned = graphThinData(data_res, data_zoom, max, min);
				data = thinned[0];
				volume_options.data = data_vol;
				max_y = thinned[1];
				min_y = thinned[2];

				candlestick_options.lineWidth = Math.ceil((axes_total / (max - min)) * candle_w) + 'px';
				candlestick_options.rangeWidth = Math.ceil((axes_total / (max - min)) * candle_line_w);
				series = $.plot.candlestick.createCandlestick({
					data: data,
					candlestick: candlestick_options
				});

				series.push(volume_options);
				series.reverse();

				var j = 0;
				for (i in window.indicators) {
					indicators_options[i].data = thinned[3][j];
					series.push(indicators_options[i]);
					j++;
				}

				plot.setData(series);
				plot.getAxes().yaxis.options.min = thinned[2];
				plot.getAxes().yaxis.options.max = thinned[1];
				plot.getAxes().xaxis.options.min = min;
				plot.setupGrid();
				plot.draw();

				$('.drag_zoom .bg').css('left', zl_r + 'px').css('width', ((zr_l + 7) - zl_r) + 'px');
			}
		});
		$('.drag_zoom #zr').draggable({
			axis: "x", containment: '.graph_contain', drag: function (e, ui) {
				e.stopPropagation();
				ui.position.left = Math.max(zl_r + p_diff, ui.position.left);
				zr_l = ui.position.left;
				p = axes1.xaxis.c2p(ui.position.left + 7);
				max = (min + p > axes_total) ? p : min + axes_total;

				data_zoom = Math.ceil((max - min) / (axes_total * 8));
				thinned = graphThinData(data_res, data_zoom, max, min);
				data = thinned[0];
				volume_options.data = data_vol;
				max_y = thinned[1];
				min_y = thinned[2];

				candlestick_options.lineWidth = Math.ceil((axes_total / (max - min)) * candle_w) + 'px';
				candlestick_options.rangeWidth = Math.ceil((axes_total / (max - min)) * candle_line_w);
				series = $.plot.candlestick.createCandlestick({
					data: data,
					candlestick: candlestick_options
				});

				series.push(volume_options);
				series.reverse();

				var j = 0;
				for (i in window.indicators) {
					indicators_options[i].data = thinned[3][j];
					series.push(indicators_options[i]);
					j++;
				}

				plot.setData(series);
				plot.getAxes().yaxis.options.min = thinned[2];
				plot.getAxes().yaxis.options.max = thinned[1];
				plot.getAxes().xaxis.options.max = max;
				plot.setupGrid();
				plot.draw();

				$('.drag_zoom .bg').css('width', ((zr_l + 7) - zl_r) + 'px');
			}
		});
		$('.drag_zoom .bg').draggable({
			axis: "x", containment: '.graph_contain', drag: function (e, ui) {
				w = $('.drag_zoom .bg').width();
				zl_r = ui.position.left;
				zr_l = ui.position.left + w - 7;
				ui.position.left = Math.min(Math.max(7, ui.position.left), axes1_max - w - 7);
				min = axes1.xaxis.c2p(ui.position.left + 7);
				max = axes1.xaxis.c2p(ui.position.left + w + 7);

				if (min < data_min && !data_loading) {
					data_loading = true;
					if (loaded_remaining && loaded_remaining.length > 0) {
						new_data = graphFillGaps(loaded_remaining, candle_options[timeframe][0], 100, data_min);
						data_res = new_data[0].concat(data_res);
						data_vol = new_data[1].concat(data_vol);
						data_min = data_res[0][0];
						loaded_remaining = new_data[4];
						data_loading = false;
						return true;
					}
					else {
						graphLoadNew(first_id, function (json_data) {
							new_data = graphFillGaps(json_data.candles, candle_options[timeframe][0], 100, data_min);
							data_res = new_data[0].concat(data_res);
							data_vol = new_data[1].concat(data_vol);
							data_min = data_res[0][0];
							first_id = json_data.first_id;
							loaded_remaining = new_data[4];
							data_loading = false;
							return true;
						});
					}
				}

				thinned = graphThinData(data_res, data_zoom, max, min);
				data = thinned[0];
				volume_options.data = data_vol;
				max_y = thinned[1];
				min_y = thinned[2];

				candlestick_options.lineWidth = Math.ceil((axes_total / (max - min)) * candle_w) + 'px';
				candlestick_options.rangeWidth = Math.ceil((axes_total / (max - min)) * candle_line_w);
				series = $.plot.candlestick.createCandlestick({
					data: data,
					candlestick: candlestick_options
				});

				series.push(volume_options);
				series.reverse();

				var j = 0;
				for (i in window.indicators) {
					indicators_options[i].data = thinned[3][j];
					series.push(indicators_options[i]);
					j++;
				}

				plot.setData(series);
				plot.getAxes().yaxis.options.min = thinned[2];
				plot.getAxes().yaxis.options.max = thinned[1];
				plot.getAxes().xaxis.options.max = max;
				plot.getAxes().xaxis.options.min = min;
				plot.setupGrid();
				plot.draw();

				$('.drag_zoom #zl').css('left', (ui.position.left - 7) + 'px');
				$('.drag_zoom #zr').css('left', (ui.position.left + w) + 'px');
			}
		});

		var axes = plot.getAxes();
		var dataset = plot.getData();
		var left_offset = 30;
		var bottom_offset = 50;
		var flip;
		var max_x;
		var currency1 = currency.toUpperCase();

		$("#graph_candles").bind("plothover", function (event, pos, item) {
			if (pos.x < min || pos.x > max || pos.y < min_y || pos.y > max_y) {
				if ($('#graph_over').is(':visible'))
					$('#graph_over').fadeOut(200);

				return false;
			}
			else {
				if ($('#graph_over').is(':hidden'))
					$('#graph_over').fadeIn(200);
			}

			for (i in data_res) {
				if (pos.x >= (data_res[i][0] - ((candle_options[timeframe][0] * 1000) / 2)) && pos.x <= (data_res[i][0] + ((candle_options[timeframe][0] * 1000) / 2))) {
					data_res[i][1] = (!data_res[i][1]) ? 0 : data_res[i][1];
					data_res[i][2] = (!data_res[i][2]) ? 0 : data_res[i][2];
					data_res[i][3] = (!data_res[i][3]) ? 0 : data_res[i][3];
					data_res[i][4] = (!data_res[i][4]) ? 0 : data_res[i][4];
					$('#g_open').html(formatCurrency(data_res[i][1], ($('#is_crypto').val() == 'Y')));
					$('#g_close').html(formatCurrency(data_res[i][2], ($('#is_crypto').val() == 'Y')));
					$('#g_low').html(formatCurrency(data_res[i][3], ($('#is_crypto').val() == 'Y')));
					$('#g_high').html(formatCurrency(data_res[i][4], ($('#is_crypto').val() == 'Y')));
					break;
				}
			}

			for (i in data_vol) {
				if (pos.x >= (data_res[i][0] - ((candle_options[timeframe][0] * 1000) / 2)) && pos.x <= (data_res[i][0] + ((candle_options[timeframe][0] * 1000) / 2))) {
					$('#g_vol').html(formatCurrency(data_vol[i][1], ($('#is_crypto').val() == 'Y')));
					break;
				}
			}
		});

		setInterval(function () {
			graphLoadNew();
		}, 10000);

		$("#graph_candles .tp-loader").remove();
	});
}

function graphLoadNew(first, callback) {
	var currency = $('#graph_price_history_currency').val();
	var c_currency = $('#c_currency').val();
	timeframe = ($('#graph_time').is('select')) ? $('#graph_time').val() : $('#graph_time a.selected').attr('data-option');
	first = (first > 0) ? first : '';
	last = (first > 0) ? '' : last_id;
	last_max = data_res[data_res.length - 1][0];

	if (!(first > 0) && (((Date.now() - last_max) / 1000) < candle_options[timeframe][0]))
		return false;

	$.getJSON("includes/ajax.graph.php?timeframe=" + timeframe + '&timeframe1=' + candle_options[timeframe][1] + '&currency=' + currency + '&c_currency=' + c_currency + '&last=' + last + '&first=' + first, function (json_data) {
		if (first == '') {
			candle_amount = Math.floor((Date.now() - last_max) / 1000) / candle_options[timeframe][0];
			new_data = graphFillGaps(json_data.candles, candle_options[timeframe][0], candle_amount);
			data_res = data_res.concat(new_data[0]);
			data_vol = data_vol.concat(new_data[1]);

			c = data_res.length - 1;
			data1 = json_data.history;
			data1.push([data_res[c][0], data_res[c][2]]);
			series1 = [{ data: data1, color: '#00bdbd', lines: { show: true, lineWidth: 1 }, shadowSize: 0 }];
			max1 = data1[data1.length - 1][0];
			axes1_max = Math.round(axes1.xaxis.p2c(max1));

			plot1.getAxes().xaxis.options.min = min1;
			plot1.getAxes().xaxis.options.max = max1;
			plot1.setData(series1);
			plot1.setupGrid();
			plot1.draw();

			if (max >= (last_max - candle_options[timeframe][0])) {
				max = (data_res[data_res.length - 1][0] > max) ? data_res[data_res.length - 1][0] : max;
				min += max - Math.max(last_max, 0);

				thinned = graphThinData(data_res, data_zoom, max, min);
				max_y = (thinned[3] > max_y) ? thinned[3] : max_y;
				min_y = (thinned[4] > min_y) ? thinned[4] : min_y;

				data = thinned[0];
				volume_options.data = data_vol;
				candlestick_options.lineWidth = Math.ceil((axes_total / (max - min)) * candle_w) + 'px';
				candlestick_options.rangeWidth = Math.ceil((axes_total / (max - min)) * candle_line_w);

				series = $.plot.candlestick.createCandlestick({
					data: data,
					candlestick: candlestick_options
				});

				series.push({ data: data_vol, bars: { show: true, lineWidth: 0, barWidth: bar_width, fillColor: "#f0f0f0", fill: true, align: "center" }, yaxis: 2 });
				series.reverse();

				var j = 0;
				for (i in window.indicators) {
					indicators_options[i].data = thinned[3][j];
					series.push(indicators_options[i]);
					j++;
				}

				plot.setData(series);
				plot.getAxes().yaxis.options.min = min_y;
				plot.getAxes().yaxis.options.max = max_y;
				plot.getAxes().xaxis.options.max = max;
				plot.setupGrid();
				plot.draw();

			}
			else {
				diff = Math.max(data_res[data_res.length - 1][0] - last_max, 0);
				diff_pix = Math.ceil(axes1.xaxis.p2c(max1)) - Math.ceil(axes1.xaxis.p2c(max1 - diff));
				zl_r -= diff_pix;
				zr_l -= diff_pix;

				$('.drag_zoom .bg').css('left', (parseInt($('.drag_zoom .bg').css('left')) - diff_pix) + 'px');
				$('.drag_zoom #zl').css('left', (parseInt($('.drag_zoom #zl').css('left')) - diff_pix) + 'px');
				$('.drag_zoom #zr').css('left', (parseInt($('.drag_zoom #zr').css('left')) - diff_pix) + 'px');
			}
		}
		else {
			callback(json_data);
		}
	});
}

function graphSettings() {
	if (!window.indicators) {
		window.indicators = {
			sma1: { value: $('#sma1').val(), data: [], color: '#77A0FF', type: 'sma', active: ($('#sma1').siblings('.check').is(':checked') && $('#sma1').val() > 0) },
			sma2: { value: $('#sma2').val(), data: [], color: '#FFD877', type: 'sma', active: ($('#sma2').siblings('.check').is(':checked') && $('#sma2').val() > 0) },
			ema1: { value: $('#ema1').val(), data: [], color: '#F67CFF', type: 'ema', active: ($('#ema1').siblings('.check').is(':checked') && $('#ema1').val() > 0) },
			ema2: { value: $('#ema2').val(), data: [], color: '#77FF72', type: 'ema', active: ($('#ema2').siblings('.check').is(':checked') && $('#ema2').val() > 0) }
		}
	}

	$('.indicator').bind("keyup change", function () {
		elem = $(this);
		id = elem.attr('id');
		val = elem.val();

		window.indicators[id].value = val;
		$.get('includes/ajax.graph.php?action=indicators&' + id + '=' + val, function () { });
	});

	$('.indicators .check').bind("click", function () {
		var checked = $(this).is(':checked');
		$.get('includes/ajax.graph.php?action=indicators&' + ($(this).attr('id')) + '=' + (checked), function () { });

		series = $.plot.candlestick.createCandlestick({
			data: data,
			candlestick: candlestick_options
		});

		series.push(candlestick_options);
		series.reverse();

		for (i in window.indicators) {
			if (window.indicators[i].type == $(this).attr('id')) {
				indicators_options[i].lines.show = (checked);
			}

			series.push(indicators_options[i]);
		}

		plot.setData(series);
		plot.setupGrid();
		plot.draw();
	});
}

function graphClickAdd() {
	$('#bids_list .order_price').click(function (e) {
		$('#sell_price').val($(this).text());
		blink('#sell_price');
		$("html, body").animate({ scrollTop: $('.testimonials-4').offset().top }, 500);
		calculateBuyPrice();
		e.preventDefault();
	});
	$('#bids_list .order_amount').click(function (e) {
		$('#sell_amount').val($(this).text());
		blink('#sell_amount');
		$("html, body").animate({ scrollTop: $('.testimonials-4').offset().top }, 500);
		calculateBuyPrice();
		e.preventDefault();
	});
	$('#asks_list .order_price').click(function (e) {
		$('#buy_price').val($(this).text());
		blink('#buy_price');
		$("html, body").animate({ scrollTop: $('.testimonials-4').offset().top }, 500);
		calculateBuyPrice();
		e.preventDefault();
	});
	$('#asks_list .order_amount').click(function (e) {
		$('#buy_amount').val($(this).text());
		blink('#buy_amount');
		$("html, body").animate({ scrollTop: $('.testimonials-4').offset().top }, 500);
		calculateBuyPrice();
		e.preventDefault();
	});
}

function graphResize() {
	if (plot) {
		plot.resize();
		plot.setupGrid();
		plot.draw();
	}
	if (plot1) {
		plot1.resize();
		plot1.setupGrid();
		plot1.draw();

		if ($('.drag_zoom').length > 0) {
			axes1 = plot1.getAxes();
			axes1_max = Math.round(axes1.xaxis.p2c(max1));
			axes1_total = max1 - min1;
			axes_total = max - min;
			p_min_x = Math.max(Math.ceil(axes1.xaxis.p2c(min)) - 14, 0);
			p_max_x = Math.ceil(axes1.xaxis.p2c(max)) - 7;
			p_diff = p_max_x - (p_min_x + 7);
			zl_r = p_min_x + 7;
			zr_l = p_max_x - 7;

			$('.drag_zoom .bg').css('left', zl_r + 'px').css('width', ((zr_l + 7) - zl_r) + 'px');
			$('.drag_zoom #zl').css('left', p_min_x + 'px');
			$('.drag_zoom #zr').css('left', p_max_x + 'px');
		}
	}
}

var plot_o, last_data;
function graphOrders(json_data, refresh) {
	if (plot && refresh) {
		plot_o.shutdown();
		plot_o = false;
	}

	if (!plot_o)
		$("#graph_orders").append('<div class="tp-loader"></div>');

	var currency = $('#graph_orders_currency').val();
	var c_currency = $('#c_currency').val();
	if (!json_data) {
		scale = true;
		try {
			json_data = static_data;
		}
		catch (e) {
			if ($('#ts_order_book').length > 0) {
				$.getJSON('includes/ajax.graph.php?action=order_book&currency=' + currency + '&c_currency=' + c_currency, function (json_data) {
					graphOrders(json_data);
				});
				return false;
			}
			return false;
		}
	}

	if (scale && json_data && json_data.bids && json_data.asks) {
		var max_ask, min_ask, max_bid, min_bid;
		for (i in json_data.bids) {
			min_bid = (!min_bid || json_data.bids[i][0] < min_bid) ? json_data.bids[i][0] : min_bid;
			max_bid = (!max_bid || json_data.bids[i][0] > max_bid) ? json_data.bids[i][0] : max_bid;
		}
		for (i in json_data.asks) {
			min_ask = (!min_ask || json_data.asks[i][0] < min_ask) ? json_data.asks[i][0] : min_ask;
			max_ask = (!max_ask || json_data.asks[i][0] > max_ask) ? json_data.asks[i][0] : max_ask;
		}

		window.ob_max_bid = max_bid;
		window.ob_min_bid = min_bid;
		window.ob_max_ask = max_ask;
		window.ob_min_ask = min_ask;

		window.ob_bid_range = max_bid - min_bid;
		window.ob_ask_range = max_ask - min_ask;
		window.ob_c_bids = json_data.bids.length;
		window.ob_c_asks = json_data.asks.length;
		window.ob_lower_range = (window.ob_bid_range < window.ob_ask_range) ? window.ob_bid_range : window.ob_ask_range;
	}

	if (last_data) {
		if (last_data.bids && last_data.bids.length && last_data.bids.length > 30 && json_data.bids.length && json_data.bids.length >= 30) {
			var c = (last_data.bids.length && last_data.bids.length > 0) ? last_data.bids.length : 0;
			c = (c > 30) ? 29 : c - 1;

			var diff = (last_data.bids[c][1] && json_data.bids[c][1]) ? parseFloat(parseFloat(json_data.bids[c][1]) - (parseFloat(last_data.bids[c][1])).toFixed(2)) : 0;
			var c_price = (diff) ? parseFloat(json_data.bids[c][0]) : Number.POSITIVE_INFINITY;
			last_data.bids.splice(0, 30);

			last_data.bids = last_data.bids.map(function (item) {
				item[1] += diff;
				return item;
			}).filter(function (item) {
				if (window.ob_max_bid && window.ob_c_asks > 1 && item[0] < window.ob_min_bid)
					return false;

				return (parseFloat(item[0]) <= c_price);
			});

			if (json_data.bids)
				json_data.bids = json_data.bids.concat(last_data.bids);
		}
		else {
			json_data.bids = last_data.bids.filter(function (item) {
				if (window.ob_max_bid && window.ob_c_asks > 1 && item[0] < window.ob_min_bid)
					return false;

				return true;
			});
		}

		if (last_data.asks && last_data.asks.length && last_data.asks.length > 30 && json_data.asks.length && json_data.asks.length >= 30) {
			var c = (last_data.asks.length && last_data.asks.length > 0) ? last_data.asks.length : 0;
			c = (c > 30) ? 29 : c - 1;

			var diff = (last_data.asks[c][1] && json_data.asks[c][1]) ? parseFloat((parseFloat(json_data.asks[c][1]) - parseFloat(last_data.asks[c][1])).toFixed(2)) : 0;
			var c_price = (diff) ? parseFloat(json_data.bids[c][0]) : Number.NEGATIVE_INFINITY;
			last_data.asks.splice(0, 30);

			last_data.asks = last_data.asks.map(function (item) {
				item[1] += diff;
				return item;
			}).filter(function (item) {
				if (window.ob_max_ask && window.ob_c_bids > 1 && item[0] > window.ob_max_ask)
					return false;

				return (parseFloat(item[0]) >= c_price);
			});

			if (json_data.asks)
				json_data.asks = json_data.asks.concat(last_data.asks);
		}
		else {
			json_data.asks = last_data.asks.filter(function (item) {
				if (window.ob_max_ask && window.ob_c_bids > 1 && item[0] > window.ob_max_ask)
					return false;

				return true;
			});
		}
	}

	last_data = json_data;

	var series = [
		{
			data: json_data.bids,
			lines: { show: true, fill: true },
			points: { show: false, fill: false },
			color: '#17D6D6'
		},
		{
			data: json_data.asks,
			lines: { show: true, fill: true },
			points: { show: false, fill: false },
			color: '#53DB80'
		}
	];

	if (plot_o) {
		plot_o.setData(series);
		plot_o.setupGrid();
		plot_o.draw();
		return false;
	}

	plot_o = $.plot($("#graph_orders"), series, {
		xaxis: {
			tickLength: 0
		},
		yaxis: {
		},
		grid: {
			backgroundColor: '#FFFFFF',
			borderWidth: 1,
			borderColor: '#aaaaaa',
			hoverable: true
		},
		crosshair: {
			mode: "x",
			color: "#aaaaaa",
			lineWidth: 1
		}
	});

	var date_options = { year: "numeric", month: "short", day: "numeric" };
	var axes = plot_o.getAxes();
	var dataset = plot_o.getData();
	var left_offset = 30;
	var bottom_offset = 50;
	var flip;
	var max_x;
	var currency1 = $('#curr_abbr_' + currency).val();
	var last_type = false;

	$("#graph_orders").bind("plothover", function (event, pos, item) {
		plot_o.unhighlight();

		if (pos.x < axes.xaxis.min || pos.x > axes.xaxis.max || pos.y < axes.yaxis.min || pos.y > axes.yaxis.max) {
			$('#tooltip').css('display', 'none');
			return false;
		}

		updateLegend(pos, axes, dataset, false, function (graph_point, graph_i, graph_j) {
			var ask = (graph_i == 1);

			if (!graph_point || graph_point == 0)
				return false;

			$('#tooltip').css('display', 'block');
			$('#tooltip .price').html(currency1 + ' ' + formatCurrency(graph_point[0], ($('#is_crypto').val() == 'Y')));

			if (last_type != graph_i) {
				if (graph_i > 0)
					$('#tooltip .price').addClass('alt');
				else
					$('#tooltip .price').removeClass('alt');
			}

			if (!ask) {
				$('#tooltip .bid span').html(formatCurrency(graph_point[1], ($('#is_crypto').val() == 'Y')));
				if (last_type != graph_i) {
					$('#tooltip .bid').css('display', 'block');
					$('#tooltip .ask').css('display', 'none');
				}
			}
			else {
				$('#tooltip .ask span').html(formatCurrency(graph_point[1], ($('#is_crypto').val() == 'Y')));
				if (last_type != graph_i) {
					$('#tooltip .ask').css('display', 'block');
					$('#tooltip .bid').css('display', 'none');
				}
			}

			var x_pix = dataset[graph_i].xaxis.p2c(graph_point[0]);
			var y_pix = dataset[graph_i].yaxis.p2c(graph_point[1]);
			max_x = dataset[graph_i].xaxis.p2c(axes.xaxis.max);
			last_type = graph_i;

			if ((max_x - x_pix) < $('#tooltip').width())
				flip = true;
			else
				flip = false;

			if (!flip) {
				$('#tooltip').css('left', (x_pix + left_offset) + 'px');
				$('#tooltip').css('top', (y_pix - bottom_offset) + 'px');
			}
			else {
				$('#tooltip').css('left', (x_pix - $('#tooltip').width()) + 'px');
				$('#tooltip').css('top', (y_pix - bottom_offset) + 'px');
			}

			plot_o.highlight(graph_i, graph_j);
		});
	});

	$("#graph_orders").remove('.tp-loader');
}

var plot_d;
function graphDistribution() {
	var c_currency = $('#c_currency').val();
	$.getJSON('includes/ajax.graph.php?action=distribution&c_currency=' + c_currency, function (json_data) {
		var bar_width = 5;
		if (json_data && json_data.length > 0) {
			var b_min = json_data[json_data.length - 1][0];
			var b_max = json_data[0][0];
			bar_width = (b_max - b_min) / 30;
		}

		var series = [
			{
				data: json_data,
				bars: { show: true, fill: true, align: 'center', barWidth: bar_width },
				color: '#00bdbd'
			}
		];

		if (plot_d) {
			plot_d.setData(series);
			plot_d.setupGrid();
			plot_d.draw();
			return false;
		}

		plot_d = $.plot($("#graph_distribution"), series, {
			xaxis: {
				tickLength: 0
			},
			yaxis: {
			},
			grid: {
				backgroundColor: '#FFFFFF',
				borderWidth: 1,
				borderColor: '#aaaaaa',
				hoverable: true
			},
			crosshair: {
				mode: "x",
				color: "#aaaaaa",
				lineWidth: 1
			}
		});

		var axes = plot_d.getAxes();
		var dataset = plot_d.getData();
		var left_offset = 30;
		var bottom_offset = 50;
		var flip;
		var max_x;
		var last_type = false;

		$("#graph_distribution").bind("plothover", function (event, pos, item) {
			plot_d.unhighlight();

			if (pos.x < axes.xaxis.min || pos.x > axes.xaxis.max || pos.y < axes.yaxis.min || pos.y > axes.yaxis.max) {
				$('#tooltip1').css('display', 'none');
				return false;
			}

			updateLegend(pos, axes, dataset, true, function (graph_point, graph_i) {
				if (!graph_point || graph_point == 0)
					return false;

				$('#tooltip1').css('display', 'block');
				$('#tooltip1 .price span').html(formatCurrency(graph_point[0], ($('#is_crypto').val() == 'Y')));
				$('#tooltip1 .users span').html(graph_point[1]);

				var x_pix = dataset[graph_i].xaxis.p2c(graph_point[0]);
				var y_pix = dataset[graph_i].yaxis.p2c(graph_point[1]);
				max_x = dataset[graph_i].xaxis.p2c(axes.xaxis.max);
				last_type = graph_i;

				if ((max_x - x_pix) < $('#tooltip1').width())
					flip = true;
				else
					flip = false;

				if (!flip) {
					$('#tooltip1').css('left', (x_pix + left_offset) + 'px');
					$('#tooltip1').css('top', (y_pix - bottom_offset) + 'px');
				}
				else {
					$('#tooltip1').css('left', (x_pix - $('#tooltip').width()) + 'px');
					$('#tooltip1').css('top', (y_pix - bottom_offset) + 'px');
				}

				plot_d.highlight(graph_i, graph_j);
			});
		});
	});
}

function shareControls() {
	$('.graph_tabs a').click(function (e) {
		e.preventDefault();

		var op = $(this).attr('data-option');
		$('.shares_contain').css('display', 'none');
		$('.graph_tabs a').removeClass('selected');
		$(this).addClass('selected');

		if (op == 'dividends') {
			$('.graph_options').css('display', 'block');
			$('#shares_dividends').css('display', 'block');
		}
		else if (op == 'history') {
			$('.graph_options').css('display', 'none');
			$('#shares_history').css('display', 'block');
		}
	});
}

function graphControls() {
	$('.graph_options a').click(function () {
		$('.graph_options a').removeClass('selected');
		$(this).addClass('selected');
		var currency = $('#graph_price_history_currency').val();

		graphPriceHistory($(this).attr('data-option'), currency);
		return false;
	});

	$('#graph_time').bind("keyup change", function () {
		graphPriceHistory($(this).val(), $('#graph_price_history_currency').val());
	});

	$('.graph_tabs a').click(function (e) {
		e.preventDefault();

		var op = $(this).attr('data-option');
		$('.graph_contain').css('display', 'none');
		$('.graph_tabs a').removeClass('selected');
		$(this).addClass('selected');

		if (op == 'timeline') {
			$('.graph_options').css('display', 'block');
			$('#ts_timeline').css('display', 'block');
		}
		else if (op == 'order-book') {
			$('.graph_options').css('display', 'none');
			$('#ts_order_book').css('display', 'block');

			if (typeof plot_o == 'undefined')
				graphOrders();
		}
		else if (op == 'distribution') {
			$('.graph_options').css('display', 'none');
			$('#ts_distribution').css('display', 'block');
			graphDistribution();
		}
	});

	$('#fiat_currency').bind("keyup change", function () {
		var params = '';
		if ($('#buy_amount').length > 0)
			params += '&buy_amount=' + $('#buy_amount').val();
		if ($('#sell_amount').length > 0)
			params += '&sell_amount=' + $('#sell_amount').val();

		window.location.href = window.location.pathname + '?currency=' + $(this).val() + params;
	});
}

function graphFillGaps(json_data, candle_size, candle_amount, start) {
	var filled = [];
	var volume = [];

	var before = (start > 0);
	var placeholder_date = window.d_placeholder;
	var lc = (!start && data_res && data_res.length > 0) ? data_res[data_res.length - 1][1] : 0;
	var c = candle_size * 1000;
	var start = (!start) ? Date.now() : start;
	var d = Math.round(start / c) * c - (c * candle_amount);
	var d1 = d;
	var d2 = d;
	var max_y = 0;
	var min_y = Number.POSITIVE_INFINITY;
	var first_i = false;
	var last_i = false;
	var j = 0;

	if (!json_data)
		json_data = [];

	/*
	var indicators_options = {};
	var indicators_cache = [];
	var indicators_c = 0;
	var last_ema = {};
	var before_cache = [];

	if (before && window.cache_before)
		indicators_cache = window.cache_before;
	else if (window.cache_after)
		indicators_cache = window.cache_after;

	for (i in window.indicators) {
		indicators_options[i] = {value: window.indicators[i].value, type: window.indicators[i].type};
		indicators_c = (window.indicators[i].value > 0) ? window.indicators[i].value : indicators_c;
		last_ema[i] = 0;
	}
	*/

	if (json_data && json_data.length > 0 && d > json_data[0][0]) {
		var found = false;
		for (i in json_data) {
			if (json_data[i][0] < d) {
				lc = json_data[i][1];
			}
			else
				break;
		}

		if (found) {
			window.d_placeholder = false;
			window.last_lc = lc;
		}
	}
	else if (json_data.length > 0) {
		lc = json_data[0][1];
		window.last_lc = lc;
		if (before)
			window.d_placeholder = json_data[0][0];
	}
	else
		lc = window.last_lc;

	while (d < start) {
		var open = 0;
		var close = 0;
		var low = Number.POSITIVE_INFINITY;
		var high = 0;
		var vol = 0;
		var found = false;
		var item = [];

		for (i in json_data) {
			if (json_data[i][0] >= d && json_data[i][0] < (d + c)) {
				j++;
				open = (!(open > 0)) ? json_data[i][1] : open;
				close = json_data[i][1];
				low = (json_data[i][1] < low) ? json_data[i][1] : low;
				high = (json_data[i][1] > high) ? json_data[i][1] : high;
				lc = json_data[i][1];
				vol += (json_data[i][2]);
				first_i = (first_i > 0) ? first_i : i;
				last_i = i;

				found = true;
				if (json_data[i][0] >= (d + c))
					break;
			}
		}

		if (found) {
			max_y = (high > max_y) ? high : max_y;
			min_y = (low < min_y && low > 0) ? low : min_y;
			item = [d, close, open, low, high];
		}
		else {
			item = [d, lc, lc, lc, lc];
		}

		if (item.length > 0) {
			/*
			indicators_cache.push(item[1]);
			
			if (indicators_cache.length > indicators_c)
				indicators_cache.shift();
			
			var indicators_item = [];
			for (i in indicators_options) {
				if (indicators_options[i].value > indicators_cache.length) {
					before_cache.push(item[1]);
					indicators_item.push(false);
					continue;
				}
				else if (before_cache.length <= indicators_c)
					before_cache.push(item[1]);
				
				var slice = indicators_cache.slice(indicators_options[i].value * -1);
				var sma = slice.reduce(function(a, b){return a+b;}) / slice.length;
				
				if (indicators_options[i].type == 'sma') {
					indicators_item.push(sma);
					continue;
				}
				else if (indicators_options[i].type == 'ema') {
					var prev_ema = (i > slice.length) ? last_ema[i] : sma;
					var ema = ((slice[slice.length - 1] - last_ema[i]) * (2/(slice.length + 1))) + last_ema[i];
					
					indicators_item.push(ema);
					last_ema[i] = ema;
					continue;
				}
			}
			*/
			//item = item.concat(indicators_item);
			volume.push([d, vol]);
			filled.push(item);
		}

		d += c;
	}

	if (placeholder_date && data_res && before && j > 0) {
		var found = false;
		for (i in data_res) {
			if (data_res[i][0] >= placeholder_date)
				break;

			data_res[i][1] = lc;
			data_res[i][2] = lc;
			data_res[i][3] = lc;
			data_res[i][4] = lc;
			found = true;
		}
		if (found)
			window.d_placeholder = false;
	}

	if (first_i && first_i != last_i) {
		diff = last_i - first_i + 1;
		json_data.splice(first_i, diff);
	}
	else if (first_i == last_i)
		json_data.splice(first_i, 1);

	min_y = (min_y < Number.POSITIVE_INFINITY) ? min_y : 0;
	if (max_y > 0)
		max_y = max_y + (max_y * 0.05);
	else if (filled.length > 0)
		max_y = filled[filled.length - 1][4];

	if (min_y < Number.POSITIVE_INFINITY)
		min_y = Math.max(min_y - (min_y * 0.05), 0);
	else if (filled.length > 0)
		min_y = filled[filled.length - 1][3];
	/*
	if (before)
		window.cache_before = before_cache;
	
	window.cache_after = indicators_cache;
*/
	return [filled, volume, max_y, min_y, json_data];
}

function graphThinData(data, zoom, max, min) {
	if (!data || data.length == 0)
		return false;

	var max_y = 0;
	var min_y = Number.POSITIVE_INFINITY;
	var thinned = [];
	var volume = [];
	var indicators_data = [];

	for (i in window.indicators) {
		indicators_data.push([]);
	}

	for (i = 0; i < data.length; i = i + zoom) {
		if (max && data[i][0] > max)
			continue;
		if (min && data[i][0] < min)
			continue;

		var c = 4;
		max_y = (data[i][4] > max_y) ? data[i][4] : max_y;
		min_y = (data[i][3] < min_y && data[i][3] > 0) ? data[i][3] : min_y;
		thinned.push(data[i]);

		for (j in indicators_data) {
			c++;
			indicators_data[j].push([data[i][0], data[i][c]]);
		}
	}

	return [thinned, max_y, min_y, indicators_data];
}

function graphSetIndicators(data, before) {
	var indicators_options = {};
	var indicators_data = {};
	var indicators_cache = [];
	var indicators_c = 0;
	var last_ema = {};
	var before_cache = [];

	if (before && window.cache_before)
		indicators_cache = window.cache_before;
	else if (window.cache_after)
		indicators_cache = window.cache_after;

	for (i in window.indicators) {
		indicators_options[i] = { value: window.indicators[i].value, type: window.indicators[i].type };
		indicators_data[i] = [];
		indicators_c = (window.indicators[i].value > 0) ? window.indicators[i].value : indicators_c;
		last_ema[i] = 0;
	}

	for (i in data) {
		var item = data[i];
		indicators_cache.push(item[1]);

		if (indicators_cache.length > indicators_c)
			indicators_cache.shift();

		for (i in indicators_options) {
			if (indicators_options[i].value > indicators_cache.length) {
				before_cache.push(item[1]);
				indicators_data[i].push([item[0], false]);
				continue;
			}
			else if (before_cache.length <= indicators_c)
				before_cache.push(item[1]);

			var slice = indicators_cache.slice(indicators_options[i].value * -1);
			var sma = slice.reduce(function (a, b) { return a + b; }) / slice.length;

			if (indicators_options[i].type == 'sma') {
				indicators_data[i].push([item[0], sma]);
				continue;
			}
			else if (indicators_options[i].type == 'ema') {
				var prev_ema = (i > slice.length) ? last_ema[i] : sma;
				var ema = ((slice[slice.length - 1] - last_ema[i]) * (2 / (slice.length + 1))) + last_ema[i];

				indicators_data[i].push([item[0], ema]);
				last_ema[i] = ema;
				continue;
			}
		}
	}

	window.cache_before = before_cache;
	window.cache_after = indicators_cache;

	for (i in window.indicators) {
		if (before)
			window.indicators[i].data = indicators_data[i].concat(window.indicators[i].data);
		else
			window.indicators[i].data = window.indicators[i].data.concat(indicators_data[i]);
	}
}

function updateLegend(pos, axes, dataset, single_dataset, callback) {
	if (pos.x < axes.xaxis.min || pos.x > axes.xaxis.max || pos.y < axes.yaxis.min || pos.y > axes.yaxis.max) {
		return false;
	}

	if (single_dataset) {
		if (!dataset || !dataset[0].data || dataset[0].data.length == 0)
			return false;

		var series = dataset[0].data;
		var graph_i = 0;
	}
	else {
		if (dataset[0] && dataset[0].data && dataset[0].data[0] && pos.x <= dataset[0].data[0][0]) {
			console.log("DATA0 = ", dataset[0].data) ;
			var series = dataset[0].data;
			var graph_i = 0;
		}
		else if (dataset[1] && dataset[1].data && dataset[1].data[0]  && pos.x >= dataset[1].data[0][0]) {
			console.log("DATA1= ", dataset[1].data) ;
			var series = dataset[1].data;
			var graph_i = 1;
		}
		else
			return false;
	}

	var diff = null;
	var last_diff = null;
	var graph_j = null;
	var graph_point = null;

	for (i in series) {
		if (!pos.x || !series[i][0])
			continue;

		diff = pos.x - parseFloat(series[i][0]);
		if (last_diff && Math.abs(diff) > Math.abs(last_diff))
			break;

		graph_j = i;
		graph_point = series[i];
		last_diff = diff;
	}

	callback(graph_point, graph_i, graph_j);
}

function updateTransactions() {
	var notrades = ($('#graph_orders_currency').length > 0 || $('#open_orders_user').length > 0 || $('#user_fee').length > 0);
	var get_10 = ($('#user_fee').length > 0);
	var open_orders_user = $('#open_orders_user').val();
	var trades_amount = (get_10) ? 10 : 5;
	var cfg_user_id = $('#cfg_user_id').val();
	var sort_column = false;

	var update = setInterval(function () {
		var currency = (notrades) ? (($('#user_fee').length > 0) ? $('#buy_currency').val() : $('#graph_orders_currency').val()) : $('#graph_price_history_currency').val();
		var c_currency = $('#c_currency').val();
		var currency_id = (currency) ? $('#curr_abbr_' + currency).attr('name') : null;
		var order_by = $('#order_by').val();
		var is_crypto = ($('#is_crypto').length > 0) ? ($('#is_crypto').val() == 'Y') : null;

		if ($('#order_by').length > 0) {
			if ($('#order_by').val() == 'btcprice')
				sort_column = '.usd_price';
			else if ($('#order_by').val() == 'date')
				sort_column = '.order_date';
			else if ($('#order_by').val() == 'btc')
				sort_column = '.order_amount';
		}
		// if (open_orders_user) {
		// 	$.getJSON("includes/ajax.trades.php?currency=" + currency + '&c_currency=' + c_currency + ((order_by) ? '&order_by=' + order_by : '') + ((notrades) ? '&notrades=1' : '') + ((open_orders_user) ? '&user=1' : '&last_price=1') + ((get_10) ? '&get10=1' : ''), function (json_data) {
		// 		var depth_chart_data = { bids: [], asks: [] };
		// 		// if (json_data.transactions) {
		// 		// 	var i = 0;
		// 		// 	var insert_elem = ('#transactions_list tr:first');
		// 		// 	$.each(json_data.transactions[0], function (i) {
		// 		// 		if ($('#order_' + this.id).length > 0)
		// 		// 			return true;

		// 		// 		var this_currency_abbr = (this.currency == currency_id) ? '' : ((this.currency1 == currency_id) ? '' : ' (' + ($('#curr_abbr_' + this.currency1).val()) + ')');
		// 		// 		var this_currency_abbr1 = $('#curr_abbr_' + currency_id).val();
		// 		// 		var this_fa_symbol = $('#curr_sym_' + currency_id).val();
		// 		// 		var this_c_currency_abbr = $('#curr_abbr_' + c_currency).val();

		// 		// 		if (i == 0) {
		// 		// 			current_price = (typeof this.btc_price == 'string') ? parseFloat(this.btc_price.replace(',', '')) : this.btc_price;
		// 		// 			if (current_price > 0) {
		// 		// 				if (this.maker_type == 'sell') {
		// 		// 					$('#stats_last_price').parents('.stat1').removeClass('price-red').addClass('price-green');
		// 		// 					$('#up_or_down1').replaceWith('<i id="up_or_down1" class="fa fa-caret-up price-green"></i>');
		// 		// 				}
		// 		// 				else {
		// 		// 					$('#stats_last_price').parents('.stat1').removeClass('price-green').addClass('price-red');
		// 		// 					$('#up_or_down1').replaceWith('<i id="up_or_down1" class="fa fa-caret-down price-red"></i>');
		// 		// 				}

		// 		// 				var open_price = parseFloat($('#stats_open').html().replace(',', ''));
		// 		// 				var change_perc = formatCurrency(current_price - open_price);
		// 		// 				var change_abs = Math.abs(change_perc);

		// 		// 				$('#stats_last_price').html(formatCurrency(current_price, 2, 8));
		// 		// 				$('#stats_last_price_curr').html((this.currency == currency_id) ? '' : ((this.currency1 == currency_id) ? '' : ' (' + ($('#curr_abbr_' + this.currency1).val()) + ')'));
		// 		// 				$('#stats_daily_change_abs').html(formatCurrency(change_abs, 2, 8));
		// 		// 				$('#stats_daily_change_perc').html(formatCurrency((change_abs / current_price) * 100, 2, 8));

		// 		// 				if (this_c_currency_abbr && $('#c_currency_' + this_c_currency_abbr).length > 0) {
		// 		// 					$('#c_currency_' + this_c_currency_abbr).find('.price').html(formatCurrency(current_price));
		// 		// 					$('#c_currency_' + this_c_currency_abbr).find('.percent').html(formatCurrency((change_abs / current_price) * 100));
		// 		// 				}

		// 		// 				if (change_perc > 0)
		// 		// 					$('#up_or_down').replaceWith('<i id="up_or_down" class="fa fa-caret-up" style="color:#60FF51;"></i>');
		// 		// 				else if (change_perc < 0)
		// 		// 					$('#up_or_down').replaceWith('<i id="up_or_down" class="fa fa-caret-down" style="color:#FF5151;"></i>');
		// 		// 				else
		// 		// 					$('#up_or_down').replaceWith('<i id="up_or_down" class="fa fa-minus"></i>');

		// 		// 				if (typeof json_data.last_price_cnv == 'object') {
		// 		// 					for (key1 in json_data.last_price_cnv) {
		// 		// 						$('.price_' + key1).html(json_data.last_price_cnv[key1]);
		// 		// 						if (key1 == this_currency_abbr1) {
		// 		// 							if (this.maker_type == 'sell')
		// 		// 								$('.price_' + key1).parent().removeClass('price-red').addClass('price-green');
		// 		// 							else
		// 		// 								$('.price_' + key1).parent().removeClass('price-green').addClass('price-red');
		// 		// 						}

		// 		// 					}
		// 		// 				}
		// 		// 			}
		// 		// 		}

		// 		// 		var current_min = parseFloat($('#stats_min').html().replace(',', ''));
		// 		// 		var current_max = parseFloat($('#stats_max').html().replace(',', ''));
		// 		// 		if (this.btc_price < current_min)
		// 		// 			$('#stats_min').html(formatCurrency(this.btc_price));
		// 		// 		if (this.btc_price > current_max)
		// 		// 			$('#stats_max').html(formatCurrency(this.btc_price));

		// 		// 		if (!notrades) {
		// 		// 			var elem = $('<tr id="order_' + this.id + '"><td><span class="time_since"></span><input type="hidden" class="time_since_seconds" value="' + this.time_since + '" /></td><td>' + this.btc + ' ' + ($('#curr_abbr_' + this.c_currency).val()) + '</td><td><span class="buy_currency_char">' + this_fa_symbol + '</span><span>' + formatCurrency(this.btc_price, ($('#is_crypto').val() == 'Y')) + '</span>' + this_currency_abbr + '</td></tr>').insertAfter(insert_elem);
		// 		// 			insert_elem = elem;

		// 		// 			timeSince($(elem).find('.time_since'));
		// 		// 			$(elem).children('td').effect("highlight", { color: "#A2EEEE" }, 2000);
		// 		// 			$('#stats_traded').html(formatCurrency(json_data.btc_traded));

		// 		// 			var active_transactions = $('#transactions_list tr:not(#no_transactions)').length;
		// 		// 			if (active_transactions > 5)
		// 		// 				$('#transactions_list tr:not(#no_transactions):last').remove();

		// 		// 		}
		// 		// 		i++;
		// 		// 	});
		// 		// }
		// 		// else {
		// 		// 	$('#no_transactions').css('display', '');
		// 		// }

		// 		$.each($('.openbid_tr'), function (index) {
		// 			if (typeof index != 'number' || index >= 30)
		// 				return false;

		// 			var elem = this;
		// 			var elem_id = $(this).attr('id');
		// 			var order_id = elem_id.replace('openbid_', '');
		// 			var found = false;
		// 			if (json_data.bids[0] != null) {
		// 				$.each(json_data.bids[0], function () {
		// 					if (this.id == order_id) {
		// 						found = true;
		// 						return false;
		// 					}
		// 				});
		// 			}
		// 			if (!found)
		// 				$(elem).remove();
		// 		});
		// 		if (json_data.bids[0] != null) {
		// 			var cum_btc = 0;
		// 			$.each(json_data.bids[0], function (index) {
		// 				if (this.btc && this.btc > 0) {
		// 					cum_btc += parseFloat(this.btc);
		// 					depth_chart_data.bids.push([this.btc_price, cum_btc]);
		// 				}

		// 				if (index >= 10)
		// 					return true;

		// 				var this_currency_id = (parseFloat($('#this_currency_id').val()) > 0 ? $('#this_currency_id').val() : this.currency);
		// 				var fa_symbol = $('#curr_sym_' + this_currency_id).val();
		// 				var currency_abbr = $('#curr_abbr_' + this.currency).val();
		// 				var is_crypto = (open_orders_user) ? (this.is_crypto == 'Y') : ($('#is_crypto').val() == 'Y');

		// 				var this_bid = $('#openbid_' + this.id);
		// 				if (this_bid.length > 0) {
		// 					$(this_bid).find('.order_amount').html(formatCurrency(this.btc, true));
		// 					$('#openbid_' + this.id + '.double').find('.order_amount').html(formatCurrency(this.btc, true));
		// 					$(this_bid).find('.order_price').html(formatCurrency((this.btc_price > 0) ? this.btc_price : this.stop_price, is_crypto));
		// 					$('#openbid_' + this.id + '.double').find('.order_price').html(formatCurrency(this.stop_price, is_crypto));

		// 					if (notrades) {
		// 						$(this_bid).find('.order_value').html(formatCurrency(parseFloat(this.btc) * parseFloat((this.btc_price > 0) ? this.btc_price : this.stop_price), is_crypto));
		// 						$('#openbid_' + this.id + '.double').find('.order_value').html(formatCurrency(parseFloat(this.btc) * parseFloat(this.stop_price), is_crypto));
		// 						if (open_orders_user) {
		// 							var double = 0;
		// 							if (this.market_price == 'Y')
		// 								var type = '<div class="identify market_order">M</div>';
		// 							else if (this.btc_price > 0 && !(this.stop_price > 0))
		// 								var type = '<div class="identify limit_order">L</div>';
		// 							else if (this.stop_price > 0 && !(this.btc_price > 0))
		// 								var type = '<div class="identify stop_order">S</div>';
		// 							else if (this.stop_price > 0 && this.btc_price > 0) {
		// 								var type = '<div class="identify limit_order">L</div>';
		// 								double = 1;
		// 							}
		// 							$(this_bid).find('.identify').replaceWith(type);
		// 							if (!double)
		// 								$('#openbid_' + this.id + '.double').remove();

		// 							$(this_bid).find('.usd_price').val(this.usd_price);
		// 						}
		// 					}
		// 				}
		// 				else {
		// 					var last_price = 999999999999999999999;
		// 					var mine = (cfg_user_id > 0 && cfg_user_id == this.user_id && !open_orders_user && this.currency == this_currency_id) ? '<a class="fa fa-user" href="open-orders.php?id=' + this.id + '" title="' + ($('#your_order').val()) + '"></a>' : '';
		// 					var json_elem = this;
		// 					var skip_next = false;
		// 					var insert_elem = false;
		// 					var before = false;
		// 					var j = 1;

		// 					if ($('#openbids_list .order_price').length > 0) {
		// 						$.each($('#openbids_list .order_price'), function (i) {
		// 							if (skip_next) {
		// 								skip_next = false;
		// 								j++;
		// 								return;
		// 							}

		// 							var price = parseFloat($(this).html());
		// 							var next_price = ($(this).parents('tr').next('tr').find('.order_price').length > 0) ? parseFloat($(this).parents('tr').next('tr').find('.order_price').html()) : 0;
		// 							var new_price = parseFloat(json_elem.btc_price);
		// 							var active_bids = $('#openbids_list .order_price').length;
		// 							this_elem = (next_price == price) ? $(this).parents('tr').next('tr').find('.order_price') : this;
		// 							this_elem = ($(this_elem).parents('tr').next('tr').hasClass('double')) ? $(this_elem).parents('tr').next('tr').find('.order_price') : this_elem;
		// 							skip_next = (next_price == price);

		// 							if (new_price > price && new_price < last_price) {
		// 								insert_elem = $(this_elem).parents('tr');
		// 								before = 1;
		// 							}
		// 							else if (new_price == price)
		// 								insert_elem = $(this_elem).parents('tr');
		// 							else if (new_price < price && active_bids == j)
		// 								insert_elem = $(this_elem).parents('tr');

		// 							if (insert_elem)
		// 								return false;

		// 							last_price = price;
		// 							j++;
		// 						});
		// 					}
		// 					else {
		// 						insert_elem = $('#no_openbids');
		// 						$('#no_openbids').css('display', 'none');
		// 					}

		// 					if (notrades) {
		// 						var usd_price = '';
		// 						var reorder_class = (open_orders_user) ? 'currency_char' : 'buy_currency_char';
		// 						var crypto_hidden = (open_orders_user) ? '<input type="hidden" class="is_crypto" value="' + json_elem.is_crypto + '" />' : '';

		// 						if (open_orders_user) {
		// 							var double = 0;
		// 							if (json_elem.market_price == 'Y')
		// 								var type = '<td><div class="identify market_order">M</div></td>';
		// 							else if (json_elem.btc_price > 0 && !(json_elem.stop_price > 0))
		// 								var type = '<td><div class="identify limit_order">L</div></td>';
		// 							else if (json_elem.stop_price > 0 && !(json_elem.btc_price > 0))
		// 								var type = '<td><div class="identify stop_order">S</div></td>';
		// 							else if (json_elem.stop_price > 0 && json_elem.btc_price > 0) {
		// 								var type = '<td><div class="identify limit_order">L</div></td>';
		// 								double = 1;
		// 							}

		// 							usd_price = '<input type="hidden" class="usd_price" value="' + (json_elem.usd_price ? json_elem.usd_price : json_elem.btc_price) + '" /><input type="hidden" class="order_date" value="' + json_elem.date + '" />';
		// 						}

		// 						var edit_str = (open_orders_user) ? '<td><a title="' + $('#cfg_orders_edit').val() + '" href="edit-order.php?order_id=' + json_elem.id + '">edit</i></a> <a title="' + $('#cfg_orders_delete').val() + '" href="open-orders.php?delete_id=' + json_elem.id + '&uniq=' + $('#uniq').val() + '">delete</a></td>' : false;
		// 						var string = '<tr class="openbid_tr" id="openbid_' + json_elem.id + '">' + crypto_hidden + usd_price + type + '<td>' + mine + '<span class="' + reorder_class + '">' + fa_symbol + '</span><span class="order_price">' + formatCurrency(((json_elem.btc_price > 0) ? json_elem.btc_price : json_elem.stop_price), is_crypto) + '</span> ' + ((parseFloat(json_elem.btc_price) != parseFloat(json_elem.fiat_price)) ? '<a title="' + $('#orders_converted_from').val().replace('[currency]', currency_abbr) + '" class="fa fa-exchange" href="" onclick="return false;"></a>' : '') + '</td><td><span class="order_amount">' + json_elem.btc + '</span></td><td><span class="' + reorder_class + '">' + fa_symbol + '</span><span class="order_value">' + formatCurrency(parseFloat(json_elem.btc) * parseFloat(json_elem.btc_price), is_crypto) + '</span></td> </tr>';

		// 						if (double)
		// 							string += '<tr class="openbid_tr double" id="openbid_' + json_elem.id + '">' + crypto_hidden + '<td><div class="identify stop_order">S</div></td><td>' + mine + '<span class="' + reorder_class + '">' + fa_symbol + '</span><span class="order_price">' + (formatCurrency(json_elem.stop_price, is_crypto)) + '</span></td><td><span class="order_amount">' + json_elem.btc + '</span></td><td><span class="' + reorder_class + '">' + fa_symbol + '</span><span class="order_value">' + formatCurrency(parseFloat(json_elem.btc) * parseFloat(json_elem.btc_price), is_crypto) + '</span></td><td><span class="oco"><i class="fa fa-arrow-up"></i> OCO</span></td></tr>';
		// 					}
		// 					else
		// 						var string = '<tr class="openbid_tr" id="openbid_' + json_elem.id + '"><td>' + mine + '<span class="order_amount">' + json_elem.btc + '</span> ' + ($('#curr_abbr_' + json_elem.c_currency).val()) + '</td><td><span class="buy_currency_char">' + fa_symbol + '</span><span class="order_price">' + (formatCurrency(json_elem.btc_price), is_crypto) + '</span> ' + ((parseFloat(json_elem.btc_price) != parseFloat(json_elem.fiat_price)) ? '<a title="' + $('#orders_converted_from').val().replace('[currency]', currency_abbr) + '" class="fa fa-exchange" href="" onclick="return false;"></a>' : '') + '</td></tr>';

		// 					if (before)
		// 						var elem = $(string).insertBefore(insert_elem);
		// 					else
		// 						var elem = $(string).insertAfter(insert_elem);

		// 					$(elem).children('td').effect("highlight", { color: "#A2EEEE" }, 2000);
		// 				}
		// 			});

		// 			sortTable('#openbids_list', ((notrades) ? 0 : 1), 1, sort_column);
		// 		}
		// 		else {
		// 			$('#no_openbids').css('display', '');
		// 		}

		// 		$.each($('.openask_tr'), function (index) {
		// 			if (typeof index != 'number' || index >= 30)
		// 				return false;

		// 			var elem = this;
		// 			var elem_id = $(this).attr('id');
		// 			var order_id = elem_id.replace('openask_', '');
		// 			var found = false;
		// 			if (json_data.asks[0] != null) {
		// 				$.each(json_data.asks[0], function () {
		// 					if (this.id == order_id) {
		// 						found = true;
		// 						return false;
		// 					}
		// 				});
		// 			}
		// 			if (!found)
		// 				$(elem).remove();
		// 		});

		// 		if (json_data.asks[0] != null) {
		// 			var cum_btc = 0;
		// 			$.each(json_data.asks[0], function (index) {
		// 				if (this.btc && this.btc > 0) {
		// 					cum_btc += parseFloat(this.btc);
		// 					depth_chart_data.asks.push([this.btc_price, cum_btc]);
		// 				}

		// 				if (index >= 10)
		// 					return true;

		// 				var this_currency_id = (parseFloat($('#this_currency_id').val()) > 0 ? $('#this_currency_id').val() : this.currency);
		// 				var fa_symbol = $('#curr_sym_' + this_currency_id).val();
		// 				var currency_abbr = $('#curr_abbr_' + this.currency).val();
		// 				var is_crypto = (open_orders_user) ? (this.is_crypto == 'Y') : ($('#is_crypto').val() == 'Y');

		// 				var this_ask = $('#openask_' + this.id);
		// 				if (this_ask.length > 0) {
		// 					$(this_ask).find('.order_amount').html(formatCurrency(this.btc, true));
		// 					$('#openask_' + this.id + '.double').find('.order_amount').html(formatCurrency(this.btc, true));
		// 					$(this_ask).find('.order_price').html(formatCurrency((this.btc_price > 0) ? this.btc_price : this.stop_price, is_crypto));
		// 					$('#ask_' + this.id + '.double').find('.order_price').html(formatCurrency(this.stop_price, is_crypto));

		// 					if (notrades) {
		// 						$(this_ask).find('.order_value').html(formatCurrency(parseFloat(this.btc) * parseFloat((this.btc_price > 0) ? this.btc_price : this.stop_price), is_crypto));
		// 						$('#openask_' + this.id + '.double').find('.order_value').html(formatCurrency(parseFloat(this.btc) * parseFloat(this.stop_price), is_crypto));
		// 						if (open_orders_user) {
		// 							var double = 0;
		// 							if (this.market_price == 'Y')
		// 								var type = '<div class="identify market_order">M</div>';
		// 							else if (this.btc_price > 0 && !(this.stop_price > 0))
		// 								var type = '<div class="identify limit_order">L</div>';
		// 							else if (this.stop_price > 0 && !(this.btc_price > 0))
		// 								var type = '<div class="identify stop_order">S</div>';
		// 							else if (this.stop_price > 0 && this.btc_price > 0) {
		// 								var type = '<div class="identify limit_order">L</div>';
		// 								double = 1;
		// 							}
		// 							$(this_ask).find('.identify').replaceWith(type);
		// 							if (!double)
		// 								$('#openask_' + this.id + '.double').remove();

		// 							$(this_ask).find('.usd_price').val(this.usd_price);
		// 						}
		// 					}
		// 				}
		// 				else {
		// 					var last_price = 0;
		// 					var mine = (cfg_user_id > 0 && cfg_user_id == this.user_id && !open_orders_user && this.currency == this_currency_id) ? '<a class="fa fa-user" href="open-orders.php?id=' + this.id + '" title="' + ($('#your_order').val()) + '"></a>' : '';
		// 					var json_elem = this;
		// 					var skip_next = false;
		// 					var insert_elem = false;
		// 					var before = false;

		// 					var j = 1;
		// 					if ($('#openasks_list .order_price').length > 0) {
		// 						$.each($('#openasks_list .order_price'), function (i) {
		// 							if (skip_next) {
		// 								skip_next = false;
		// 								i++;
		// 								return;
		// 							}

		// 							var price = parseFloat($(this).html());
		// 							var next_price = ($(this).parents('tr').next('tr').find('.order_price').length > 0) ? parseFloat($(this).parents('tr').next('tr').find('.order_price').html()) : 0;
		// 							var new_price = parseFloat(json_elem.btc_price);
		// 							var active_asks = $('#openasks_list .order_price').length;
		// 							this_elem = (next_price == price) ? $(this).parents('tr').next('tr').find('.order_price') : this;
		// 							this_elem = ($(this_elem).parents('tr').next('tr').hasClass('double')) ? $(this_elem).parents('tr').next('tr').find('.order_price') : this_elem;
		// 							skip_next = (next_price == price);

		// 							if (new_price < price && new_price > last_price) {
		// 								insert_elem = $(this_elem).parents('tr');
		// 								before = 1;
		// 							}
		// 							else if (new_price == price)
		// 								insert_elem = $(this_elem).parents('tr');
		// 							else if (new_price > price && active_asks == j)
		// 								insert_elem = $(this_elem).parents('tr');

		// 							if (insert_elem)
		// 								return false;

		// 							last_price = price;
		// 							j++;
		// 						});
		// 					}
		// 					else {
		// 						insert_elem = $('#no_openasks');
		// 						$('#no_openasks').css('display', 'none');
		// 					}

		// 					if (notrades) {
		// 						var usd_price = '';
		// 						var reorder_class = (open_orders_user) ? 'currency_char' : 'buy_currency_char';
		// 						var crypto_hidden = (open_orders_user) ? '<input type="hidden" class="is_crypto" value="' + json_elem.is_crypto + '" />' : '';

		// 						if (open_orders_user) {
		// 							var double = 0;
		// 							if (json_elem.market_price == 'Y')
		// 								var type = '<td><div class="identify market_order">M</div></td>';
		// 							else if (json_elem.btc_price > 0 && !(json_elem.stop_price > 0))
		// 								var type = '<td><div class="identify limit_order">L</div></td>';
		// 							else if (json_elem.stop_price > 0 && !(json_elem.btc_price > 0))
		// 								var type = '<td><div class="identify stop_order">S</div></td>';
		// 							else if (json_elem.stop_price > 0 && json_elem.btc_price > 0) {
		// 								var type = '<td><div class="identify limit_order">L</div></td>';
		// 								double = 1;
		// 							}

		// 							usd_price = '<input type="hidden" class="usd_price" value="' + (json_elem.usd_price ? json_elem.usd_price : json_elem.btc_price) + '" /><input type="hidden" class="order_date" value="' + json_elem.date + '" />';
		// 						}

		// 						var edit_str = (open_orders_user) ? '<td><a title="' + $('#cfg_orders_edit').val() + '" href="edit-order.php?order_id=' + json_elem.id + '">Edit</a> <a title="' + $('#cfg_orders_delete').val() + '" href="open-orders.php?delete_id=' + json_elem.id + '&uniq=' + $('#uniq').val() + '">Delete</a></td>' : false;
		// 						var string = '<tr class="openask_tr" id="openask_' + json_elem.id + '">' + crypto_hidden + usd_price + type + '<td>' + mine + '<span class="' + reorder_class + '">' + fa_symbol + '</span><span class="order_price">' + (formatCurrency((json_elem.btc_price > 0) ? json_elem.btc_price : json_elem.stop_price, is_crypto)) + '</span> ' + ((parseFloat(json_elem.btc_price) != parseFloat(json_elem.fiat_price)) ? '<a title="' + $('#orders_converted_from').val().replace('[currency]', currency_abbr) + '" class="fa fa-exchange" href="" onclick="return false;"></a>' : '') + '</td><td><span class="order_amount">' + json_elem.btc + '</span></td><td><span class="' + reorder_class + '">' + fa_symbol + '</span><span class="order_value">' + formatCurrency(parseFloat(json_elem.btc) * parseFloat(json_elem.btc_price), is_crypto) + '</span></td> </tr>';

		// 						if (double)
		// 							string += '<tr class="openask_tr double" id="openask_' + json_elem.id + '">' + crypto_hidden + '<td><div class="identify stop_order">S</div></td><td>' + mine + '<span class="' + reorder_class + '">' + fa_symbol + '</span><span class="order_price">' + (formatCurrency(json_elem.stop_price, is_crypto)) + '</span></td><td><span class="order_amount">' + json_elem.btc + '</span></td><td>' + fa_symbol + '<span class="order_value">' + formatCurrency(parseFloat(json_elem.btc) * parseFloat(json_elem.btc_price), is_crypto) + '</span></td><td><span class="oco"><i class="fa fa-arrow-up"></i> OCO</span></td></tr>';
		// 					}
		// 					else
		// 						var string = '<tr class="openask_tr" id="openask_' + json_elem.id + '"><td>' + mine + '<span class="order_amount">' + json_elem.btc + '</span> ' + ($('#curr_abbr_' + json_elem.c_currency).val()) + '</td><td><span class="buy_currency_char">' + fa_symbol + '</span><span class="order_price">' + (formatCurrency(json_elem.btc_price, is_crypto)) + '</span> ' + ((parseFloat(json_elem.btc_price) != parseFloat(json_elem.fiat_price)) ? '<a title="' + $('#orders_converted_from').val().replace('[currency]', currency_abbr) + '" class="fa fa-exchange" href="" onclick="return false;"></a>' : '') + '</td></tr>';

		// 					if (before)
		// 						var elem = $(string).insertBefore(insert_elem);
		// 					else
		// 						var elem = $(string).insertAfter(insert_elem);

		// 					$(elem).children('td').effect("highlight", { color: "#A2EEEE" }, 2000);
		// 				}
		// 			});

		// 			sortTable('#openasks_list', ((notrades) ? 0 : 1), 0, sort_column);
		// 		}
		// 		else {
		// 			$('#no_openasks').css('display', '');
		// 		}

		// 		if ($("#graph_orders").length > 0 && (depth_chart_data.bids.length > 0 || depth_chart_data.asks > 0))
		// 			graphOrders(depth_chart_data);

		// 		if (parseFloat(json_data.last_price) && $('#last_price').length > 0) {
		// 			var lp_prev = $('#last_price').val();
		// 			var lp_now = $('<div/>').html((is_crypto ? '' : json_data.fa_symbol) + formatCurrency(json_data.last_price, 2, 8) + (is_crypto ? ' ' + json_data.fa_symbol : '') + json_data.last_price_curr).text();
		// 			$('#last_price').val(lp_now);

		// 			if (json_data.last_trans_color == 'price-green')
		// 				$('#last_price').removeClass('price-red').addClass(json_data.last_trans_color);
		// 			else
		// 				$('#last_price').removeClass('price-green').addClass(json_data.last_trans_color);

		// 			if (lp_prev != lp_now)
		// 				$('#last_price').effect("highlight", { color: "#A2EEEE" }, 1000);
		// 		}

		// 		var current_price = ($('#asks_list .order_price').length > 0) ? parseFloat($('#asks_list .order_price:first').html().replace(',', '')) : 0;
		// 		var current_bid = ($('#bids_list .order_price').length > 0) ? parseFloat($('#bids_list .order_price:first').html().replace(',', '')) : 0;

		// 		if ($('#buy_price').length > 0 && $('#buy_price').is('[readonly]') && current_price > 0) {
		// 			$('#buy_price').val(parseFloat(current_price));
		// 			$("#buy_price").trigger("change");
		// 		}
		// 		if ($('#sell_price').length > 0 && $('#sell_price').is('[readonly]') && current_bid > 0) {
		// 			$('#sell_price').val(parseFloat(current_bid));
		// 			$("#sell_price").trigger("change");
		// 		}

		// 		if (current_price > 0)
		// 			$('#buy_market_price').prop('readonly', '');
		// 		else
		// 			$('#buy_market_price').prop('readonly', 'readonly');
		// 		if (current_bid > 0)
		// 			$('#sell_market_price').prop('readonly', '');
		// 		else
		// 			$('#sell_market_price').prop('readonly', 'readonly');

		// 		$('#buy_user_available').html(json_data.available_fiat);
		// 		$('#sell_user_available').html(json_data.available_btc);
		// 		reorderLabels(($('#is_crypto').val() == 'Y'));
		// 	});
		// }

		// $.getJSON("includes/ajax.trades.php?currency=" + currency + '&c_currency=' + c_currency + ((order_by) ? '&order_by=' + order_by : '') + ((notrades) ? '&notrades=1' : '') + '&last_price=1' + ((get_10) ? '&get10=1' : ''), function (json_data) {
		// 	var depth_chart_data = { bids: [], asks: [] };
		
		// 	$.each($('.bid_tr'), function (index) {
		// 		if (typeof index != 'number' || index >= 30)
		// 			return false;

		// 		var elem = this;
		// 		var elem_id = $(this).attr('id');
		// 		var order_id = elem_id.replace('bid_', '');
		// 		var found = false;
		// 		if (json_data.bids[0] != null) {
		// 			$.each(json_data.bids[0], function () {
		// 				if (this.id == order_id) {
		// 					found = true;
		// 					return false;
		// 				}
		// 			});
		// 		}
		// 		if (!found)
		// 			$(elem).remove();
		// 	});
		// 	if (json_data.bids[0] != null) {
		// 		var cum_btc = 0;
		// 		$.each(json_data.bids[0], function (index) {
		// 			if (this.btc && this.btc > 0) {
		// 				cum_btc += parseFloat(this.btc);
		// 				depth_chart_data.bids.push([this.btc_price, cum_btc]);
		// 			}

		// 			if (index >= 10)
		// 				return true;

		// 			var this_currency_id = (parseFloat($('#this_currency_id').val()) > 0 ? $('#this_currency_id').val() : this.currency);
		// 			var fa_symbol = $('#curr_sym_' + this_currency_id).val();
		// 			var currency_abbr = $('#curr_abbr_' + this.currency).val();
		// 			var is_crypto = (open_orders_user) ? (this.is_crypto == 'Y') : ($('#is_crypto').val() == 'Y');

		// 			var this_bid = $('#bid_' + this.id);
		// 			if (this_bid.length > 0) {
		// 				$(this_bid).find('.order_amount').html(formatCurrency(this.btc, true));
		// 				$('#bid_' + this.id + '.double').find('.order_amount').html(formatCurrency(this.btc, true));
		// 				$(this_bid).find('.order_price').html(formatCurrency((this.btc_price > 0) ? this.btc_price : this.stop_price, is_crypto));
		// 				$('#bid_' + this.id + '.double').find('.order_price').html(formatCurrency(this.stop_price, is_crypto));

		// 				if (notrades) {
		// 					$(this_bid).find('.order_value').html(formatCurrency(parseFloat(this.btc) * parseFloat((this.btc_price > 0) ? this.btc_price : this.stop_price), is_crypto));
		// 					$('#bid_' + this.id + '.double').find('.order_value').html(formatCurrency(parseFloat(this.btc) * parseFloat(this.stop_price), is_crypto));
		// 					// if (open_orders_user) {
		// 					// 	var double = 0;
		// 					// 	if (this.market_price == 'Y')
		// 					// 		var type = '<div class="identify market_order">M</div>';
		// 					// 	else if (this.btc_price > 0 && !(this.stop_price > 0))
		// 					// 		var type = '<div class="identify limit_order">L</div>';
		// 					// 	else if (this.stop_price > 0 && !(this.btc_price > 0))
		// 					// 		var type = '<div class="identify stop_order">S</div>';
		// 					// 	else if (this.stop_price > 0 && this.btc_price > 0) {
		// 					// 		var type = '<div class="identify limit_order">L</div>';
		// 					// 		double = 1;
		// 					// 	}
		// 					// 	$(this_bid).find('.identify').replaceWith(type);
		// 					// 	if (!double)
		// 					// 		$('#bid_' + this.id + '.double').remove();

		// 					// 	$(this_bid).find('.usd_price').val(this.usd_price);
		// 					// }
		// 				}
		// 			}
		// 			else {
		// 				var last_price = 999999999999999999999;
		// 				var mine = (cfg_user_id > 0 && cfg_user_id == this.user_id && !open_orders_user && this.currency == this_currency_id) ? '<a class="fa fa-user" href="open-orders.php?id=' + this.id + '" title="' + ($('#your_order').val()) + '"></a>' : '';
		// 				var json_elem = this;
		// 				var skip_next = false;
		// 				var insert_elem = false;
		// 				var before = false;
		// 				var j = 1;

		// 				if ($('#bids_list .order_price').length > 0) {
		// 					$.each($('#bids_list .order_price'), function (i) {
		// 						if (skip_next) {
		// 							skip_next = false;
		// 							j++;
		// 							return;
		// 						}

		// 						var price = parseFloat($(this).html());
		// 						var next_price = ($(this).parents('tr').next('tr').find('.order_price').length > 0) ? parseFloat($(this).parents('tr').next('tr').find('.order_price').html()) : 0;
		// 						var new_price = parseFloat(json_elem.btc_price);
		// 						var active_bids = $('#bids_list .order_price').length;
		// 						this_elem = (next_price == price) ? $(this).parents('tr').next('tr').find('.order_price') : this;
		// 						this_elem = ($(this_elem).parents('tr').next('tr').hasClass('double')) ? $(this_elem).parents('tr').next('tr').find('.order_price') : this_elem;
		// 						skip_next = (next_price == price);

		// 						if (new_price > price && new_price < last_price) {
		// 							insert_elem = $(this_elem).parents('tr');
		// 							before = 1;
		// 						}
		// 						else if (new_price == price)
		// 							insert_elem = $(this_elem).parents('tr');
		// 						else if (new_price < price && active_bids == j)
		// 							insert_elem = $(this_elem).parents('tr');

		// 						if (insert_elem)
		// 							return false;

		// 						last_price = price;
		// 						j++;
		// 					});
		// 				}
		// 				else {
		// 					insert_elem = $('#no_bids');
		// 					$('#no_bids').css('display', 'none');
		// 				}

		// 				// if (notrades) {
		// 				// 	var usd_price = '';
		// 				// 	var reorder_class = (open_orders_user) ? 'currency_char' : 'buy_currency_char';
		// 				// 	var crypto_hidden = (open_orders_user) ? '<input type="hidden" class="is_crypto" value="' + json_elem.is_crypto + '" />' : '';

		// 				// 	if (open_orders_user) {
		// 				// 		var double = 0;
		// 				// 		if (json_elem.market_price == 'Y')
		// 				// 			var type = '<td><div class="identify market_order">M</div></td>';
		// 				// 		else if (json_elem.btc_price > 0 && !(json_elem.stop_price > 0))
		// 				// 			var type = '<td><div class="identify limit_order">L</div></td>';
		// 				// 		else if (json_elem.stop_price > 0 && !(json_elem.btc_price > 0))
		// 				// 			var type = '<td><div class="identify stop_order">S</div></td>';
		// 				// 		else if (json_elem.stop_price > 0 && json_elem.btc_price > 0) {
		// 				// 			var type = '<td><div class="identify limit_order">L</div></td>';
		// 				// 			double = 1;
		// 				// 		}

		// 				// 		usd_price = '<input type="hidden" class="usd_price" value="' + (json_elem.usd_price ? json_elem.usd_price : json_elem.btc_price) + '" /><input type="hidden" class="order_date" value="' + json_elem.date + '" />';
		// 				// 	}

		// 				// 	var edit_str = (open_orders_user) ? '<td><a title="' + $('#cfg_orders_edit').val() + '" href="edit-order.php?order_id=' + json_elem.id + '"><i class="fa fa-pencil"></i></a> <a title="' + $('#cfg_orders_delete').val() + '" href="open-orders.php?delete_id=' + json_elem.id + '&uniq=' + $('#uniq').val() + '"><i class="fa fa-times"></i></a></td>' : false;
		// 				// 	var string = '<tr class="bid_tr" id="bid_' + json_elem.id + '">' + crypto_hidden + usd_price + type + '<td>' + mine + '<span class="' + reorder_class + '">' + fa_symbol + '</span><span class="order_price">' + formatCurrency(((json_elem.btc_price > 0) ? json_elem.btc_price : json_elem.stop_price), is_crypto) + '</span> ' + ((parseFloat(json_elem.btc_price) != parseFloat(json_elem.fiat_price)) ? '<a title="' + $('#orders_converted_from').val().replace('[currency]', currency_abbr) + '" class="fa fa-exchange" href="" onclick="return false;"></a>' : '') + '</td><td><span class="order_amount">' + json_elem.btc + '</span></td><td><span class="' + reorder_class + '">' + fa_symbol + '</span><span class="order_value">' + formatCurrency(parseFloat(json_elem.btc) * parseFloat(json_elem.btc_price), is_crypto) + '</span></td>' + edit_str + '</tr>';

		// 				// 	if (double)
		// 				// 		string += '<tr class="bid_tr double" id="bid_' + json_elem.id + '">' + crypto_hidden + '<td><div class="identify stop_order">S</div></td><td>' + mine + '<span class="' + reorder_class + '">' + fa_symbol + '</span><span class="order_price">' + (formatCurrency(json_elem.stop_price, is_crypto)) + '</span></td><td><span class="order_amount">' + json_elem.btc + '</span></td><td><span class="' + reorder_class + '">' + fa_symbol + '</span><span class="order_value">' + formatCurrency(parseFloat(json_elem.btc) * parseFloat(json_elem.btc_price), is_crypto) + '</span></td><td><span class="oco"><i class="fa fa-arrow-up"></i> OCO</span></td></tr>';
		// 				// }
		// 				// else
		// 					var string = '<tr class="bid_tr" id="bid_' + json_elem.id + '"><td>' + mine + '<span class="order_amount">' + json_elem.btc + '</span> ' + ($('#curr_abbr_' + json_elem.c_currency).val()) + '</td><td><span class="buy_currency_char">' + fa_symbol + '</span><span class="order_price">' + (formatCurrency(json_elem.btc_price), is_crypto) + '</span> ' + ((parseFloat(json_elem.btc_price) != parseFloat(json_elem.fiat_price)) ? '<a title="' + $('#orders_converted_from').val().replace('[currency]', currency_abbr) + '" class="fa fa-exchange" href="" onclick="return false;"></a>' : '') + '</td></tr>';

		// 				if (before)
		// 					var elem = $(string).insertBefore(insert_elem);
		// 				else
		// 					var elem = $(string).insertAfter(insert_elem);

		// 				$(elem).children('td').effect("highlight", { color: "#A2EEEE" }, 2000);
		// 			}
		// 		});

		// 		sortTable('#bids_list', ((notrades) ? 0 : 1), 1, sort_column);
		// 	}
		// 	else {
		// 		$('#no_bids').css('display', '');
		// 	}

		// 	$.each($('.ask_tr'), function (index) {
		// 		if (typeof index != 'number' || index >= 30)
		// 			return false;

		// 		var elem = this;
		// 		var elem_id = $(this).attr('id');
		// 		var order_id = elem_id.replace('ask_', '');
		// 		var found = false;
		// 		if (json_data.asks[0] != null) {
		// 			$.each(json_data.asks[0], function () {
		// 				if (this.id == order_id) {
		// 					found = true;
		// 					return false;
		// 				}
		// 			});
		// 		}
		// 		if (!found)
		// 			$(elem).remove();
		// 	});

		// 	if (json_data.asks[0] != null) {
		// 		var cum_btc = 0;
		// 		$.each(json_data.asks[0], function (index) {
		// 			if (this.btc && this.btc > 0) {
		// 				cum_btc += parseFloat(this.btc);
		// 				depth_chart_data.asks.push([this.btc_price, cum_btc]);
		// 			}

		// 			if (index >= 10)
		// 				return true;

		// 			var this_currency_id = (parseFloat($('#this_currency_id').val()) > 0 ? $('#this_currency_id').val() : this.currency);
		// 			var fa_symbol = $('#curr_sym_' + this_currency_id).val();
		// 			var currency_abbr = $('#curr_abbr_' + this.currency).val();
		// 			var is_crypto = (open_orders_user) ? (this.is_crypto == 'Y') : ($('#is_crypto').val() == 'Y');

		// 			var this_ask = $('#ask_' + this.id);
		// 			if (this_ask.length > 0) {
		// 				$(this_ask).find('.order_amount').html(formatCurrency(this.btc, true));
		// 				$('#ask_' + this.id + '.double').find('.order_amount').html(formatCurrency(this.btc, true));
		// 				$(this_ask).find('.order_price').html(formatCurrency((this.btc_price > 0) ? this.btc_price : this.stop_price, is_crypto));
		// 				$('#ask_' + this.id + '.double').find('.order_price').html(formatCurrency(this.stop_price, is_crypto));

		// 				// if (notrades) {
		// 				// 	$(this_ask).find('.order_value').html(formatCurrency(parseFloat(this.btc) * parseFloat((this.btc_price > 0) ? this.btc_price : this.stop_price), is_crypto));
		// 				// 	$('#ask_' + this.id + '.double').find('.order_value').html(formatCurrency(parseFloat(this.btc) * parseFloat(this.stop_price), is_crypto));
		// 					// if (open_orders_user) {
		// 					// 	var double = 0;
		// 					// 	if (this.market_price == 'Y')
		// 					// 		var type = '<div class="identify market_order">M</div>';
		// 					// 	else if (this.btc_price > 0 && !(this.stop_price > 0))
		// 					// 		var type = '<div class="identify limit_order">L</div>';
		// 					// 	else if (this.stop_price > 0 && !(this.btc_price > 0))
		// 					// 		var type = '<div class="identify stop_order">S</div>';
		// 					// 	else if (this.stop_price > 0 && this.btc_price > 0) {
		// 					// 		var type = '<div class="identify limit_order">L</div>';
		// 					// 		double = 1;
		// 					// 	}
		// 					// 	$(this_ask).find('.identify').replaceWith(type);
		// 					// 	if (!double)
		// 					// 		$('#ask_' + this.id + '.double').remove();

		// 					// 	$(this_ask).find('.usd_price').val(this.usd_price);
		// 					// }
		// 				// }
		// 			}
		// 			else {
		// 				var last_price = 0;
		// 				var mine = (cfg_user_id > 0 && cfg_user_id == this.user_id && !open_orders_user && this.currency == this_currency_id) ? '<a class="fa fa-user" href="open-orders.php?id=' + this.id + '" title="' + ($('#your_order').val()) + '"></a>' : '';
		// 				var json_elem = this;
		// 				var skip_next = false;
		// 				var insert_elem = false;
		// 				var before = false;

		// 				var j = 1;
		// 				if ($('#asks_list .order_price').length > 0) {
		// 					$.each($('#asks_list .order_price'), function (i) {
		// 						if (skip_next) {
		// 							skip_next = false;
		// 							i++;
		// 							return;
		// 						}

		// 						var price = parseFloat($(this).html());
		// 						var next_price = ($(this).parents('tr').next('tr').find('.order_price').length > 0) ? parseFloat($(this).parents('tr').next('tr').find('.order_price').html()) : 0;
		// 						var new_price = parseFloat(json_elem.btc_price);
		// 						var active_asks = $('#asks_list .order_price').length;
		// 						this_elem = (next_price == price) ? $(this).parents('tr').next('tr').find('.order_price') : this;
		// 						this_elem = ($(this_elem).parents('tr').next('tr').hasClass('double')) ? $(this_elem).parents('tr').next('tr').find('.order_price') : this_elem;
		// 						skip_next = (next_price == price);

		// 						if (new_price < price && new_price > last_price) {
		// 							insert_elem = $(this_elem).parents('tr');
		// 							before = 1;
		// 						}
		// 						else if (new_price == price)
		// 							insert_elem = $(this_elem).parents('tr');
		// 						else if (new_price > price && active_asks == j)
		// 							insert_elem = $(this_elem).parents('tr');

		// 						if (insert_elem)
		// 							return false;

		// 						last_price = price;
		// 						j++;
		// 					});
		// 				}
		// 				else {
		// 					insert_elem = $('#no_asks');
		// 					$('#no_asks').css('display', 'none');
		// 				}

		// 				// if (notrades) {
		// 				// 	var usd_price = '';
		// 				// 	var reorder_class = (open_orders_user) ? 'currency_char' : 'buy_currency_char';
		// 				// 	var crypto_hidden = (open_orders_user) ? '<input type="hidden" class="is_crypto" value="' + json_elem.is_crypto + '" />' : '';

		// 				// 	// if (open_orders_user) {
		// 				// 	// 	var double = 0;
		// 				// 	// 	if (json_elem.market_price == 'Y')
		// 				// 	// 		var type = '<td><div class="identify market_order">M</div></td>';
		// 				// 	// 	else if (json_elem.btc_price > 0 && !(json_elem.stop_price > 0))
		// 				// 	// 		var type = '<td><div class="identify limit_order">L</div></td>';
		// 				// 	// 	else if (json_elem.stop_price > 0 && !(json_elem.btc_price > 0))
		// 				// 	// 		var type = '<td><div class="identify stop_order">S</div></td>';
		// 				// 	// 	else if (json_elem.stop_price > 0 && json_elem.btc_price > 0) {
		// 				// 	// 		var type = '<td><div class="identify limit_order">L</div></td>';
		// 				// 	// 		double = 1;
		// 				// 	// 	}

		// 				// 	// 	usd_price = '<input type="hidden" class="usd_price" value="' + (json_elem.usd_price ? json_elem.usd_price : json_elem.btc_price) + '" /><input type="hidden" class="order_date" value="' + json_elem.date + '" />';
		// 				// 	// }

		// 				// 	var edit_str = (open_orders_user) ? '<td><a title="' + $('#cfg_orders_edit').val() + '" href="edit-order.php?order_id=' + json_elem.id + '"><i class="fa fa-pencil"></i></a> <a title="' + $('#cfg_orders_delete').val() + '" href="open-orders.php?delete_id=' + json_elem.id + '&uniq=' + $('#uniq').val() + '"><i class="fa fa-times"></i></a></td>' : false;
		// 				// 	var string = '<tr class="ask_tr" id="ask_' + json_elem.id + '">' + crypto_hidden + usd_price + type + '<td>' + mine + '<span class="' + reorder_class + '">' + fa_symbol + '</span><span class="order_price">' + (formatCurrency((json_elem.btc_price > 0) ? json_elem.btc_price : json_elem.stop_price, is_crypto)) + '</span> ' + ((parseFloat(json_elem.btc_price) != parseFloat(json_elem.fiat_price)) ? '<a title="' + $('#orders_converted_from').val().replace('[currency]', currency_abbr) + '" class="fa fa-exchange" href="" onclick="return false;"></a>' : '') + '</td><td><span class="order_amount">' + json_elem.btc + '</span></td><td><span class="' + reorder_class + '">' + fa_symbol + '</span><span class="order_value">' + formatCurrency(parseFloat(json_elem.btc) * parseFloat(json_elem.btc_price), is_crypto) + '</span></td>' + edit_str + '</tr>';

		// 				// 	if (double)
		// 				// 		string += '<tr class="ask_tr double" id="ask_' + json_elem.id + '">' + crypto_hidden + '<td><div class="identify stop_order">S</div></td><td>' + mine + '<span class="' + reorder_class + '">' + fa_symbol + '</span><span class="order_price">' + (formatCurrency(json_elem.stop_price, is_crypto)) + '</span></td><td><span class="order_amount">' + json_elem.btc + '</span></td><td>' + fa_symbol + '<span class="order_value">' + formatCurrency(parseFloat(json_elem.btc) * parseFloat(json_elem.btc_price), is_crypto) + '</span></td><td><span class="oco"><i class="fa fa-arrow-up"></i> OCO</span></td></tr>';
		// 				// }
		// 				// else
		// 					var string = '<tr class="ask_tr" id="ask_' + json_elem.id + '"><td>' + mine + '<span class="order_amount">' + json_elem.btc + '</span> ' + ($('#curr_abbr_' + json_elem.c_currency).val()) + '</td><td><span class="buy_currency_char">' + fa_symbol + '</span><span class="order_price">' + (formatCurrency(json_elem.btc_price, is_crypto)) + '</span> ' + ((parseFloat(json_elem.btc_price) != parseFloat(json_elem.fiat_price)) ? '<a title="' + $('#orders_converted_from').val().replace('[currency]', currency_abbr) + '" class="fa fa-exchange" href="" onclick="return false;"></a>' : '') + '</td></tr>';

		// 				if (before)
		// 					var elem = $(string).insertBefore(insert_elem);
		// 				else
		// 					var elem = $(string).insertAfter(insert_elem);

		// 				$(elem).children('td').effect("highlight", { color: "#A2EEEE" }, 2000);
		// 			}
		// 		});

		// 		sortTable('#asks_list', ((notrades) ? 0 : 1), 0, sort_column);
		// 	}
		// 	else {
		// 		$('#no_asks').css('display', '');
		// 	}

		// 	if ($("#graph_orders").length > 0 && (depth_chart_data.bids.length > 0 || depth_chart_data.asks > 0))
		// 		graphOrders(depth_chart_data);

		// 	if (parseFloat(json_data.last_price) && $('#last_price').length > 0) {
		// 		var lp_prev = $('#last_price').val();
		// 		var lp_now = $('<div/>').html((is_crypto ? '' : json_data.fa_symbol) + formatCurrency(json_data.last_price, 2, 8) + (is_crypto ? ' ' + json_data.fa_symbol : '') + json_data.last_price_curr).text();
		// 		$('#last_price').val(lp_now);

		// 		if (json_data.last_trans_color == 'price-green')
		// 			$('#last_price').removeClass('price-red').addClass(json_data.last_trans_color);
		// 		else
		// 			$('#last_price').removeClass('price-green').addClass(json_data.last_trans_color);

		// 		if (lp_prev != lp_now)
		// 			$('#last_price').effect("highlight", { color: "#A2EEEE" }, 1000);
		// 	}

		// 	var current_price = ($('#asks_list .order_price').length > 0) ? parseFloat($('#asks_list .order_price:first').html().replace(',', '')) : 0;
		// 	var current_bid = ($('#bids_list .order_price').length > 0) ? parseFloat($('#bids_list .order_price:first').html().replace(',', '')) : 0;

		// 	if ($('#buy_price').length > 0 && $('#buy_price').is('[readonly]') && current_price > 0) {
		// 		$('#buy_price').val(parseFloat(current_price));
		// 		$("#buy_price").trigger("change");
		// 	}
		// 	if ($('#sell_price').length > 0 && $('#sell_price').is('[readonly]') && current_bid > 0) {
		// 		$('#sell_price').val(parseFloat(current_bid));
		// 		$("#sell_price").trigger("change");
		// 	}

		// 	if (current_price > 0)
		// 		$('#buy_market_price').prop('readonly', '');
		// 	else
		// 		$('#buy_market_price').prop('readonly', 'readonly');
		// 	if (current_bid > 0)
		// 		$('#sell_market_price').prop('readonly', '');
		// 	else
		// 		$('#sell_market_price').prop('readonly', 'readonly');

		// 	$('#buy_user_available').html(json_data.available_fiat);
		// 	$('#sell_user_available').html(json_data.available_btc);
		// 	reorderLabels(($('#is_crypto').val() == 'Y'));
		// });
		
	}, (!notrades ? 2000 : 5000));
}

function formatCurrency(amount, is_btc, flex) {
	if (isNaN(parseFloat(amount)))
		return '0';

	amount = parseFloat(amount).toFixed(8);
	var decimal_sep = $('#cfg_decimal_separator').val();
	var thousands_sep = $('#cfg_thousands_separator').val();
	var dec_amount = (typeof is_btc != 'number') ? (is_btc ? 8 : 2) : is_btc;

	if (flex && String(amount).indexOf('.') >= 0) {
		flex = (typeof flex != 'number') ? 8 : flex;
		amount = String(amount);
		dec_detect = amount.split('.')[1].replace(/[^0-9]/g, '').length - amount.split('.')[1].replace(/[^0-9]/g, '').replace(/^[0]+/g, '').length;
		if (parseFloat(amount.split('.')[1]) > 0) {
			dec_amount = Math.max(dec_amount, dec_detect + 1);
			dec_amount = (dec_amount > flex) ? flex : dec_amount;
		}
	}

	var string = parseFloat(amount).toFixed(dec_amount).toString();
	if (string.indexOf('.') >= 0) {
		var string_parts = string.split('.');
		string = string_parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, $('#cfg_thousands_separator').val()) + $('#cfg_decimal_separator').val() + string_parts[1];
		return string;
	}

	return parseFloat(amount).toFixed(dec_amount).toString().replace('.', $('#cfg_decimal_separator').val()).replace(/\B(?=(\d{3})+(?!\d))/g, $('#cfg_thousands_separator').val());
}

// function updateTransactionsList() {
// 	if (!($('#refresh_transactions').length > 0))
// 		return false;

// 	var gmt_offset = parseInt($('#gmt_offset').val()) * -1;
// 	var update = setInterval(function () {
// 		var c_currency = $('#c_currency1').val();
// 		var currency = $('#graph_orders_currency').val();
// 		var type = $('#type').val();
// 		var order_by = $('#order_by').val();
// 		var page = $('#page').val();

// 		$.getJSON("includes/ajax.transactions.php?c_currency=" + c_currency + '&currency=' + currency + '&type=' + type + '&order_by=' + order_by + '&page=' + page, function (transactions) {
// 			if (transactions != null) {
// 				var last = false;
// 				$.each(transactions, function (i) {
// 					var transaction = transactions[i];
// 					var this_transaction = $('#transaction_' + transaction.id);

// 					if (this_transaction.length > 0) {
// 						last = this_transaction;
// 						return;
// 					}

// 					var this_fa_symbol = '<span class="buy_currency_char">' + $('#curr_sym_' + transaction.currency).val() + '</span>';
// 					var string = '<tr id="transaction_' + transaction.id + '"><input type="hidden" class="is_crypto" value="' + transaction.is_crypto + '" />';
// 					string += '<td>' + transaction.type + '</td>';
// 					string += '<td><input type="hidden" class="localdate" value="' + (parseInt(transaction.datestamp) + gmt_offset) + '" /></td>';
// 					string += '<td>' + ((parseFloat(transaction.btc_net)).toFixed(8)) + '</td>';
// 					string += '<td><span class="currency_char">' + this_fa_symbol + '</span><span>' + formatCurrency(transaction.btc_net * transaction.fiat_price, (transaction.is_crypto == 'Y')) + '</span></td>';
// 					string += '<td><span class="currency_char">' + this_fa_symbol + '</span><span>' + formatCurrency(transaction.fiat_price, (transaction.is_crypto == 'Y')) + '</span></td>';
// 					string += '<td><span class="currency_char">' + this_fa_symbol + '</span><span>' + formatCurrency(transaction.fee * transaction.fiat_price, (transaction.is_crypto == 'Y')) + '</span></td>';
// 					string += '</tr>';

// 					var elem = $(string).insertAfter((last) ? $(last) : $('#table_first'));
// 					$(elem).children('td').effect("highlight", { color: "#A2EEEE" }, 2000);
// 					$('#no_transactions').css('display', 'none');

// 					localDates();
// 					last = this_transaction;
// 				});
// 				reorderRowLabels();
// 			}
// 		});
// 	}, 5000);
// }

function updateStats() {
	var update = setInterval(function () {
		var currency = $('#graph_price_history_currency').val();
		$.getJSON("includes/ajax.stats.php?currency=" + currency, function (json_data) {
			$('#stats_open').html(json_data.open);
			$('#stats_market_cap').html(json_data.market_cap.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
			$('#stats_total_btc').html(json_data.total_btc.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
			$('#stats_trade_volume').html(json_data.trade_volume.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
		});
	}, 3600000);
}

function filtersUpdate() {
	$('#filters select').bind("keyup change", function () {
		$('#filters_area').append('<div class="tp-loader"></div>');
		var url = $('#filters').attr('action');
		var query = $('#filters').serialize();

		if ($('#share-screen').length > 0) {
			var id = ($('.ts_view.selected').attr('data-option') == 'dividends') ? '#filters_area' : '#filters_area1';
			var bypass = ($('.ts_view.selected').attr('data-option') == 'dividends') ? 'bypass' : 'bypass1';
			$(id).load(url + '?page=1&' + bypass + '=1&' + query, function () {
				paginationUpdate();
			});
		}
		else {
			$('#filters_area').load(url + '?page=1&bypass=1&' + query, function () {
				paginationUpdate();
				localDates();
			});
		}
	});
}

function paginationUpdate() {
	$('.pagination a').click(function (e) {
		if ($('#share-screen').length > 0) {
			var id = ($('.ts_view.selected').attr('data-option') == 'dividends') ? '#filters_area' : '#filters_area1';
			var bypass = ($('.ts_view.selected').attr('data-option') == 'dividends') ? 'bypass' : 'bypass1';
		}
		else {
			var bypass = 'bypass';
			var id = '#filters_area';
		}


		$('#filters_area').append('<div class="tp-loader"></div>');
		var url = $(this).attr('href');
		var query = $('#filters').serialize();

		$(id).load(url + '&' + bypass + '=1&' + query, function () {
			paginationUpdate();
			localDates();
		});
		e.preventDefault();
		return false;
	});
}

function switchBuyCurrency() {
	$('#buy_currency,#sell_currency').bind("keyup change", function () {
		var currency = $(this).val();
		var c_currency = $('#c_currency').val();
		$.getJSON("includes/ajax.get_currency.php?currency=" + currency + '&c_currency=' + c_currency, function (json_data) {
			if ($('#unit_cost').length > 0) {
				$('#usd_ask').val(json_data.currency_info.usd_ask);
				$('.sell_currency_label,.buy_currency_label,.currency_label').html(currency.toUpperCase());
				$('.sell_currency_char,.buy_currency_char,.currency_char').html(((json_data.currency_info.is_crypto != 'Y') ? json_data.currency_info.fa_symbol : ''));
				$('#buy_currency,#sell_currency').val(currency);
				$('#user_available').html(((json_data.currency_info.is_crypto != 'Y') ? json_data.available_fiat : json_data.available_btc));
				$('#shares_earned_conv').html(formatCurrency(parseFloat($('#shares_earned_usd').val()) / json_data.currency_info.usd_ask, (json_data.currency_info.is_crypto == 'Y')));
				$('#shares_payed_conv').html(formatCurrency(parseFloat($('#shares_payed_usd').val()) / json_data.currency_info.usd_ask, (json_data.currency_info.is_crypto == 'Y')))
				$('#unit_price_buy').html(formatCurrency(parseFloat($('#unit_cost').val()) / parseFloat($('#usd_ask').val()), (json_data.currency_info.is_crypto == 'Y')));
				$('#unit_price_sell').html(formatCurrency(parseFloat($('#unit_cost_sell').val()) / parseFloat($('#usd_ask').val()), (json_data.currency_info.is_crypto == 'Y')));
				$('#is_crypto').val(json_data.currency_info.is_crypto);
				calculateBuyPrice();
				return false;
			}

			$('#filters_area').load('buy-sell.php?bypass=1&currency=' + currency);
			$('#buy_currency,#sell_currency').val(currency);
			$('.sell_currency_label,.buy_currency_label').html(json_data.currency_info.currency);
			$('.sell_currency_char,.buy_currency_char').html(json_data.currency_info.fa_symbol);
			$('#buy_price').val(json_data.current_ask.toString().replace('.', $('#cfg_decimal_separator').val()));
			$('#sell_price').val(json_data.current_bid.toString().replace('.', $('#cfg_decimal_separator').val()));
			$('#sell_user_available').html(json_data.available_btc);
			$('#buy_user_available').html(json_data.available_fiat);
			$('#this_currency_id').val(json_data.currency_info.id);
			$('#is_crypto').val(json_data.currency_info.is_crypto);
			$('#fiat_currency').val(json_data.currency_info.id);
			$('#stats_last_price').html(formatCurrency(json_data.stats.last_price, 2, 8));
			$('#stats_daily_change_abs').html(formatCurrency(Math.abs(parseFloat(json_data.stats.daily_change)), 2, 8));
			$('#stats_daily_change_perc').html(formatCurrency(Math.abs(parseFloat(json_data.stats.daily_change_percent))));
			$('#stats_min').html(formatCurrency(json_data.stats.min, 2, 8));
			$('#stats_max').html(formatCurrency(json_data.stats.max, 2, 8));
			$('#stats_open').html(formatCurrency(json_data.stats.open, 2, 8));
			$('#stats_market_cap').html(formatCurrency(json_data.stats.market_cap, 2, 8));
			$('#stats_trade_volume').html(formatCurrency(json_data.stats.trade_volume, 2, 8));

			if (json_data.currency_info.is_crypto == 'Y') {
				$('.buy_currency_char,.sell_currency_char').addClass('cc');
				$('.stat1 .buy_currency_char,.stat2 .buy_currency_char,.trades .buy_currency_char').each(function () {
					$(this).parent().find('span:not(.cc):first').after(this);
				});
				$('.trades .buy_currency_char').each(function () {
					if ($(this).parent().find('a').length > 0)
						var el = $(this).parent().find('a:first');
					else
						var el = $(this).parent().find('span:not(.cc):first');

					$(el).after($(this));
				});
			}
			else {
				$('.buy_currency_char,.sell_currency_char').removeClass('cc');
				$('.stat1 .buy_currency_char,.stat2 .buy_currency_char,.trades .buy_currency_char').each(function () {
					$(this).parent().find('span:not(.cc):first').before(this);
				});
				$('.trades .buy_currency_char').each(function () {
					if ($(this).parent().find('a').length > 0)
						var el = $(this).parent().find('a:first');
					else
						var el = $(this).parent().find('span:not(.cc):first');

					$(el).before($(this));
				});
			}

			if ($('.c_currencies_prices').length > 0) {
				$('.c_currencies_prices').each(function () {
					var abbr = $(this).find('.c_currency_abbr').val();
					$(this).find('.price').html(formatCurrency(json_data.market_stats[abbr].last_price, 2, 4));
					$(this).find('.percent').html(formatCurrency(Math.abs(json_data.market_stats[abbr].daily_change_percent)));
				});
			}

			if ($(".ticker").length > 0) {
				$('#graph_price_history_currency').val(currency);
				$('#graph_orders_currency').val(currency);
				graphPriceHistory(true);
				graphOrders(false, true);
			}

			reorderLabels((json_data.currency_info.is_crypto == 'Y'));
			calculateBuyPrice();
		});
	});
}

function reorderLabels(is_crypto) {
	if (is_crypto) {
		$('.buy_currency_char,.sell_currency_char').addClass('cc');
		$('.stat1 .buy_currency_char,.stat2 .buy_currency_char,.trades .buy_currency_char').each(function () {
			if (($(this).siblings('.stats_min').length > 0))
				$(this).siblings('span:not(.cc):first').after(this);
			else
				$(this).siblings('span:not(.cc):last').after(this);
		});
		$('.trades .buy_currency_char').each(function () {
			if ($(this).siblings('a').length > 0)
				var el = $(this).siblings('a:not(.fa):first');
			else
				var el = $(this).siblings('span:not(.cc):first');

			$(el).after($(this));
		});
		$('.stat1').css('fontSize', '18px');
	}
	else {
		$('.buy_currency_char,.sell_currency_char').removeClass('cc');
		$('.stat1 .buy_currency_char,.stat2 .buy_currency_char,.trades .buy_currency_char').each(function () {
			$(this).siblings('span:not(.cc):first').before(this);
		});
		$('.trades .buy_currency_char').each(function () {
			if ($(this).siblings('a').length > 0)
				var el = $(this).siblings('a:not(.fa):first');
			else
				var el = $(this).siblings('span:not(.cc):first');

			$(el).before($(this));
		});
		$('.stat1').css('fontSize', '20px');
	}

	reorderRowLabels();
}

function reorderRowLabels() {
	$('.currency_char').each(function () {
		var is_crypto = $(this).parents('tr').find('.is_crypto').val();
		if ($(this).siblings('a').length > 0)
			var el = $(this).siblings('a:not(.fa):first');
		else
			var el = $(this).siblings('span:not(.cc):first');

		if (is_crypto == 'Y')
			$(el).after($(this));
		else
			$(el).before($(this));
		$(this).addClass('cc');
	});
}

function calculateBuy() {
	$('#buy_amount,#buy_price,#buy_stop_price,#sell_amount,#sell_price,#sell_stop_price').bind("keyup change", function () {
		calculateBuyPrice();
	});

	$('#btc_amount,#fiat_amount').bind("keyup change", function () {
		calculateWithdrawal();
	});

	$('#buy_amount,#buy_price,#sell_amount,#sell_price,#fiat_amount,#btc_amount,#buy_stop_price,#sell_stop_price').bind("keypress", function (e) {
		var charCode = (e.which) ? e.which : e.keyCode;
		var k = String.fromCharCode(charCode);
		var dec = $('#cfg_decimal_separator').val();
		var tho = $('#cfg_thousands_separator').val();

		if (charCode != 46 && charCode != 39 && charCode != 37 && charCode > 31 && (charCode < 48 || charCode > 57) && k != dec && k != tho)
			return false;

		return true;
	});

	$('#buy_amount,#buy_price,#sell_amount,#sell_price,#fiat_amount,#btc_amount,#buy_stop_price,#sell_stop_price').focus(function () {
		if (!(parseFloat($(this).val()) > 0))
			$(this).val('');
	});

	$('#buy_amount,#buy_price,#sell_amount,#sell_price,#fiat_amount,#btc_amount,#buy_stop_price,#sell_stop_price').blur(function () {
		if (!(parseFloat($(this).val()) > 0))
			$(this).val('0');
	});

	$('#buy_market_price,#sell_market_price').click(function () {
		if ($(this).is('[readonly]')) {
			alert($('#buy_errors_no_compatible').val());
			$(this).prop('checked', '');
		}
		else {
			$(this).prop('checked', 'checked');
		}
	});

	$('#buy_market_price').click(function () {
		if ($(this).is(':checked') && !$(this).is('[readonly]')) {
			$('#buy_stop').prop('checked', '');
			$('#buy_limit').prop('checked', '');
			$('#buy_price_market_label').css('display', '');
			$('#buy_price_limit_label').css('display', 'none');
			$('#buy_price').attr('readonly', 'readonly');
			$('#buy_price_container').css('display', '');

			if ($('#buy_limit').is(':checked'))
				$('#buy_stop_container').hide(400);
			else
				$('#buy_stop_container').css('display', 'none');

			calculateBuyPrice();
		}
	});

	$('#sell_market_price').click(function () {
		if ($(this).is(':checked') && !$(this).is('[readonly]')) {
			$('#sell_stop').prop('checked', '');
			$('#sell_limit').prop('checked', '');
			$('#sell_price_market_label').css('display', '');
			$('#sell_price_limit_label').css('display', 'none');
			$('#sell_price').attr('readonly', 'readonly');
			$('#sell_price_container').css('display', '');

			if ($('#sell_limit').is(':checked'))
				$('#sell_stop_container').hide(400);
			else
				$('#sell_stop_container').css('display', 'none');

			calculateBuyPrice();
		}
	});

	$('#buy_stop').click(function () {
		if ($(this).is(':checked')) {
			$('#buy_market_price').prop('checked', '');
			$('#buy_price').removeAttr('readonly');
			if ($('#buy_limit').is(':checked')) {
				$('#buy_stop_container').show(400);
			}
			else {
				$('#buy_stop_container').css('display', '');
				$('#buy_price_container').css('display', 'none');
			}
		}
		else {
			if ($('#buy_limit').is(':checked')) {
				$('#buy_stop_container').hide(400);
			}
			else {
				$(this).prop('checked', 'checked');
			}
		}
		calculateBuyPrice();
	});

	$('#sell_stop').click(function () {
		if ($(this).is(':checked')) {
			$('#sell_market_price').prop('checked', '');
			$('#sell_price').removeAttr('readonly');
			if ($('#sell_limit').is(':checked')) {
				$('#sell_stop_container').show(400);
			}
			else {
				$('#sell_stop_container').css('display', '');
				$('#sell_price_container').css('display', 'none');
			}
		}
		else {
			if ($('#sell_limit').is(':checked')) {
				$('#sell_stop_container').hide(400);
			}
			else {
				$(this).prop('checked', 'checked');
			}
		}
		calculateBuyPrice();
	});

	$('#buy_limit').click(function () {
		if ($(this).is(':checked')) {
			$('#buy_market_price').prop('checked', '');
			$('#buy_price').removeAttr('readonly');
			$('#buy_price_market_label').css('display', 'none');
			$('#buy_price_limit_label').css('display', '');

			if ($('#buy_stop').is(':checked')) {
				$('#buy_price_container').show(400);
			}
			else {
				$('#buy_price_container').css('display', '');
				$('#buy_stop_container').css('display', 'none');
			}
		}
		else {
			if ($('#buy_stop').is(':checked')) {
				$('#buy_price_container').hide(400);
			}
			else {
				$(this).prop('checked', 'checked');
			}
		}
		calculateBuyPrice();
	});

	$('#sell_limit').click(function () {
		if ($(this).is(':checked')) {
			$('#sell_market_price').prop('checked', '');
			$('#sell_price').removeAttr('readonly');
			$('#sell_price_market_label').css('display', 'none');
			$('#sell_price_limit_label').css('display', '');

			if ($('#sell_stop').is(':checked')) {
				$('#sell_price_container').show(400);
			}
			else {
				$('#sell_price_container').css('display', '');
				$('#sell_stop_container').css('display', 'none');
			}
		}
		else {
			if ($('#sell_stop').is(':checked')) {
				$('#sell_price_container').hide(400);
			}
			else {
				$(this).prop('checked', 'checked');
			}
		}
		calculateBuyPrice();
	});

	$('#method').bind("keyup change", function () {
		if ($(this).val() != 'google') {
			$('.method_show').show(400);
		}
		else {
			$('.method_show').hide(400);
		}
	});
}

function calculateBuyPrice(all) {

	var bonus_amount = $("#bonus_amount").val();

	if ($('#unit_cost').length > 0) {
		var unit_cost = (parseFloat($('#unit_cost').val()) / parseFloat($('#usd_ask').val())).toFixed(($('#is_crypto').val() == 'Y' ? '8' : '2'));
		var unit_cost_sell = (parseFloat($('#unit_cost_sell').val()) / parseFloat($('#usd_ask').val())).toFixed(($('#is_crypto').val() == 'Y' ? '8' : '2'));
		var buy_amount = ($('#buy_amount').val()) ? parseFloat($('#buy_amount').val().replace(',', '')) : 0;
		var sell_amount = ($('#sell_amount').val()) ? parseFloat($('#sell_amount').val().replace(',', '')) : 0;
		$('#buy_total').html(formatCurrency(buy_amount * unit_cost, ($('#is_crypto').val() == 'Y')));
		$('#sell_total').html(formatCurrency(sell_amount * unit_cost_sell, ($('#is_crypto').val() == 'Y')));
		return false;
	}

	var user_fee = parseFloat($('#user_fee').val());
	var user_fee1 = parseFloat($('#user_fee1').val());
	var dec = $('#cfg_decimal_separator').val();
	var tho = $('#cfg_thousands_separator').val();

	var first_ask = ($('#asks_list .order_price').length > 0) ? parseFloat($('#asks_list .order_price:first').html().replace(tho, '')) : 0;
	var buy_amount = ($('#buy_amount').val()) ? parseFloat($('#buy_amount').val().replace(tho, '')) : 0;
	var buy_price = ($('#buy_price').val()) ? parseFloat($('#buy_price').val().replace(tho, '')) : 0;
	var buy_stop_price = ($('#buy_stop_price').val()) ? parseFloat($('#buy_stop_price').val().replace(tho, '')) : 0;
	var buy_fee = (buy_price >= first_ask || $('#buy_market_price').is(':checked')) ? user_fee : user_fee1;
	var buy_subtotal = buy_amount * (($('#buy_stop').is(':checked') && !$('#buy_limit').is(':checked')) ? buy_stop_price : buy_price);
	
	var buy_commision = (buy_fee * 0.01) * buy_subtotal;
	//
	var ref_status = $("#ref_status").val();
	if (ref_status == 1 || ref_status == "1") {
		if(document.getElementById('is_referral').checked) {
			if (parseFloat(bonus_amount) > parseFloat(buy_commision)) {
				buy_commision = parseFloat(0); 
			} else {
				buy_commision = parseFloat(buy_commision) - parseFloat(bonus_amount); 
			}
		}
	}
	
	//
	var buy_total = buy_subtotal + buy_commision;
	console.log("User fee "+user_fee);
	console.log("User fee1 "+user_fee1);
	$('#buy_subtotal').html(formatCurrency(buy_subtotal, ($('#is_crypto').val() == 'Y')));
	$('#buy_total').html(formatCurrency(buy_total, ($('#is_crypto').val() == 'Y')));
	$('#buy_user_fee').html((buy_price >= first_ask || $('#buy_market_price').is(':checked')) ? user_fee.toFixed(2) : user_fee1.toFixed(2));

	var first_bid = ($('#bids_list .order_price').length > 0) ? parseFloat($('#bids_list .order_price:first').html().replace(tho, '')) : 0;
	var sell_amount = ($('#sell_amount').val()) ? parseFloat($('#sell_amount').val().replace(tho, '')) : 0;
	var sell_price = ($('#sell_price').val()) ? parseFloat($('#sell_price').val().replace(tho, '')) : 0;
	var sell_stop_price = ($('#sell_stop_price').val()) ? parseFloat($('#sell_stop_price').val().replace(tho, '')) : 0;
	var sell_fee = ((sell_price > 0 && sell_price <= first_bid) || $('#sell_market_price').is(':checked')) ? user_fee : user_fee1;
	var sell_subtotal = sell_amount * (($('#sell_stop').is(':checked') && !$('#sell_limit').is(':checked')) ? sell_stop_price : sell_price);
	
	var sell_commision = (sell_fee * 0.01) * sell_subtotal;

	if (ref_status == 1 || ref_status == "1") {
		if(document.getElementById('is_referral_sell').checked) {
		    if (parseFloat(bonus_amount) > parseFloat(sell_commision)) {
				sell_commision = parseFloat(0); 
			} else {
				sell_commision = parseFloat(sell_commision) - parseFloat(bonus_amount); 
			}
		}
	}
	
	
	var sell_total = sell_subtotal - sell_commision;
	$('#sell_subtotal').html(formatCurrency(sell_subtotal, ($('#is_crypto').val() == 'Y')));
	$('#sell_total').html(formatCurrency(sell_total, ($('#is_crypto').val() == 'Y')));
	$('#sell_user_fee').html(((sell_price > 0 && sell_price <= first_bid) || $('#sell_market_price').is(':checked')) ? user_fee.toFixed(2) : user_fee1.toFixed(2));
}

function setFullBalance() {
	$('#buy_user_available').click(function (e) {
		e.preventDefault();

		var fiat_amount = parseFloat($(this).text().replace($('#cfg_thousands_separator').val(), ''));
		var fee = parseFloat($('#user_fee').val()) * 0.01;
		var limit_price = parseFloat($('#buy_price').val().replace($('#cfg_thousands_separator').val(), ''));

		$('#buy_amount').val(((Math.round((fiat_amount / limit_price) * 100000000) / 100000000) - ((Math.round((fiat_amount / limit_price) * 100000000) / 100000000) * fee)).toString());
		calculateBuyPrice();
	});
	$('#sell_user_available').click(function (e) {
		e.preventDefault();

		$('#sell_amount').val($(this).text().replace($('#cfg_thousands_separator').val(), ''));
		calculateBuyPrice();
	});
}

function buttonDisable() {
	$('form').submit(function () {
		$('.but_user').addClass('loading');
		$('.but_user').attr('disabled', 'disabled');
	});
}

function localDates() {
	var h24 = ($('#cfg_time_24h').val() == 'Y');
	$('.localdate').each(function () {
		var date = new Date(parseInt($(this).val() * 1000));
		//var offset = date.getTimezoneOffset() * 60;
		//var date1 = new Date(parseInt((parseInt($(this).val()) + parseInt(offset))*1000));
		var hours = date.getHours();
		var minutes = date.getMinutes();
		var ampm = hours >= 12 ? 'pm' : 'am';
		if (!h24) {
			hours = hours % 12;
			hours = hours ? hours : 12; // the hour '0' should be '12'
		}
		minutes = minutes < 10 ? '0' + minutes : minutes;
		var strTime = hours + ':' + minutes + (!h24 ? ' ' + ampm : '');

		$(this).parent().html($('#javascript_mon_' + date.getMonth()).val() + ' ' + date.getDate() + ', ' + date.getFullYear() + ', ' + strTime);
	});
}

function timeSince(elem) {
	var miliseconds = $(elem).siblings('.time_since_seconds').val();
	var date1 = new Date(parseInt(miliseconds) * 1000);
	date1 = (date1.getTime() > Date.now()) ? new Date() : date1;

	$(elem).countdown({
		since: date1,
		significant: 1,
		layout: '{o<}{on} {ol}{o>}{w<}{wn} {wl}{w>}{d<}{dn} {dl}{d>}{h<}{hn} {hl}{h>}{m<}{mn} {ml}{m>}{s<}{sn} {sl}{s>}'
	});
}

/*
function timeUntil(elem) {
	var miliseconds = $(elem).siblings('.time_until_seconds').val();
	var date = new Date(parseInt(miliseconds)*1000);
	var lang = $('#language_selector').val();
	lang = (lang = 'zh') ? 'zh-CN' : lang;
	
		
	$(elem).countdown({ 
	    until: date,
	    significant: 1,
	    onExpiry: pageRefresh,
	    layout: '{o<}{on} {ol}{o>}{w<}{wn} {wl}{w>}{d<}{dn} {dl}{d>}{h<}{hn} {hl}{h>}{m<}{mn} {ml}{m>}{s<}{sn} {sl}{s>}'
	});
}
*/

function pageRefresh() {
	//location.reload(); 
	$('.error').remove();
}

function startFileSortable() {

}

function switchAccount() {
	$('#deposit_bank_account').bind("keyup change", function () {
		$.getJSON("includes/ajax.get_bank_account.php?account=" + $(this).val(), function (json_data) {
			$('#client_account').html(json_data.client_account);
			$('#escrow_account').html(json_data.escrow_account);
			$('#escrow_name').html(json_data.escrow_name);
		});
	});
}
function switchAccount1() {
	$('#withdraw_account').bind("keyup change", function () {
		$.getJSON("includes/ajax.get_bank_account.php?avail=1&account=" + $(this).val(), function (json_data) {
			$('.currency_label').html(json_data.currency);
			$('.currency_char').html(json_data.currency_char);
			$('#user_available').html(json_data.available);
			calculateWithdrawal();
		});
	});
}

function calculateWithdrawal() {
	var btc_amount = ($('#btc_amount').val()) ? parseFloat($('#btc_amount').val().replace(window.tho, '')) : 0;
	var btc_fee = ($('#withdraw_btc_network_fee').html()) ? parseFloat($('#withdraw_btc_network_fee').html().replace(window.tho, '')) : 0;
	var btc_total = (btc_amount > 0) ? btc_amount - btc_fee : 0;
	var fiat_amount = ($('#fiat_amount').val()) ? parseFloat($('#fiat_amount').val().replace(window.tho, '')) : 0;
	var fiat_fee = ($('#withdraw_fiat_fee').html()) ? parseFloat($('#withdraw_fiat_fee').html().replace(window.tho, '')) : 0;
	var fiat_total = (fiat_amount > 0) ? fiat_amount - fiat_fee : 0;
	$('#withdraw_btc_total').html(formatCurrency(btc_total, 8));
	$('#withdraw_fiat_total').html(formatCurrency(fiat_total));
}

function expireSession() {
	if ($('#is_logged_in').val() > 0) {
		var init_time = Math.round(new Date().getTime() / 1000);
		var checker = setInterval(function () {
			var curtime = Math.round(new Date().getTime() / 1000);
			if (curtime - init_time >= 900) {
				clearInterval(checker);
				window.location.href = 'logout.php?log_out=1';
			}
		}, 5);
	}
}

function sortTable(elem_selector, col_num, desc, col_name) {
	var rows = $(elem_selector + ' tr:not(:first,.double)').get();
	var c_curr_abbr = $('#curr_abbr_' + $('#c_currency').val()).val()
	desc = (col_name == '.order_date' || col_name == '.order_amount') ? true : desc;

	if (!col_name) {
		if ($('.usd_price').length > 0)
			col_name = '.usd_price';
		else
			col_name = '.order_price';
	}

	rows.sort(function (a, b) {
		if ($(a).children('th').length > 0)
			return -1;

		var A = (col_name != '.order_price') ? parseFloat($(a).find(col_name).val()) : parseFloat($(a).find('.order_price').eq(col_num).text().replace('$', '').replace(',', '').replace(c_curr_abbr, ''));
		var B = (col_name != '.order_price') ? parseFloat($(b).find(col_name).val()) : parseFloat($(b).find('.order_price').eq(col_num).text().replace('$', '').replace(',', '').replace(c_curr_abbr, ''));
		A = (isNaN(A)) ? 0 : A;
		B = (isNaN(B)) ? 0 : B;

		if (A < B) {
			return (desc) ? 1 : -1;
		}

		if (A > B) {
			return (desc) ? -1 : 1;
		}
		return 0;
	});

	$.each(rows, function (index, row) {
		$(elem_selector).append(row);
		var id = $(row).attr('id');
		$('#' + id + '.double').insertAfter(row);
	});
}

function blink(selector) {
	var selector = (!selector) ? '.blink' : selector;
	if (!($(selector).length > 0))
		return false;

	var i = 0;
	var on = false;
	var elems = $(selector);
	var blink = setInterval(function () {
		$(elems).toggleClass('blink');

		if (i > 5 && !on) {
			clearInterval(blink);
			$(elems).removeClass('blink');
		}
		i++;
		on = (!on);
	}, 300);
}

function confirmDeleteAll(uniq, e) {
	e.preventDefault();

	if (!uniq)
		return false;

	var r = confirm($('#order-cancel-all-conf').val());
	if (r == true) {
		window.location.href = 'open-orders.php?delete_all=1&uniq=' + uniq;
	}
}

function startTicker() {
	var elem = $('.ticker .scroll');
	var elem_f = $('.ticker .scroll');
	var elem_sub_l = $('.ticker .scroll a:last');
	var elem_sub_l_w = elem_sub_l.outerWidth();
	var elem_w = elem.outerWidth();
	var window_w = $(window).width();
	var cloned = false;

	var properties = {
		duration: 400, easing: 'linear', complete: function () {
			$('.ticker .scroll').animate({ left: '-=50px' }, properties);
		}, progress: function () {
			offset = elem_sub_l.offset();
			if (elem_sub_l && offset) {
				if ((window_w + 500) - offset.left > 0) {
					elem = $('.ticker .scroll:last').clone().css('left', (offset.left + elem_sub_l_w) + 'px').insertAfter('.ticker .scroll:last');
					elem_sub_l = elem.find('a:last');
					cloned = true;
				}
			}
			if (elem_f && elem_f.offset()) {
				if ((elem_f.offset().left * -1) >= elem_f.outerWidth() && cloned) {
					elem_f.remove();
					elem_f = $('.ticker .scroll:first');
					cloned = false;
				}
			}

		}
	};
	elem.animate({ left: '-=50px' }, properties);
}

function hideStickyMenu(show) {
	if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
		if (show) {
			$('#trueHeader').animate({ top: "0" }, 300);
		}
		else {
			var h = $('#trueHeader').outerHeight();
			$('#trueHeader').animate({ top: '-' + h + 'px' }, 300);
		}
	}
}

$(document).ready(function () {
	if ($(".ticker").length > 0) {
		var currency = $('#graph_price_history_currency').val();
		startTicker();
		graphPriceHistory();
		graphClickAdd();
		setFullBalance();
	}

	if ($("#graph_orders").length > 0 && $("#graph_orders").is(':visible')) {
		graphOrders();
		//var update = setInterval(graphOrders,10000);
		updateTransactions();
	}

	if ($('#open_orders_user').length > 0)
		updateTransactions();

	if ($('#user_fee').length > 0)
		updateTransactions();

	if ($('.graph_options').length > 0) {
		graphControls();
	}

	if ($('.time_since').length > 0) {
		$('.time_since').each(function () {
			timeSince(this);
		});
	}

	if ($('#share-screen').length > 0) {
		shareControls();
	}

	$('.change_c_currency,#c_currency').bind("keyup change", function () {
		window.location.href = window.location.pathname + '?c_currency=' + $(this).val();
	});

	$('#language_selector').bind("keyup change", function () {
		var lang = $(this).val();
		var alternate = $('[hreflang="' + lang + '"][rel="alternate"]').attr('href');

		if (typeof alternate == 'undefined') {
			var url = window.location.pathname;
			alternate = url.substring(url.lastIndexOf('/') + 1) + '?lang=' + lang;
		}

		window.location.href = alternate;
	});

	$('#currency_selector').bind("keyup change", function () {
		var lang = $('#language_selector').val();
		var url = $('#url_' + 'index_php' + '_' + lang).val();
		window.location.href = url + '?currency=' + $(this).val();
	});

	$('#fee_currency').bind("keyup change", function () {
		var lang = $('#language_selector').val();
		var url = $('#url_' + 'fee-schedule_php' + '_' + lang).val();
		window.location.href = url + '?currency=' + $(this).val();
	});

	$('#ob_currency').bind("keyup change", function () {
		var lang = $('#language_selector').val();
		var url = $('#url_' + 'order-book_php' + '_' + lang).val();
		window.location.href = url + '?currency=' + $(this).val();
	});

	if ($("#transactions_timestamp").length > 0) {
		updateTransactions();
		updateStats();
	}

	$('#enable_tfa [name="sms"]').click(function () {
		$('#send_sms').val('1');
		return true;
	});

	$('#enable_tfa [name="google"]').click(function () {
		$('#google_2fa').val('1');
		return true;
	});

	$('#cancel_transaction').click(function () {
		$('#cancel').val('1');
		return true;
	});


	if (($('#is_crypto').length > 0 && $('#is_crypto').val() == 'Y') || ('.currency_char').length > 0) {
		$('.buy_currency_char,.sell_currency_char').addClass('cc');
		reorderLabels(($('#is_crypto').val() == 'Y'));
	}

	$('input:text,select').focusin(function () {
		hideStickyMenu();
	});
	$('input:text,select').focusout(function () {
		hideStickyMenu(true);
	});

	var first_text = $('input:text').first();
	if (first_text.length > 0) {
		if ($(first_text).val() == '0')
			$(first_text).val('').focus().trigger("focus");
		else if (!($(first_text).val().length > 0))
			$(first_text).focus().trigger("focus");

		hideStickyMenu();
	}

	$(window).resize(function () {
		graphResize();
	});

	$(window).scroll(function () {
		if ($(this).scrollTop() > 100) {
			$('.scrollup').fadeIn();
		} else {
			$('.scrollup').fadeOut();
		}
	});

	$('.scrollup').click(function () {
		$("html, body").animate({ scrollTop: 0 }, 500);
		return false;
	});

	selectnav('tiny', {
		label: '--- Navigation --- ',
		indent: '-'
	});

	ddsmoothmenu.init({
		mainmenuid: "menu",
		orientation: 'h',
		classname: 'menu',
		contentsource: "markup"
	})

	filtersUpdate();
	paginationUpdate();
	switchBuyCurrency();
	calculateBuy();
	buttonDisable();
	localDates();
	switchAccount();
	switchAccount1();
	//expireSession();
	updateTransactionsList();
	//timeUntil();
	blink();
});

(function () {
	window.dec = $('#cfg_decimal_separator').val();
	window.tho = $('#cfg_thousands_separator').val();
	var _parseFloat = window.parseFloat;
	window.parseFloat = function (number) {
		if (typeof number == 'string') {
			if (number.match(/(\.{1})([0-9]{0,8})$/))
				return _parseFloat(number);

			return _parseFloat(number.toString().replace(window.tho, '').replace(window.dec, '.'));
		}
		else
			return _parseFloat(number);
	};
})();

// Sticky Menu Core
var Modernizr = function () { }
//jQuery(function (t) { var n, e, i, o, r, s, a, u, l, h, c, p, d, f, g, m, v, y, $, w, C, x, k, b, T, I, H, M, P, S, z, O, q, A, F, L, B, D, N, V, E, K, _; return K = t(window), z = t(document), window.App = {}, n = t("body, html"), o = t("#header"), s = t("#headerPush"), i = t("#footer"), O = "easeInOutExpo", k = "/wp-content/themes/KIND", b = 39, C = 37, P = 38, v = 40, y = 27, N = navigator.userAgent, q = N.match(/(Android|iPhone|BlackBerry|Opera Mini)/i), A = N.match(/(iPad|Kindle)/i), E = function () { return window.innerHeight || K.height() }, _ = function () { return window.innerWidth || K.width() }, V = function (t) { return K.resize(function () { return t() }), K.bind("orientationchange", function () { return t() }) }, r = o.find(".button"), m = o.find("#trueHeader"), D = m.outerHeight(), L = 0 === t("#masthead").length && 0 === t("#slideshow").length, B = m.offset().top, F = B + 5 * D, setInterval(function () { var t; return t = K.scrollTop(), o.css({ height: o.outerHeight() }), t >= B ? (o.addClass("sticky"), 0 >= B || r.hasClass("inv") && r.removeClass("transparent inv")) : (o.removeClass("sticky"), 0 >= B || r.hasClass("inv") || r.addClass("transparent inv")), t >= F ? o.addClass("condensed") : o.removeClass("condensed") }, 10), 1 === (c = t("#slideshow")).length && (I = function () { function t() { this.$slideshow = c, this.$slides = this.$slideshow.find(".slide"), this.max = this.$slides.length - 1 } return t.prototype.autoplay = function () { var t = this; if (0 !== this.max) return this.interval = setInterval(function () { return t.next() }, 6e3), this }, t.prototype.clear = function () { return clearInterval(this.interval) }, t.prototype.goToSlide = function (t) { var n, e, i = this; if (t !== this.current) return null != this.interval && this.clear(), t > this.max && (t = 0), 0 > t && (t = this.max), this.$slides.removeClass("active"), this.$slides.eq(t).addClass("active"), this.$h2 = this.$slides.eq(t).find("h2"), setTimeout(function () { return i.typeOutSteps() }, 1e3), (n = this.$slides.eq(t).find(".product_pledged")) && (e = Math.round(parseInt(n.text())), n.text(("" + e).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,")), n.css({ opacity: 1 })), this.current = t, this.autoplay(), this }, t.prototype.next = function () { return this.goToSlide(this.current + 1) }, t.prototype.prev = function () { return this.goToSlide(this.current - 1) }, t.prototype.typeOutSteps = function () { var t, n, e, i, o, r, s = this; return n = 0, i = this.$h2.data().sequences.split(","), t = e = i[n], r = function (r) { var a, u; return null == r && (r = 0), n !== i.length ? (u = 0, a = function () { var l; return r++ , l = t.substring(0, r), s.$h2.text(l), s.$h2.addClass("typing"), clearInterval(u), r !== t.length ? u = setInterval(a, s.human()) : (n++ , s.$h2.removeClass("typing"), n !== i.length ? (r = 0, e = t, t = i[n], setTimeout(o, s.human(10))) : void 0) }, a()) : void 0 }, r(), o = function () { var n, i, o, a, u, l, h, c, p, d, f, g; for (a = e.split(" "), n = t.split(" "), p = "", l = 0, c = f = 0, g = a.length; g > f; c = ++f)d = a[c], d === n[c] && (l++ , l > c && (p += d + " ")); return u = t.length, h = p.length > 0 ? p.length - 1 : 0, o = 0, i = function () { var t; return t = e.substring(0, u), s.$h2.text(t), s.$h2.addClass("typing"), u-- , clearInterval(o), u === h ? (s.$h2.removeClass("typing"), setTimeout(function () { return r(h) }, s.human(5))) : o = setInterval(i, s.human()) }, i() }, this }, t.prototype.human = function (t) { return null == t && (t = 1), Math.round(170 * Math.random() + 30) * t }, t }(), T = new I, setTimeout(function () { return T.goToSlide(0) }, 100), t("#slideDecor").bind("click tap", function () { return n.stop(1, 1).animate({ scrollTop: t("#content").offset().top - 100 }, 660, O) }), f = t("#theVideo"), t("#slideshow .button.transparent").on("click tap", function (t) { return t.preventDefault(), n.stop(1, 1).animate({ scrollTop: f.offset().top - o.outerHeight() }, 450, function () { return S(f.find("iframe")) }) })), Modernizr.csstransitions && (l = t("[data-parallax]")).length >= 1 && K.bind("load scroll touchmove", function () { var t, n, e; return e = K.scrollTop(), n = parseFloat(.35 * e), n -= 148 - n, t = "center " + n + "px", l.css({ backgroundPosition: t }) }), 1 === (g = t("#timeline")).length && (M = function () { function n() { this.$timeline = g, this.$fx = this.$timeline.find("#timelineFx"), this.$items = this.$timeline.find("article") } return n.prototype.checkPosition = function (n) { return null == n && (n = K.scrollTop()), this.$items.each(function (e, i) { var o; return i = t(i), o = i.offset().top, n >= o - K.height() ? i.css({ opacity: 1 }) : void 0 }), this }, n.prototype.resizeFx = function () { var t; if (null != this.$timeline) return t = this.$timeline.outerHeight(), t -= this.$timeline.find("article:visible:last-child").outerHeight(), this.$fx.css({ height: t }), this }, n }(), H = new M, K.load(function () { return H.resizeFx() }), setInterval(function () { return H.checkPosition() }, 100)), w = function () { function t() { this.zoom = 15, this.lat = 30.191969, this.long = -98.084782 } return t.prototype.init = function () { return this.map = new GMaps({ div: "#mapCanvas", zoom: this.zoom, lat: this.lat, lng: this.long, zoomControlOpt: { style: "SMALL", position: "TOP_LEFT" }, zoomControl: !0, panControl: !0, streetViewControl: !0, mapTypeControl: !1, scrollwheel: !1 }), this.addMarker(), this }, t.prototype.addMarker = function () { return this.map.addMarker({ lat: this.lat, lng: this.long, icon: k + "/assets/images/icon-marker.png" }), this }, t }(), 1 === (u = t("#mapCanvas")).length && ($ = new w, window.onload = function () { return $.init() }, K.bind("load resize", function () { var t; return t = K.height() - o.outerHeight() + 72, 888 > t && (t = 888), u.css({ height: t }), $.map.setCenter($.lat, $.long) })), 1 === (e = t("#faqListing")).length && e.find(".faqs").isotope({ itemSelector: ".faq" }), (p = t(".specs")).length >= 1 && p.each(function () { var n, e, i; return i = t(this), e = i.find("figure"), n = i.find("aside"), e.next().is("aside") && e.outerHeight() < n.outerHeight() ? e.css({ height: n.outerHeight() + 50 }) : void 0 }), 1 === (d = t("#thePosts")).length && d.infinitescroll({ navSelector: "#postNav", nextSelector: "#postNav a:first-child", itemSelector: "#thePosts .post" }), 1 === (h = t("#popup")).length && (x = function () { function n() { var n = this; this.$popup = h, this.$content = this.$popup.find("#popupContent"), this.$popup.add(t("#close")).bind("click tap", function () { return n.close() }), this.$content.bind("click tap", function (t) { return t.stopPropagation() }), K.bind("keydown", function (t) { return t.keyCode === y ? n.close() : void 0 }), this.$content.find("a").bind("click tap", function () { return window.location = t(this).attr("href") }) } return n.prototype.open = function (n) { var e = this; return t("#popupContent-load").empty().append(t(n).html()), this.$popup.stop(1, 1).fadeIn(750, function () { var t; return 1 === (t = e.$popup.find("iframe")).length ? S(t) : void 0 }), this }, n.prototype.close = function () { var t = this; return this.$popup.stop(1, 1).fadeOut(750, function () { return t.$popup.find("#popupContent-load").empty() }), this }, n }(), window.App.PopupModal = new x), S = function (t) { var n, e; return n = t[0], e = n.src, e.match(/autoplay/) ? (n.src = e.replace("autoplay=0", "autoplay=1"), console.log(n.src)) : n.src += 0 > e.indexOf("?") ? "?autoplay=1" : "&autoplay=1" }, t('[data-action="revealVideo"]').on("click tap", function () { var n, e, i; return i = t(this), e = i.parents("figure"), n = e.find("iframe"), n.addClass("over"), S(n) }), t("iframe:not(.skip)").each(function (n, e) { var i, o; return e = t(this)[0], o = "wmode=transparent", i = e.src, e.src += 0 > i.indexOf("?") ? "?" + o : "&" + o }), 1 === (a = t(".id-widget-wrap .main-btn")).length ? (a.text("Back Us"), a.css({ opacity: 1 })) : void 0 });

/* SelectNav.js (v. 0.1)
 * Converts your <ul>/<ol> navigation into a dropdown list for small screens */
window.selectnav = function () { "use strict"; var a = function (a, b) { function l(a) { var b; a || (a = window.event), a.target ? b = a.target : a.srcElement && (b = a.srcElement), b.nodeType === 3 && (b = b.parentNode), b.value && (window.location.href = b.value) } function m(a) { var b = a.nodeName.toLowerCase(); return b === "ul" || b === "ol" } function n(a) { for (var b = 1; document.getElementById("selectnav" + b); b++); return a ? "selectnav" + b : "selectnav" + (b - 1) } function o(a) { i++; var b = a.children.length, c = "", k = "", l = i - 1; if (!b) return; if (l) { while (l--) k += g; k += " " } for (var p = 0; p < b; p++) { var q = a.children[p].children[0], r = q.innerText || q.textContent, s = ""; d && (s = q.className.search(d) !== -1 || q.parentElement.className.search(d) !== -1 ? j : ""), e && !s && (s = q.href === document.URL ? j : ""), c += '<option value="' + q.href + '" ' + s + ">" + k + r + "</option>"; if (f) { var t = a.children[p].children[1]; t && m(t) && (c += o(t)) } } return i === 1 && h && (c = '<option value="">' + h + "</option>" + c), i === 1 && (c = '<select class="selectnav" id="' + n(!0) + '">' + c + "</select>"), i-- , c } a = document.getElementById(a); if (!a) return; if (!m(a)) return; document.documentElement.className += " js"; var c = b || {}, d = c.activeclass || "active", e = typeof c.autoselect == "boolean" ? c.autoselect : !0, f = typeof c.nested == "boolean" ? c.nested : !0, g = c.indent || "→", h = c.label || "- Navigation -", i = 0, j = " selected "; a.insertAdjacentHTML("afterend", o(a)); var k = document.getElementById(n()); return k.addEventListener && k.addEventListener("change", l), k.attachEvent && k.attachEvent("onchange", l), k }; return function (b, c) { a(b, c) } }();

//** Smooth Navigational Menu- By Dynamic Drive DHTML code library: http://www.dynamicdrive.com
//** Script Download/ instructions page: http://www.dynamicdrive.com/dynamicindex1/ddlevelsmenu/
var ddsmoothmenu = {
	arrowimages: { down: [], right: [] },
	transition: { overtime: 300, outtime: 300 }, //duration of slide in/ out animation, in milliseconds
	shadow: { enable: false, offsetx: 5, offsety: 5 }, //enable shadow?
	showhidedelay: { showdelay: 100, hidedelay: 200 }, //set delay in milliseconds before sub menus appear and disappear, respectively
	// end cfg
	detectwebkit: navigator.userAgent.toLowerCase().indexOf("applewebkit") != -1, //detect WebKit browsers (Safari, Chrome etc)
	detectie6: document.all && !window.XMLHttpRequest,

	getajaxmenu: function ($, setting) { //function to fetch external page containing the panel DIVs
		var $menucontainer = $('#' + setting.contentsource[0]) //reference empty div on page that will hold menu
		$menucontainer.html("Loading Menu...")
		$.ajax({
			url: setting.contentsource[1], //path to external menu file
			async: true,
			error: function (ajaxrequest) {
				$menucontainer.html('Error fetching content. Server Response: ' + ajaxrequest.responseText)
			},
			success: function (content) {
				$menucontainer.html(content)
				ddsmoothmenu.buildmenu($, setting)
			}
		})
	},


	buildmenu: function ($, setting) {
		// var smoothmenu = ddsmoothmenu
		// var $mainmenu = $("#" + setting.mainmenuid + ">ul") //reference main menu UL
		// $mainmenu.parent().get(0).className = setting.classname || "ddsmoothmenu"
		// var $headers = $mainmenu.find("ul").parent()
		// $headers.hover(
		// 	function (e) {
		// 		$(this).children('a:eq(0)').addClass('selected')
		// 	},
		// 	function (e) {
		// 		$(this).children('a:eq(0)').removeClass('selected')
		// 	}
		// )
		// $headers.each(function (i) { //loop through each LI header
		// 	var $curobj = $(this).css({}) //reference current LI header
		// 	var $subul = $(this).find('ul:eq(0)').css({ display: 'block' })
		// 	$subul.data('timers', {})
		// 	this._dimensions = { w: this.offsetWidth, h: this.offsetHeight, subulw: $subul.outerWidth(), subulh: $subul.outerHeight() }
		// 	this.istopheader = $curobj.parents("ul").length == 1 ? true : false //is top level header?
		// 	$subul.css({ top: this.istopheader && setting.orientation != 'v' ? this._dimensions.h + "px" : 0 })
		// 	$curobj.children("a:eq(0)").css(this.istopheader ? { paddingRight: smoothmenu.arrowimages.down[2] } : {})
		// 	if (smoothmenu.shadow.enable) {
		// 		this._shadowoffset = { x: (this.istopheader ? $subul.offset().left + smoothmenu.shadow.offsetx : this._dimensions.w), y: (this.istopheader ? $subul.offset().top + smoothmenu.shadow.offsety : $curobj.position().top) } //store this shadow's offsets
		// 		if (this.istopheader)
		// 			$parentshadow = $(document.body)
		// 		else {
		// 			var $parentLi = $curobj.parents("li:eq(0)")
		// 			$parentshadow = $parentLi.get(0).$shadow
		// 		}
		// 		this.$shadow = $('<div class="ddshadow' + (this.istopheader ? ' toplevelshadow' : '') + '"></div>').prependTo($parentshadow).css({ left: this._shadowoffset.x + 'px', top: this._shadowoffset.y + 'px' })  //insert shadow DIV and set it to parent node for the next shadow div
		// 	}
		// 	$curobj.hover(
		// 		function (e) {
		// 			var $targetul = $subul //reference UL to reveal
		// 			var header = $curobj.get(0) //reference header LI as DOM object
		// 			clearTimeout($targetul.data('timers').hidetimer)
		// 			$targetul.data('timers').showtimer = setTimeout(function () {
		// 				header._offsets = { left: $curobj.offset().left, top: $curobj.offset().top }
		// 				var menuleft = header.istopheader && setting.orientation != 'v' ? 0 : header._dimensions.w
		// 				menuleft = (header._offsets.left + menuleft + header._dimensions.subulw > $(window).width()) ? (header.istopheader && setting.orientation != 'v' ? -header._dimensions.subulw + header._dimensions.w : -header._dimensions.w) : menuleft //calculate this sub menu's offsets from its parent
		// 				if ($targetul.queue().length <= 1) { //if 1 or less queued animations
		// 					$targetul.css({ left: menuleft + "px", width: header._dimensions.subulw + 'px' }).animate({ height: 'show', opacity: 'show' }, ddsmoothmenu.transition.overtime)
		// 					if (smoothmenu.shadow.enable) {
		// 						var shadowleft = header.istopheader ? $targetul.offset().left + ddsmoothmenu.shadow.offsetx : menuleft
		// 						var shadowtop = header.istopheader ? $targetul.offset().top + smoothmenu.shadow.offsety : header._shadowoffset.y
		// 						if (!header.istopheader && ddsmoothmenu.detectwebkit) { //in WebKit browsers, restore shadow's opacity to full
		// 							header.$shadow.css({ opacity: 1 })
		// 						}
		// 						header.$shadow.css({ overflow: '', width: header._dimensions.subulw + 'px', left: shadowleft + 'px', top: shadowtop + 'px' }).animate({ height: header._dimensions.subulh + 'px' }, ddsmoothmenu.transition.overtime)
		// 					}
		// 				}
		// 			}, ddsmoothmenu.showhidedelay.showdelay)
		// 		},
		// 		function (e) {
		// 			var $targetul = $subul
		// 			var header = $curobj.get(0)
		// 			clearTimeout($targetul.data('timers').showtimer)
		// 			$targetul.data('timers').hidetimer = setTimeout(function () {
		// 				$targetul.animate({ height: 'hide', opacity: 'hide' }, ddsmoothmenu.transition.outtime)
		// 				if (smoothmenu.shadow.enable) {
		// 					if (ddsmoothmenu.detectwebkit) { //in WebKit browsers, set first child shadow's opacity to 0, as "overflow:hidden" doesn't work in them
		// 						header.$shadow.children('div:eq(0)').css({ opacity: 0 })
		// 					}
		// 					header.$shadow.css({ overflow: 'hidden' }).animate({ height: 0 }, ddsmoothmenu.transition.outtime)
		// 				}
		// 			}, ddsmoothmenu.showhidedelay.hidedelay)
		// 		}
		// 	) //end hover
		// }) //end $headers.each()
		// $mainmenu.find("ul").css({ display: 'none', visibility: 'visible' })
	},

	init: function (setting) {
		if (typeof setting.customtheme == "object" && setting.customtheme.length == 2) { //override default menu colors (default/hover) with custom set?
			var mainmenuid = '#' + setting.mainmenuid
			var mainselector = (setting.orientation == "v") ? mainmenuid : mainmenuid + ', ' + mainmenuid
			document.write('<style type="text/css">\n'
				+ mainselector + ' ul li a {background:' + setting.customtheme[0] + ';}\n'
				+ mainmenuid + ' ul li a:hover {background:' + setting.customtheme[1] + ';}\n'
				+ '</style>')
		}
		this.shadow.enable = (document.all && !window.XMLHttpRequest) ? false : this.shadow.enable //in IE6, always disable shadow
		jQuery(document).ready(function ($) { //ajax menu?
			if (typeof setting.contentsource == "object") { //if external ajax menu
				ddsmoothmenu.getajaxmenu($, setting)
			}
			else { //else if markup menu
				ddsmoothmenu.buildmenu($, setting)
			}
		})
	}

}
