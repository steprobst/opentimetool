<?php
/**
 * 
 * $Id: util.php 123 2019-09-03 15:03:36Z munix9 $
 * 
 */

require_once 'Date/Calc.php';

/**
 * 
 * 
 * @package    ott
 * @version    2002/03/28
 * @access     public
 * @author     Wolfram Kriesing <wolfram@kriesing.de>
 */
class util
{
    function translateAndPrint($string)
    {
        global $translator, $lang;

        // only translate exact matches
        $translated = $translator->simpleTranslate($string, $lang);
        //$translated = $this->convToISO($translated); // SX
        echo $translated;
    }

    function translate($string)
    {
        global $translator, $lang;

        // if the simple-translation didnt succeed we need to use the regExp-translation
        // and translate handles all that itself, so no extra code around it needed
        $ret = $translator->translate($string, $lang);
        //$ret = $this->convToISO($ret); // SX

        return $ret;
    }

    /**
     * translate the DAY, MONTH, YEAR values into a timestamp
     * or if the string contains a ':' then we assume it's a time, like 10:00
     * 
     * @version    2002/03/11
     * @access     public
     * @author     Wolfram Kriesing <wolfram@kriesing.de>
     * @param      array   the values returned by the select boxes
     *                     this is passed by reference so we can change the value directly in this method
     * @return     mixed   true on success, or false if the date is not valid
     */
    function makeTimestamp($dateOrTime)
    {
/* actually we dont have any of those fields
        if (is_array($date)) {
            if (!Date_Calc::isValidDate($date['day'], $date['month'], $date['year'])) {
                return false;
            }
            $date = mktime (0, 0, 0, $date['month'], $date['day'], $date['year']);
            return $date;
        } else
*/
        $ret = $dateOrTime;

        $date = explode('.', $dateOrTime);
        if (is_array($date) && sizeof($date) > 1) {
            if (!Date_Calc::isValidDate($date[0], $date[1], $date[2])) {
                return false;
            }
            $ret = mktime(0, 0, 0, $date[1], $date[0], $date[2]);
        }

        $time = explode(':', $dateOrTime);
        if (is_array($time) && sizeof($time) == 2) {
            $ret = mktime($time[0], $time[1], 1, 1, 1970, 0);
        }
        return $ret;
    }

    function formatPrice($price)
    {
        return sprintf('%.2f', $price);
    }

    // SX: conv to iso to have tstrings correctly translated to iso
    function convToISO($str)
    {
        $curenc = mb_detect_encoding($str, "UTF-8, ISO-8859-1, WINDOWS-1252 , GBK", true);
        if ($curenc != "ISO-8859-1") {
            return iconv($curenc, "ISO-8859-1", $str);
        }
        return $str;
    }

    function recRemDir($dir, $skip = '')
    {
        static $ret = array();
        foreach (glob("{$dir}/*") as $file) {
            if (is_dir($file)) {
                $this->recRemDir($file, $skip);
            } else {
                $ret[] = str_replace($_SERVER['DOCUMENT_ROOT'], '', $file)
                       . ' ... ' . (@unlink($file) ? 'ok' : 'error');
            }
        }
        if (realpath($dir) === realpath($skip)) {
            $res = 'skip';
        } else {
            $res = @rmdir($dir) ? 'ok' : 'error';
        }
        $ret[] = str_replace($_SERVER['DOCUMENT_ROOT'], '', $dir) . ' ... ' . $res;
        return $ret;
    }

    function isMobile()
    {
        global $session;

        if (!isset($session->temp->isMobile)) {
            require_once 'Mobile-Detect/Mobile_Detect.php';
            $detect = new Mobile_Detect;
            @$session->temp->isMobile = ($detect->isMobile() && !$detect->isTablet());
        }
        return $session->temp->isMobile;
    }

    function isDesktop($set = null)
    {
        global $session;

        if ($set === null) {
            if (!isset($session->temp->isDesktop)) {
                @$session->temp->isDesktop = !$this->isMobile();
            }
            $ret = $session->temp->isDesktop;
        } else {
            if ($set === true) {
                $ret = true;
            } else if ($set === false && $this->isMobile()) {
                $ret = false;
            } else {
                $ret = !$this->isMobile();
            }
            @$session->temp->isDesktop = $ret;
        }
        return $ret;
    }

    function generatePassword($length = 8)
    {
	$sets = array(
            'abcdefghijklmnopqrstuvwxyz',
            'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
            '1234567890',
            '!@#$%&*?_/',
        );
	$all = '';
	$password = '';

	foreach ($sets as $set) {
            $password .= $set[array_rand(str_split($set))];
            $all .= $set;
	}
	$all = str_split($all);
	for ($i = 0; $i < $length - count($sets); $i++) {
            $password .= $all[array_rand($all)];
        }
	$password = str_shuffle($password);

        return $password;
    }

    function sendMail($to, $subject, $message)
    {
        global $config;

        $headers = !empty($config->mailAdditionalHeaders)
                 ? $config->mailAdditionalHeaders : '';
        $parameters = !empty($config->mailAdditionalParameters)
                    ? $config->mailAdditionalParameters : '';
        return mail($to, $subject, $message, $headers, $parameters);
    }

} // end of class

$util = new util;
