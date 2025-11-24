<?php

namespace App\Http\Helpers;

use Morilog\Jalali\Jalalian;

class JalaliDate
{
    /**
     * Convert a Gregorian date to Jalali date
     *
     * @param string|null $date Gregorian date in Y-m-d format
     * @param string $format Output format for Jalali date
     * @return string Formatted Jalali date
     */
    public static function toJalali($date = null, $format = 'Y/m/d')
    {
        if (is_null($date)) {
            return Jalalian::now()->format($format);
        }
        
        return Jalalian::fromDateTime($date)->format($format);
    }
    
    /**
     * Convert a Jalali date to Gregorian date
     *
     * @param int $year Jalali year
     * @param int $month Jalali month
     * @param int $day Jalali day
     * @param string $format Output format for Gregorian date
     * @return string Formatted Gregorian date
     */
    public static function toGregorian($year, $month, $day, $format = 'Y-m-d')
    {
        return Jalalian::fromFormat('Y/m/d', "$year/$month/$day")->toCarbon()->format($format);
    }
    
    /**
     * Get current Jalali date with custom format
     *
     * @param string $format Output format
     * @return string Formatted current Jalali date
     */
    public static function now($format = 'Y/m/d')
    {
        return Jalalian::now()->format($format);
    }
    
    /**
     * Get Jalali month name
     *
     * @param int $month Month number (1-12)
     * @return string Persian month name
     */
    public static function getMonthName($month)
    {
        $months = [
            1 => 'فروردین',
            2 => 'اردیبهشت',
            3 => 'خرداد',
            4 => 'تیر',
            5 => 'مرداد',
            6 => 'شهریور',
            7 => 'مهر',
            8 => 'آبان',
            9 => 'آذر',
            10 => 'دی',
            11 => 'بهمن',
            12 => 'اسفند'
        ];
        
        return $months[$month] ?? '';
    }
    
    /**
     * Get Jalali day of week name
     *
     * @param int $day Day of week (0-6)
     * @return string Persian day name
     */
    public static function getDayName($day)
    {
        $days = [
            0 => 'شنبه',
            1 => 'یکشنبه',
            2 => 'دوشنبه',
            3 => 'سه شنبه',
            4 => 'چهارشنبه',
            5 => 'پنجشنبه',
            6 => 'جمعه'
        ];
        
        return $days[$day] ?? '';
    }
    
    /**
     * Format a Carbon date instance to Jalali date
     *
     * @param \Carbon\Carbon $carbon Carbon date instance
     * @param string $format Output format for Jalali date
     * @return string Formatted Jalali date
     */
    public static function fromCarbon($carbon, $format = 'Y/m/d')
    {
        return Jalalian::fromCarbon($carbon)->format($format);
    }
} 