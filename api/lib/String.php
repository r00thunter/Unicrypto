<?php
class String {
	public static function substring($string,$length=0,$start=0) {
		if ($length == 0) {
			return $string;
		}
		else {
			if (strlen($string) > $length) 
				$suffix = '...';
				
			$new_string = substr($string,$start,$length);
			if (($start + $length) < strlen($string))
				$new_string = $new_string.$suffix;
			return $new_string;
		}
	}
	
	public static function currency($amount,$crypto=false,$flex=false) {
		global $CFG;
		
		if (!empty($amount) && $amount > 0) {
			$thousands = ($CFG->thousands_separator) ? $CFG->thousands_separator : ',';
			$decimal = ($CFG->decimal_separator) ? $CFG->decimal_separator : '.';
			$dec_amount = (!is_numeric($crypto)) ? ($crypto ? 8 : 2) : $crypto;
			
			if ($flex) {
				$flex = (!is_numeric($flex)) ? 8 : $flex;
				$dec_detect = strlen(preg_replace("/[^0-9]/",'',strrchr($amount, "."))) - strlen(ltrim(preg_replace("/[^0-9]/",'',strrchr($amount, ".")),'0'));
				if (strrchr($amount, ".") > 0) {
					$dec_amount = max($dec_amount,$dec_detect + 1);
					$dec_amount = ($dec_amount > $flex) ? $flex : $dec_amount;
				}
			}
		 
			return number_format($amount,$dec_amount,$decimal,$thousands);
		}
		else {
			return 0;
		}
	}
	
	public static function currencyInput($amount) {
		global $CFG;
		
		return str_replace($CFG->decimal_separator,'.',str_replace($CFG->thousands_separator,'',preg_replace("/[^0-9.,]/", "",$amount)));
	}
	
	public static function currencyOutput($amount) {
		global $CFG;
	
		return rtrim(rtrim(number_format($amount,8,$CFG->decimal_separator,''),'0'),$CFG->decimal_separator);
	}
}
?>