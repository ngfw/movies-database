<?php

/**
 * Helper
 * @project Movie Database 2
 * @copyright (c) 2013, Nick Gejadze
 */
class Helper{

	/**
     * Holds Class Instance
     * @access protected 
     * @static
     * @var object
     */
    private static $instance = null;

    /**
     * if $instance is not set starts new Helper and return instance
     * @access public 
     * @static
     * @return object Class instance
     */
    public static function init() {
        if (self::$instance === null):
            self::$instance = new Helper;
        endif;
        return self::$instance;
    }

    /**
     * sort multidimensional array
     * @access public
     * @return mixed
     * @static
     * @param array $array
     * @param string $col
     * @param direction $string
     */
    public static function sort_multiarray($array, $col, $direction = 'ASC') {
        $arsort = array(); //initiate sort array
        if ($array and is_array($array)) : //make sure it's an array
            foreach ($array as $value) ://populate the sort array
                $arsort[] = $value[$col];
            endforeach;
            if ($direction == 'ASC'):
                array_multisort($arsort, SORT_ASC, $array); //ascending order
            else:
                array_multisort($arsort, SORT_DESC, $array); //descending order
            endif;
        return $array;                
        endif;
        return false;   //fail
    }

    /**
     * convert number of minutes to time
     * @access public
     * @param intiget $time
     * @param string $format
     * @static
     * @return string
     */
    public static function convertToHoursMins($time, $format = '%d:%d') {
        settype($time, 'integer');
        if ($time < 1):
            return;
        endif;
        $hours = floor($time/60);
        $minutes = $time%60;
        return sprintf($format, $hours, $minutes);
    }

    /**
     * shortener
     * @param mixed $str Description.
     * @access public
     * @static
     * @return mixed Value.
     */
    public static function shortener($str){
        if (strlen($str) >= 30):
            return substr($str, 0, 30). "...";
        else:
            return $str;
        endif;
    }

    /**
     * humanTiming
     * @param mixed $time
     * @access public
     * @static
     * @return mixed Value.
     */
    public static function humanTiming ($time){
        $time = time() - $time;
        $objects = ngfw\Registry::get("objects");
        $translation = $objects['translation'];
        $tokens = array (
            31536000 => $translation['year'],
            2592000 => $translation['month'],
            604800 => $translation['week'],
            86400 => $translation['day'],
            3600 => $translation['hour'],
            60 => $translation['minute'],
            1 => $translation['second']
        );
        foreach ($tokens as $unit => $text):
            if ($time < $unit):
                continue;
            endif;
            $numberOfUnits = floor($time / $unit);
            if (ngfw\Registry::get("requestedLanguage") == "en" and $numberOfUnits > 1):
                $text.="s";
            endif;
            return $numberOfUnits.' '.$text;
        endforeach;
    }
	
}