<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Classes\JDF;

abstract class BaseModel extends Model
{

    
    /**
     * Override the getDateFormat to convert Gregorian to Jalali on the fly
     */
    public function getDateFormat()
    {
        return 'Y/m/d H:i:s';
    }

    /**
     * Override getAttribute to convert dates automatically
     */
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);
        // Check if value is a Carbon datetime instance
        if ($value instanceof \Carbon\Carbon) {
            $jdf = new JDF();
          
            // Check if time components are not zero (meaning time is set)
            $hasTime = $value->hour !== 0 || $value->minute !== 0 || $value->second !== 0;
            
            if ($hasTime) {
                // Format with time components
                return $jdf->jdate_en('Y/m/d H:i:s', $value->timestamp);
            } else {
                // Format as date only
                return $jdf->jdate_en('Y/m/d', $value->timestamp);
            }
        }
    
        return $value;
    }

    /**
     * Keep the ability to get original Carbon date when needed
     */
    public function getOriginalDate($key)
    {
        return parent::getAttribute($key);
    }

    /**
     * Format a date attribute with a custom format
     * 
     * @param string $key The attribute name
     * @param string $format The format to use (Jalali format)
     * @return string
     */
    public function getFormattedDate($key, $format = 'Y/m/d')
    {
        $value = $this->getOriginalDate($key);
        if ($value instanceof \Carbon\Carbon) {
            $jdf = new JDF();
            return $jdf->jdate_en($format, $value->timestamp);
        }
        return $value;
    }

    /**
     * Get the Gregorian date for database operations
     */
    public function fromDateTime($value)
    {
        if (empty($value)) {
            return $value;
        }

        // If it's already a Carbon instance, return it
        if ($value instanceof Carbon) {
            return $value->format($this->getDateFormat());
        }

        // Try to create a Carbon instance
        try {
            return Carbon::createFromFormat($this->getDateFormat(), $value)
                ->format($this->getDateFormat());
        } catch (\Exception $e) {
            return $value;
        }
    }

    /**
     * Override setAttribute to handle Jalali date inputs
     */
    public function setAttribute($key, $value)
    {
        // Check if this attribute is a date field
        if (in_array($key, $this->getDates())) {
            // If it's already a Carbon instance, use it as is
            if ($value instanceof Carbon) {
                return parent::setAttribute($key, $value);
            }

            // If it's a string, check if it's Jalali or Gregorian
            if (is_string($value)) {
                // Try to parse as Gregorian first
                try {
                    $carbon = Carbon::createFromFormat('Y-m-d H:i:s', $value);
                    return parent::setAttribute($key, $carbon);
                } catch (\Exception $e) {
                    // If not Gregorian, try to parse as Jalali
                    try {
                        // Check if it matches Jalali format (1402/12/25)
                        if (preg_match('/^(\d{4})\/(\d{1,2})\/(\d{1,2})/', $value, $matches)) {
                            $jdf = new JDF();
                            // Get the Gregorian components
                            $gregorian = $jdf->jalali_to_gregorian($matches[1], $matches[2], $matches[3], '-');
                            // Create Carbon instance from the Gregorian date
                            $carbon = Carbon::createFromFormat('Y-m-d', $gregorian);
                            return parent::setAttribute($key, $carbon);
                        }
                    } catch (\Exception $e) {
                        // If all parsing fails, store as is
                        return parent::setAttribute($key, $value);
                    }
                }
            }
        }

        // For non-date fields, use default behavior
        return parent::setAttribute($key, $value);
    }

    public function getDates()
    {
        $dates = parent::getDates();
        
        // Add any dates from $casts that are datetime
        foreach ($this->casts as $key => $value) {
            if ($value === 'datetime' || $value === 'date') {
                $dates[] = $key;
            }
        }
        
        return array_unique($dates);
    }
} 