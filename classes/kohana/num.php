<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Number helper class. Provides additional formatting methods that for working
 * with numbers.
 *
 * @package    Kohana
 * @category   Helpers
 * @author     Kohana Team
 * @copyright  (c) 2009 Kohana Team
 * @license    http://kohanaphp.com/license
 */
class Kohana_Num {

	/**
	 * Returns the English ordinal suffix (th, st, nd, etc) of a number.
	 *
	 *     echo 2, Num::ordinal(2);   // "2nd"
	 *     echo 10, Num::ordinal(10); // "10th"
	 *     echo 33, Num::ordinal(33); // "33rd"
	 *
	 * @param   integer  number
	 * @return  string
	 */
	public static function ordinal($number)
	{
		if ($number % 100 > 10 AND $number % 100 < 14)
		{
			return 'th';
		}

		switch ($number % 10)
		{
			case 1:
				return 'st';
			case 2:
				return 'nd';
			case 3:
				return 'rd';
			default:
				return 'th';
		}
	}

	/**
	 * Locale-aware number and monetary formatting.
	 *
	 *     // In English, "1,200.05"
	 *     // In Spanish, "1200,05"
	 *     // In Portuguese, "1 200,05"
	 *     echo Num::format(1200.05, 2);
	 *
	 *     // In English, "1,200.05"
	 *     // In Spanish, "1.200,05"
	 *     // In Portuguese, "1.200.05"
	 *     echo Num::format(1200.05, 2, TRUE);
	 *
	 * @param   float    number to format
	 * @param   integer  decimal places
	 * @param   boolean  monetary formatting?
	 * @return  string
	 * @since   3.0.2
	 */
	public static function format($number, $places, $monetary = FALSE)
	{
		$info = localeconv();

		if ($monetary)
		{
			$decimal   = $info['mon_decimal_point'];
			$thousands = $info['mon_thousands_sep'];
		}
		else
		{
			$decimal   = $info['decimal_point'];
			$thousands = $info['thousands_sep'];
		}

		return number_format($number, $places, $decimal, $thousands);
	}

	/**
	 * Round a number to a specified precision, using a specified tie breaking technique
	 * 
	 * @param float $value Number to round
	 * @param integer $precision Desired precision
	 * @param integer $mode Tie breaking mode, accepts the PHP_ROUND_HALF_* constants
	 * @return float Rounded number
	 */
	static public function round($value, $precision = 0, $mode = self::ROUND_HALF_UP)
	{
		if (version_compare(PHP_VERSION, '5.3', '>='))
		{
			return round($value, $precision, $mode);
		}

		if ($mode === self::ROUND_HALF_UP)
		{
			return round($value, $precision);
		}
		else
		{
			$factor = ($precision === 0) ? 1 : pow(10, $precision);
			
			switch ($mode)
			{
				case self::ROUND_HALF_DOWN:
					return floor($value * $factor) / $factor;
				break;

				case self::ROUND_HALF_EVEN:
				case self::ROUND_HALF_ODD:
					if (($value * $factor) - floor($value * $factor) === 0.5)
					{
						// Round up if the integer is odd and the round mode is set to even
						// or the integer is even and the round mode is set to odd.
						// Any other instance round down.
						$up = (!!(floor($value * $factor) & 1) === ($mode === self::ROUND_HALF_EVEN));

						if ($up)
						{
							$value = ceil($value * $factor);
						}
						else
						{
							$value = floor($value * $factor);
						}
						return $value / $factor;
					}
					else
					{
						return round($value, $precision);
					}
				break;
			}
		}
	}

} // End num
