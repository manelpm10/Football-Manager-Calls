<?php
/**
 * Common functions to manage time.
 */
class Time
{
	/**
	 * Get first day of the last month in Y-m-d format.
	 *
	 * @return string
	 */
	public static function getFirstDayOfLastMonth()
	{
		return date( 'Y-m-d', strtotime( ' '. date( 'F Y', strtotime( '-1 month' ) ) ) );
	}

	/**
	 * Get last day of the last month in n/Y format.
	 *
	 * @return string
	 */
	public static function getLastMonth()
	{
		return date( 'n/Y', strtotime( '-1day '. date( 'F Y' ) ) );
	}

	/**
	 * Get last month in Y-m-d format.
	 *
	 * @return string
	 */
	public static function getLastDayOfLastMonth()
	{
		return date( 'Y-m-d', strtotime( '-1day '. date( 'F Y' ) ) );
	}

	/**
	 * Get first day of the month of the date passed as argument in Y-m-d format.
	 *
	 * @return string
	 */
	public static function getFirstDayOfMonth( $date = false )
	{
		if ( $date === false )
		{
			return date( 'Y-m-01' );
		}
		else
		{
			return date( 'Y-m-d', strtotime( date( 'Y-m-01', strtotime( $date ) ) ) );
		}
	}

	/**
	 * Get last day of month in Y-m-d format.
	 *
	 * @return string
	 */
	public static function getLastDayOfMonth( $date )
	{
		return date( 'Y-m-d', strtotime( '-1day '. date( 'F Y', strtotime( $date.' +1 month' ) ) ) );
	}
	/**
	 * Get day of the last week in Y-m-d format from its name.
	 *
	 * @return string
	 */
	public static function getDayOfLastWeek( $day )
	{
		$time_of_requested_day = strtotime( $day );
		$requested_day = date( 'w', $time_of_requested_day );
		$requested_day = ( $requested_day == 0 ) ? 6 : $requested_day - 1;

		$current = 'today';

		$current_day = date( 'w', strtotime( $current ) );
		$current_day = ( $current_day == 0 ) ? 6 : $current_day - 1;

		$time_diff = $requested_day - $current_day;

		if ( $current_day > 1 )
		{
			$time_diff -= 7;
		}
		else
		{
			$time_diff -= 14;
		}

		return date( 'Y-m-d', strtotime( $current.' '.$time_diff.' days' ) );
	}

	/**
	 * Gets an array with all the start dates of months between from and to dates.
	 *
	 * @param string $from Start date.
	 * @param string $to End date.
	 * @return array
	 */
	public static function getMonthlyTimeInterval( $from, $to )
	{
		$numeric_index = 1;
		$dates = array();

		for( $date_index = Time::getFirstDayOfMonth( $from ); $date_index <= $to; $date_index = Time::getFirstDayOfMonth( date( 'Y-m-d', strtotime( $date_index.' +1 month' ) ) ) )
		{
			$dates[$numeric_index++] = $date_index;
		}

		return $dates;
	}

	/**
	 * Get latest year months.
	 *
	 * @return array
	 */
	public static function getLatestYearMonths()
	{
		$i = 0;
		while ( $i <= 12 )
		{
			$dates[] = date( 'Y-m-d', strtotime( ' '. date( 'F Y', strtotime( '-' . $i . ' month' ) ) ) );
			$i++;
		}

		return $dates;
	}

	/**
	 * Return the date less number of months requested.
	 *
	 * @param date $date Start date.
	 * @param integer $num_months Number of months.
	 * @return date
	 */
	public static function getMonthsAgoFromDate( $date, $num_months = 1 )
	{
		return date( 'Y-m-d', strtotime( $date . ' -' . $num_months . ' month' ) );
	}

	/**
	 * Return the number of months between two dates.
	 *
	 * @param date $date1 Date from.
	 * @param date $date2 Date to.
	 * @return integer
	 */
	public static function numMonthsBetweenDates( $date1, $date2 )
	{
		$date_comp = Time::compareDates( $date1, $date2 );
		if( $date_comp < 1 )
		{
			$time1			= strtotime( $date2 );
			$time2			= strtotime( $date1 );
		}
		elseif( $date_comp > 1 )
		{
			$time1			= strtotime( $date1 );
			$time2			= strtotime( $date2 );
		}
		else
		{
			return 0;
		}

		$day_diff		= date( 'j', $time1 ) - date( 'j', $time2 );
		$months_diff	= date( 'n', $time1 ) - date( 'n', $time2 );
		$year_diff		= date( 'Y', $time1 ) - date( 'Y', $time2 );

		$months			= $months_diff + ( $year_diff * 12 );
		if ( $day_diff < 0 )
		{
			$months = $months - 1;
		}
		elseif ( $day_diff > 0 )
		{
			$months = $months + 1;
		}

		return $months; 
	}

	/**
	 * Compare two dates. Depending on the relation between them, one of the following result code is returned in the system variable "result":
	 * 
	 * $date1 < $date2		-1
	 * $date1 = $date2		0
	 * $date1 > $date2		1
	 *
	 * @param date $date1 Date from.
	 * @param date $date2 Date to.
	 * @return integer
	 */
	public static function compareDates( $date1, $date2 )
	{
		$date1 = strtotime( $date1 );
		$date2 = strtotime( $date2 );

		if ( $date1 < $date2 )
		{
			return -1;
		}
		elseif( $date1 > $date2 )
		{
			return 1;
		}

		return 0;
	}
}
