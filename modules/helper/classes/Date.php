<?php
defined('SYSPATH') or die('No direct script access.');

class Date {

    public static function get_date_style($time) {
        $t = time() - $time;
        $f = array(
            '31536000' => '年',
            '2592000' => '个月',
            '604800' => '星期',
            '86400' => '天',
            '3600' => '小时',
            '60' => '分钟',
            '1' => '秒' 
        );
        foreach($f as $k => $v) {
            if (0 != $c = floor($t / (int)$k)) {
                return $c . $v . '前';
            }
        }
    }

    /**
	 * 判断干支、生肖和星座
	 *
	 * @param string $type 返回类型: XZ星座|GZ干支|SX生肖
	 * @param date   $birth 年月日(yyyy-mm-dd)
	 */
    function birthext($type, $birth) {
        $tmpstr = explode('-', $birth);
        $y = (int)$tmpstr[0];
        $m = (int)$tmpstr[1];
        $d = (int)$tmpstr[2];
        $result = '';
        switch($type) {
            case 'XZ': //星座
                $XZDict = array('摩羯', '水瓶', '双鱼', '白羊', '金牛', '双子', '巨蟹', '狮子', '处女', '天秤', '天蝎', '射手');
                $Zone = array(1222, 122, 222, 321, 421, 522, 622, 722, 822, 922, 1022, 1122, 1222);
                if ((100 * $m + $d) >= $Zone[0] || (100 * $m + $d) < $Zone[1]) {
                    $i = 0;
                } else {
                    for($i = 1; $i < 12; $i++) {
                        if ((100 * $m + $d) >= $Zone[$i] && (100 * $m + $d) < $Zone[$i + 1]) break;
                    }
                }
                $result = $XZDict[$i] . '座';
                break;
            case 'GZ': //干支
                $GZDict = array(
                    array('甲', '乙', '丙', '丁', '戊', '己', '庚', '辛', '壬', '癸'),
                    array('子', '丑', '寅', '卯', '辰', '巳', '午', '未', '申', '酉', '戌', '亥') 
                );
                $i = $y - 1900 + 36;
                $result = $GZDict[0][($i % 10)] . $GZDict[1][($i % 12)];
                break;
            case 'SX': //生肖
                $SXDict = array('鼠', '牛', '虎', '兔', '龙', '蛇', '马', '羊', '猴', '鸡', '狗', '猪');
                $result = $SXDict[(($y - 4) % 12)];
                break;
        }
        return $result;
    }

    public static function days($month, $year = FALSE) {
        if ($year === FALSE) {
            $year = date('Y');
        }
        return cal_days_in_month (CAL_GREGORIAN, $month, $year);
    }

    public static function daysof($starttime, $endtime) {
        $ret = array();
        while($starttime < $endtime) {
            $ret[] = date('Y-m-d', $starttime);
            $starttime = strtotime("+1 day", $starttime);
        }
        return $ret;
    }

    public static function unix2dos($timestamp = FALSE) {
        $timestamp = ($timestamp === FALSE) ? getdate() : getdate($timestamp);
        
        if ($timestamp['year'] < 1980) {
            return (1 << 21 | 1 << 16);
        }
        
        $timestamp['year'] -= 1980;
        
        // What voodoo is this? I have no idea... Geert can explain it though,
        // and that's good enough for me.
        return ($timestamp['year'] << 25 | $timestamp['mon'] << 21 | $timestamp['mday'] << 16 | $timestamp['hours'] << 11 | $timestamp['minutes'] << 5 | $timestamp['seconds'] >> 1);
    }

    public static function dos2unix($timestamp = FALSE) {
        $sec = 2 * ($timestamp & 0x1f);
        $min = ($timestamp >> 5) & 0x3f;
        $hrs = ($timestamp >> 11) & 0x1f;
        $day = ($timestamp >> 16) & 0x1f;
        $mon = ($timestamp >> 21) & 0x0f;
        $year = ($timestamp >> 25) & 0x7f;
        
        return mktime($hrs, $min, $sec, $mon, $day, $year + 1980);
    }

    public static function formatted_time($datetime_str = 'now', $timestamp_format = 'Y-m-d H:i:s', $timezone = NULL) {
        $tz = new DateTimeZone($timezone ? $timezone : date_default_timezone_get());
        $time = new DateTime($datetime_str, $tz);
        
        if ($time->getTimeZone()->getName() !== $tz->getName()) {
            $time->setTimeZone($tz);
        }
        
        return $time->format($timestamp_format);
    }
}
