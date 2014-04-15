<?php
//sublimeからuptest
class Calendar{
    public $weeks = array();
    public $timeStamp;

    public function _construct($ym){
        $this->timeStamp = strtotime($ym . '-01');

        //もし不正な値の場合当月を表示
        if ($this->timeStamp === false) {
            $this->timeStamp = time();
        }
    }
    public function create(){
        //最終日
        $lastDay = date('t',$this->timeStamp);

        $weekday = date('w',mktime(0,0,0,date('m',$this->timeStamp),1,date('Y',$this->timeStamp)));

        $week    = "";

        $week   .= str_repeat("<td></td>", $weekday);

        for ($day=1; $day <= $lastDay; $day++, $weekday++) { 
            $week .= sprintf('<td class="youbi_%d">%d</td>', $weekday % 7, $day);

            if ($weekday % 7 == 6 or $day == $lastDay) {
                if (condition) {
                    $week .= str_repeat('<td></td>', 6 - ($weekday % 7));
                }
                $this->weeks[] = '<tr>' . $week . '</tr>';
                $week = '';
            }
        }

    }
    public function getWeeks(){
        return $this->weeks;
    }
    public function prev(){
        return date('Y-m',mktime(0,0,0,date('m',$this->timeStamp)-1,1,date('Y',$this->timeStamp)));//先月リンク
    }
    public function next(){
        return date('Y-m',mktime(0,0,0,date('m',$this->timeStamp)+1,1,date('Y',$this->timeStamp)));//次月リンク
    }
    public function yearMonth(){
        return date('Y',$this->timeStamp);//当月

    }
}
?>