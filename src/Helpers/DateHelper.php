<?php
// +----------------------------------------------------------------------
// | 通用类包
// +----------------------------------------------------------------------
// | Copyright (c) 2020 http://www.hmall.com.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: joyecZhang <zhangwei762@163.com>
// +----------------------------------------------------------------------

declare (strict_types=1);

namespace JoyceZ\LaravelLib\Helpers;

/**
 * 日期常用助手函数
 * Class DateHelper
 * @package JoyceZ\LaravelLib\Helpers
 */
class DateHelper
{
    /**
     * 获取今日开始时间戳和结束时间戳
     *
     * 语法：mktime(hour,minute,second,month,day,year) => (小时,分钟,秒,月份,天,年)
     */
    public static function today()
    {
        return [
            'start' => mktime(0, 0, 0, (int)date('m'), (int)date('d'), (int)date('Y')),
            'end' => mktime(0, 0, 0, (int)date('m'), (int)date('d') + 1, (int)date('Y')) - 1,
        ];
    }

    /**
     * 昨日
     *
     * @return array
     */
    public static function yesterday()
    {
        return [
            'start' => mktime(0, 0, 0, (int)date('m'), (int)date('d') - 1, (int)date('Y')),
            'end' => mktime(0, 0, 0, (int)date('m'), (int)date('d'), (int)date('Y')) - 1,
        ];
    }

    /**
     * 本周
     * @return array
     */
    public static function week()
    {
        $length = 0;
        // 星期天直接返回上星期，因为计算周围 星期一到星期天，如果不想直接去掉
        if (date('w') == 0) {
            $length = 7;
        }

        return [
            'start' => mktime(0, 0, 0, (int)date('m'), (int)date('d') - (int)date('w') + 1 - $length, (int)date('Y')),
            'end' => mktime(23, 59, 59, (int)date('m'), (int)date('d') - (int)date('w') + 7 - $length, (int)date('Y')),
        ];
    }

    /**
     * 上周
     *
     * @return array
     */
    public static function lastWeek()
    {
        $length = 7;
        // 星期天直接返回上星期，因为计算周围 星期一到星期天，如果不想直接去掉
        if (date('w') == 0) {
            $length = 14;
        }
        return [
            'start' => mktime(0, 0, 0, (int)date('m'), (int)date('d') - (int)date('w') + 1 - $length, (int)date('Y')),
            'end' => mktime(23, 59, 59, (int)date('m'), (int)date('d') - (int)date('w') + 7 - $length, (int)date('Y')),
        ];
    }

    /**
     * 本月
     *
     * @return array
     */
    public static function thisMonth()
    {
        return [
            'start' => mktime(0, 0, 0, (int)date('m'), 1, (int)date('Y')),
            'end' => mktime(23, 59, 59, (int)date('m'), (int)date('t'), (int)date('Y')),
        ];
    }

    /**
     * 上个月
     *
     * @return array
     */
    public static function lastMonth()
    {
        $start = mktime(0, 0, 0, (int)date('m') - 1, 1, (int)date('Y'));
        $end = mktime(23, 59, 59, (int)date('m') - 1, (int)date('t'), (int)date('Y'));

        if (date('m', $start) != date('m', $end)) {
            $end -= 60 * 60 * 24;
        }

        return [
            'start' => $start,
            'end' => $end,
        ];
    }

    /**
     * 几个月前
     *
     * @param integer $month 月份
     * @return array
     */
    public static function monthsAgo($month)
    {
        $start = mktime(0, 0, 0, (int)date('m') - $month, 1, (int)date('Y'));
        $end = mktime(23, 59, 59, (int)date('m') - $month, (int)date('t'), (int)date('Y'));
        if (date('m', $start) != date('m', $end)) {
            $end -= 60 * 60 * 24;
        }
        return [
            'start' => $start,
            'end' => $end,
        ];
    }

    /**
     * 某年
     * @param $year
     * @return array
     */
    public static function someYear($year)
    {
        $start_month = 1;
        $end_month = 12;

        $start_time = $year . '-' . $start_month . '-1 00:00:00';
        $end_month = $year . '-' . $end_month . '-1 23:59:59';
        $end_time = date('Y-m-t H:i:s', strtotime($end_month));

        return [
            'start' => strtotime($start_time),
            'end' => strtotime($end_time)
        ];
    }

    /**
     * 某月
     *
     * @param int $year
     * @param int $month
     * @return array
     */
    public static function aMonth($year = 0, $month = 0)
    {
        $year = $year == 0 ? (int)date('Y') : $year;
        $month = $month == 0 ? (int)date('m') : $month;
        $day = date('t', strtotime($year . '-' . $month));
        return [
            "start" => strtotime($year . '-' . $month),
            "end" => mktime(23, 59, 59, $month, (int)$day, (int)$year)
        ];
    }

    /**
     * 根据日期获取是星期几
     * @param int $time
     * @param string $format
     * @return mixed
     */
    public static function getWeekName(int $time, $format = "周")
    {
        $week = date('w', $time);
        $weekName = ['日', '一', '二', '三', '四', '五', '六'];
        foreach ($weekName as &$item) {
            $item = $format . $item;
        }
        return $weekName[$week];
    }

    /**
     * 获取指定开始日期到结束日期
     * @param int $start_day 开始天数，以当天开始：0，每增加一天，在当天基础上 +1
     * @param int $end_day
     * @return array
     */
    public static function getFutureHowManyDays(int $start_day = 0, int $end_day = 7)
    {
        $dateArr = [];
        for ($i = $start_day; $i < $end_day; $i++) {
            $dateArr[$i] = date('Y-m-d', strtotime(date('Y-m-d') . '+' . $i . 'day'));
        }
        return $dateArr;
    }

    /**
     * 根据开始/结束时间，并以分钟为分界点生成 时间范围
     * @param string $start_time
     * @param string $end_time
     * @param int $minute
     * @return array
     */
    public static function buildEveryDayTimeRange(string $start_time = '09:00', string $end_time = '22:30', int $minute = 30)
    {
        $arr = [];
        for ($i = strtotime($start_time); $i <= strtotime($end_time); $i = $i + 60 * $minute) {
            $arr[] = date("H:i", $i);
        }
        $result = [];
        $num = count($arr);
        foreach ($arr as $key => $val) {
            if ($key < $num) {
                $time_str = strtotime($val);
                $result[$key + 1] = $val . '-' . date('H:i', $time_str + 60 * $minute);
            }
        }
        return $result;
    }

    /**
     * 格式化时间戳
     *
     * @param $time
     * @return string
     */
    public static function formatTimestamp($time)
    {
        $min = $time / 60;
        $hours = $time / 3600;
        $days = floor($hours / 24);
        $hours = floor($hours - ($days * 24));
        $min = floor($min - ($days * 60 * 24) - ($hours * 60));

        return $days . " 天 " . $hours . " 小时 " . $min . " 分钟 ";
    }

    /**
     * 格式化时间戳
     * @param int $time 时间戳
     * @param string $format 格式化时间格式
     * @return false|string
     */
    public static function formatParseTime(int $time = 0, string $format = 'Y-m-d H:i:s')
    {
        return $time > 0 ? date($format, $time) : '';
    }

    /**
     * 生成时间戳
     * @param int $accuracy 精度 默认微妙
     * @return string
     */
    public static function buildTimestamp($accuracy = 1000)
    {
        list($msec, $sec) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * $accuracy);
    }

    /**
     * 当前时间多久之前，以天核算
     * @param int $curTime
     * @return false|string
     */
    public static function formatDateLongAgo(int $curTime)
    {
        $todayLast = strtotime(date('Y-m-d 23:59:59'));
        $agoTime = $todayLast - $curTime;
        $agoDay = intval(floor($agoTime / 86400));
        if ($agoDay === 0) {
            return '今天' . date('H:i', $curTime);
        } elseif ($agoDay === 1) {
            return '昨天 ' . date('H:i', $curTime);
        } elseif ($agoDay === 2) {
            return '前天 ' . date('H:i', $curTime);
        } elseif ($agoDay > 2 && $agoDay < 16) {
            return $agoDay . '天前 ' . date('H:i', $curTime);
        } else {
            $format = date('Y') != date('Y', $curTime) ? 'Y-m-d H:i' : 'm-d H:i';
            return date($format, $curTime);
        }
    }

    /**
     * 当前时间多久之前，以分钟到天核算
     * @param int $curTime
     * @return false|string
     */
    public static function formatTimeLongAgo(int $curTime)
    {
        $todayLast = strtotime(date('Y-m-d 23:59:59'));
        $agoTimestamp = time() - $curTime;
        $agoTime = $todayLast - $curTime;
        $agoDay = intval(floor($agoTime / 86400));

        if ($agoTimestamp < 60) {
            $result = '刚刚';
        } elseif ($agoTimestamp < 3600) {
            $result = (ceil($agoTimestamp / 60)) . '分钟前';
        } elseif ($agoTimestamp < 3600 * 12) {
            $result = (ceil($agoTimestamp / 3600)) . '小时前';
        } elseif ($agoDay === 0) {
            $result = '今天 ' . date('H:i', $curTime);
        } elseif ($agoDay === 1) {
            $result = '昨天 ' . date('H:i', $curTime);
        } elseif ($agoDay === 2) {
            $result = '前天 ' . date('H:i', $curTime);
        } elseif ($agoDay > 2 && $agoDay < 16) {
            $result = $agoDay . '天前 ' . date('H:i', $curTime);
        } else {
            $format = date('Y') != date('Y', $curTime) ? 'Y-m-d H:i' : 'm-d H:i';
            return date($format, $curTime);
        }
        return $result;
    }

    /**
     * 每周重复、隔周重复
     * 根据开始日期至结束日期以及指定周几获得对应重复日期
     * @param string $startDate 开始日期 Y-m-d
     * @param string $endDate 结束日期 Y-m-d
     * @param array $week 选中数字周几 [1,2,3,4,5,6,7] 或中文周几 ['周天', '周一', '周二', '周三', '周四', '周五', '周六']
     * @param bool $isApartWeek 是否隔周排期 true | false
     * @param bool $isNumWeek 是否是数字周几 true | false
     * @return array
     */
    public static function generateDateWeek(string $startDate, string $endDate, array $week = [], $isApartWeek = false, $isNumWeek = true)
    {
        $start_date = strtotime($startDate);
        $end_date = strtotime($endDate);
        $days = ($end_date - $start_date) / 86400;
        $weekArr = $isNumWeek ? ['7', '1', '2', '3', '4', '5', '6'] : ['周天', '周一', '周二', '周三', '周四', '周五', '周六'];
        // 组建数组格式 $dataWeek['日期'] => 星期
        $dateWeek = [];
        for ($i = 0; $i < $days; $i++) {
            $num_week = date('w', $start_date + ($i * 86400));
            $dateWeek[date('Y-m-d', $start_date + ($i * 86400))] = $weekArr[$num_week];
        }
        if ($isApartWeek) {
            //以周日为节点，将每周日期规整在一起
            $index = 0;
            $separateDateWeek = [];
            foreach ($dateWeek as $key => $item) {
                $separateDateWeek[$index][] = [$key => $item];
                if ((string)$item == (string)$weekArr[0]) {
                    $index++;
                }
            }
            //对以每周日期规整一起数组取偶，提出隔周日期数据
            $evenDateWeek = [];
            foreach ($separateDateWeek as $key => $item) {
                if (!($key & 1)) {
                    $evenDateWeek = array_merge($evenDateWeek, $item);
                }
            }
            //二维数组合并成一维数组
            $dateWeek = array_reduce($evenDateWeek, 'array_merge', []);
        }
        // 查找两个数组的交集，即获取提交的星期对应的日期
        $newDate = array_intersect($dateWeek, $week);
        // 获取数组中的键值(日期)，并组成一个新数组
        return array_keys($newDate);
    }
}