<?php
//出力エスケープ
function h($str){
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

//セレクトボックスループ処理
function optionLoop($start, $end, $value = null){
    for($i = $start; $i <= $end; $i++){
        if(isset($value) &&  $value == $i){
            echo '<option value="'.$i.'" selected="selected">'.$i.'</option>';
        }else{
            echo '<option value="'.$i.'">'.$i.'</option>';
        }
    }
    return $i;
}
?>