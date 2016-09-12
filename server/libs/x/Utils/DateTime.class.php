<?php
/**
 * Created by PhpStorm.
 * User: xBei
 * Date: 2016/9/5
 * Time: 18:07
 */

namespace x\Utils;


use DateTimeZone;

class DateTime
{
    /**
     * 年，表示年月日中的＂年＂
     */
    const DATA_PART_YEAR = 1;
    /**
     * 月，表示年月日中的＂月＂
     */
    const DATA_PART_MONTH = 2;
    /**
     * 天，表示年月日中的＂日＂
     */
    const DATA_PART_DAY = 3;
    /**
     * 时，表示时分秒中的＂时＂
     */
    const DATA_PART_HOUR = 4;
    /**
     * 分，表示时分秒中的＂分＂
     */
    const DATA_PART_MINUTE = 5;
    /**
     * 秒，表示时分秒中的＂秒＂
     */
    const DATA_PART_SECOND = 6;

    private $_datetime;

    /**
     * DateTime constructor.
     * @param string|int|null $time
     * @param DateTimeZone|null $timezone
     */
    function __construct($time = 'now', DateTimeZone $timezone = null)
    {
        if(is_string($time)) {
	        $this->_datetime = new \DateTime($time, $timezone);
        }elseif($time instanceof DateTime){
        	$this->_datetime = new \DateTime();
	        $this->_datetime->setTimestamp($time->_datetime->getTimestamp());
        }else {
            $this->_datetime = new \DateTime();
            if(is_numeric($time)) {
                $this->_datetime->setTimestamp($time);
            }
        }
    }

    //region 日期的加减计算
    public function addYears($years, $newDate = false)
    {
        $date = $newDate
            ? $this->copy()
            : $this;
        if($years == 0)
            return $date;
        $date->setYear($date->getYear() + $years);
        return $this;
    }
    public function addMonths($month, $newDate = false)
    {
        $date = $newDate
            ? $this->copy()
            : $this;
        if ($month == 0)
            return $date;
        return $date->setMonth($date->getMonth()+$month);
    }
    public function addDays($days, $newDate = false)
    {
        $date = $newDate
            ? $this->copy()
            : $this;
        if ($days == 0)
            return $date;
        return $date->setDate($date->getDate()+$days);
    }
    public function addHours($hours, $newDate = false){
        $date = $newDate
            ? $this->copy()
            : $this;
        if($hours == 0)
            return $date;
        $date->setHours($date->getHour() + $hours);
        return $this;
    }
    public function addMinutes($minutes, $newDate = false){
        $date = $newDate
            ? $this->copy()
            : $this;
        if($minutes == 0)
            return $date;
        $date->setMinutes($date->getMinute() + $minutes);
        return $this;
    }
    public function addSeconds($seconds, $newDate = false){
        $date = $newDate
            ? $this->copy()
            : $this;
        if($seconds == 0)
            return $date;
        $date->setSeconds($date->getSecond() + $seconds);
        return $this;
    }
    //endregion

    //region 获取日期、时间
    public function getYear()
    {
        return intval($this->_datetime->format('Y'));
    }

    public function getMonth()
    {
        return intval($this->_datetime->format('m'));
    }

    public function getDate()
    {
        return intval($this->_datetime->format('d'));
    }

    /**
     * 返回星期，0-星期天
     * @return int
     */
    public function getWeek()
    {
        return intval($this->_datetime->format('w'));
    }

    public function getHour()
    {
        return intval($this->_datetime->format('H'));
    }

    public function getMinute()
    {
        return intval($this->_datetime->format('i'));
    }

    public function getSecond()
    {
        return intval($this->_datetime->format('s'));
    }

    public function getMicrosecond()
    {
        return intval($this->_datetime->format('u'));
    }
    //endregion

    //region 设置日期
    public function setYear($year)
    {
        $this->_datetime->setDate($year, $this->getMonth(), $this->getDate());
        return $this;
    }

    public function setMonth($month)
    {
        $this->_datetime->setDate($this->getYear(), $month, $this->getDate());
        return $this;
    }

    public function setDate($date)
    {
        $this->_datetime->setDate($this->getYear(), $this->getMonth(), $date);
        return $this;
    }

    public function setHours($hours)
    {
        $this->_datetime->setTime($hours, $this->getMinute(), $this->getSecond());
        return $this;
    }

    public function setMinutes($minutes)
    {
        $this->_datetime->setTime($this->getHour(), $minutes, $this->getSecond());
        return $this;
    }

    public function setSeconds($seconds)
    {
        $this->_datetime->setTime($this->getHour(), $this->getMinute(), $seconds);
        return $this;
    }

    public function setTime($hour = 0, $minute = 0, $second = 0)
    {
        $this->_datetime->setTime($hour, $minute, $second);
        return $this;
    }

    //endregion

    public function format($format){
        return $this->_datetime->format($format);
    }

    public static function Now()
    {
        return new DateTime("now");
    }

    public static function Today()
    {
        return (new DateTime("now"))
            ->setTime(0, 0, 0);
    }
    public static function each(DateTime $begin, DateTime $end, $mode, $callBack){
        $tmp = $begin -> copy();
        $result = array();
        while ($tmp < $end){
            //list($key, $value) = $callBack($tmp);
            //$result[$key] = $value;
            $result[] = $callBack($tmp);
            switch ($mode){
                case self::DATA_PART_YEAR://year
                    $tmp->addYears(1);
                    break;
                case self::DATA_PART_MONTH://month
                    $tmp->addMonths(1);
                    break;
                case self::DATA_PART_DAY://day
                    $tmp->addDays(1);
                    break;
                case self::DATA_PART_HOUR://hour
                    $tmp->addHours(1);
                    break;
                case self::DATA_PART_MINUTE://minute
                    $tmp->addMinutes(1);
                    break;
                case self::DATA_PART_SECOND://second
                    $tmp->addSeconds(1);
                    break;
            }
        }
        return $result;
    }
    function copy(){
        return new DateTime($this->_datetime->getTimestamp());
    }
    function __toString()
    {
        return $this->_datetime->format('Y-m-d H:i:s');
    }
}