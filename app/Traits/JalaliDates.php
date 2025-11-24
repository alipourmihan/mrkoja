<?php

namespace App\Traits;

trait JalaliDates
{
    /**
     * Get the date in Jalali format
     *
     * @param string $value
     * @return string
     */
    public function getJalaliDateAttribute($field = 'created_at', $format = 'Y/m/d')
    {
        return to_jalali($this->$field, $format);
    }

    /**
     * Get the datetime in Jalali format
     *
     * @param string $value
     * @return string
     */
    public function getJalaliDateTimeAttribute($field = 'created_at', $format = 'Y/m/d H:i:s')
    {
        return to_jalali($this->$field, $format);
    }

    /**
     * Get formatted Jalali date
     *
     * @param string $value
     * @return string
     */
    public function getFormattedJalaliDateAttribute($field = 'created_at')
    {
        return format_jalali_date($this->$field);
    }
} 