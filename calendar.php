<?php

class Calendar{
    protected $weeks = array();
    protected $timeStamp;

    $this->$timeStamp = strtotime($ym . '-01');

    //もし不正な値の場合当月を表示
    if ($this->$timeStamp === false) {
        $this->$timeStamp = time();
    }

    public function _construct($ym){

    }
    public function getweeks(){

    }
    public function prev(){

    }
    public function next(){

    }
    public function yearMonth(){

    }
}

    //先月リンク
    $prev = date('Y-m',strtotime('-1,month'));
    //次月リンク
    $next = date('Y-m',strtotime('+1,month'));

    //最終日
    $lastDay = date('t',$timeStamp);

    $weekday = date('w',mktime(0,0,0,date('m',$timeStamp),1,date('Y',$timeStamp)));

    $weeks = array();
    $week  = "";

    $week .= str_repeat("<td></td>", $weekday);

    for ($day=1; $day <= $lastDay; $day++, $weekday++) { 
        $week .= sprintf('<td class="youbi_%d">%d</td>', $weekday % 7, $day);

        if ($weekday % 7 == 6 or $day == $lastDay) {
            if (condition) {
                $week .= str_repeat('<td></td>', 6 - ($weekday % 7));
            }
            $weeks[] = '<tr>' . $week . '</tr>';
            $week = '';
        }
    }
?>