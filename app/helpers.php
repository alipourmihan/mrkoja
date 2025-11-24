<?php

use App\Classes\JDF;

if (!function_exists('jdate')) {
    function jdate($format, $timestamp = '', $none = '', $time_zone = 'Asia/Tehran', $tr_num = 'fa') {
        $jdf = new JDF();
        print_r($format);
        die();
        return $jdf->jdate($format, $timestamp, $none, $time_zone, $tr_num);
    }
}

if (!function_exists('jdate_en')) {
    function jdate_en($format, $timestamp = '', $none = '', $time_zone = 'Asia/Tehran') {
        $jdf = new JDF();
        return $jdf->jdate_en($format, $timestamp, $none, $time_zone);
    }
}

if (!function_exists('jstrftime')) {
    function jstrftime($format, $timestamp = '', $none = '', $time_zone = 'Asia/Tehran', $tr_num = 'fa') {
        $jdf = new JDF();
        return $jdf->jstrftime($format, $timestamp, $none, $time_zone, $tr_num);
    }
}

if (!function_exists('jmktime')) {
    function jmktime($h = '', $m = '', $s = '', $jm = '', $jd = '', $jy = '', $none = '', $timezone = 'Asia/Tehran') {
        $jdf = new JDF();
        return $jdf->jmktime($h, $m, $s, $jm, $jd, $jy, $none, $timezone);
    }
}

if (!function_exists('jgetdate')) {
    function jgetdate($timestamp = '', $none = '', $timezone = 'Asia/Tehran', $tn = 'en') {
        $jdf = new JDF();
        return $jdf->jgetdate($timestamp, $none, $timezone, $tn);
    }
}

if (!function_exists('jcheckdate')) {
    function jcheckdate($jm, $jd, $jy) {
        $jdf = new JDF();
        return $jdf->jcheckdate($jm, $jd, $jy);
    }
}

if (!function_exists('tr_num')) {
    function tr_num($str, $mod = 'en', $mf = '٫') {
        $jdf = new JDF();
        return $jdf->tr_num($str, $mod, $mf);
    }
}

if (!function_exists('jdate_words')) {
    function jdate_words($array, $mod = '') {
        $jdf = new JDF();
        return $jdf->jdate_words($array, $mod);
    }
}

if (!function_exists('gregorian_to_jalali')) {
    function gregorian_to_jalali($gy, $gm, $gd, $mod = '') {
        $jdf = new JDF();
        return $jdf->gregorian_to_jalali($gy, $gm, $gd, $mod);
    }
}

if (!function_exists('jalali_to_gregorian')) {
    function jalali_to_gregorian($jy, $jm, $jd, $mod = '') {
        $jdf = new JDF();
        return $jdf->jalali_to_gregorian($jy, $jm, $jd, $mod);
    }
}

// Higher level helper functions for common use cases
if (!function_exists('to_jalali')) {
    function to_jalali($date, $format = 'Y/m/d', $convert_numbers = true) {
        if (!$date) return null;
        
        if ($convert_numbers) {
            return jdate($format, strtotime($date));
        }
        return jdate_en($format, strtotime($date));
    }
}

if (!function_exists('to_gregorian')) {
    function to_gregorian($jalali_date) {
        if (!$jalali_date) return null;
        
        $jdf = new JDF();
        return $jdf->gregorianDate($jalali_date);
    }
}

if (!function_exists('jalali_date')) {
    function jalali_date($time) {
        $jdf = new JDF();
        return $jdf->jalaliDate($time);
    }
}

if (!function_exists('gregorian_date')) {
    function gregorian_date($time) {
        $jdf = new JDF();
        return $jdf->gregorianDate($time);
    }
}

if (!function_exists('gregorian_date2')) {
    function gregorian_date2($time) {
        $jdf = new JDF();
        return $jdf->gregorianDate2($time);
    }
}

if (!function_exists('gregorian_to_solar_months')) {
    function gregorian_to_solar_months($month) {
        $jdf = new JDF();
        return $jdf->gregorianToSolarMonths($month);
    }
}

if (!function_exists('jalali_year')) {
    function jalali_year($year) {
        $jdf = new JDF();
        return $jdf->jalaliYear($year);
    }
}

if (!function_exists('is_jalali_leap_year')) {
    function is_jalali_leap_year($year = '') {
        $jdf = new JDF();
        return $jdf->isJalaliLeapYear($year);
    }
}

// Format helpers for common date formats
if (!function_exists('format_jalali_date')) {
    function format_jalali_date($timestamp, $format = '%A، %d %B %Y') {
        if (!$timestamp) return null;
        return jstrftime($format, strtotime($timestamp));
    }
}

if (!function_exists('format_jalali_datetime')) {
    function format_jalali_datetime($timestamp, $format = '%A، %d %B %Y ساعت H:i') {
        if (!$timestamp) return null;
        return jdate($format, strtotime($timestamp));
    }
}

if (!function_exists('jalali_month_days')) {
    function jalali_month_days($month, $year = '') {
        if ($month <= 6) return 31;
        if ($month <= 11) return 30;
        return is_jalali_leap_year($year) ? 30 : 29;
    }
}

if (!function_exists('get_jalali_month_name')) {
    function get_jalali_month_name($month_num) {
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
        return $months[$month_num] ?? '';
    }
}

if (!function_exists('get_jalali_day_name')) {
    function get_jalali_day_name($day_num) {
        $days = [
            0 => 'یکشنبه',
            1 => 'دوشنبه',
            2 => 'سه‌شنبه',
            3 => 'چهارشنبه',
            4 => 'پنج‌شنبه',
            5 => 'جمعه',
            6 => 'شنبه'
        ];
        return $days[$day_num] ?? '';
    }
} 