//ajaxを使用して再読み込みをして表示させる
function view_calendar(){
    // $('boby').append(data);
    $('body').children().remove();
    $.ajax({
        url: 'calendar.php',
        type:'post'
    }).then(function(data,status){
        // 成功時
        console.log(status);
        // console.log(data);
        $('body').append(data);
    },function(data,status){
        // 失敗時
        console.log(status);
    });
}

//先月次月遷移処理
function move_calendar(year,month){
    // console.log(year);
    // console.log(month);
    // $('boby').append(data);
    $('body').children().remove();
    $.ajax({
        url: 'calendar.php',
        data:{
            year : year,
            month: month
        },
        type:'GET'
    }).then(function(data,status){
        // 成功時
        console.log(status);
        //console.log(data);
        $('body').append(data);
    },function(data,status){
        // 失敗時
        console.log(status);
    });
}

//先月次月移動
$(function(){
    view_calendar();
    //先月次月ボタン遷移処理
    $(document).on('click','.move_month',function(e){
        //hrefの値を取得
        var href = $(this).attr('href');
        // 文字列分割でGETの値を取得
        var hrefArray = href.split('?');
        hrefArray = hrefArray[1].split('&');
        // 文字列分割で
        // 年、月、日
        // を取得
        var year  = hrefArray[0].split('=');
        var month = hrefArray[1].split('=');
        move_calendar(year[1],month[1]);
        // aタグで遷移しないようにする
        return false;
    });
});

//セレクトボックス移動
$(document).on('click','#view_output',function(e){
    var move_year  = $('.move_ym').eq(0).val();
    var move_month = $('.move_ym').eq(1).val();
    // console.log(move_year+move_month);
    move_calendar(move_year,move_month);
});

// スケジュール編集モーダル
$(document).on('click','.calendar_days',function(e){
    //hrefの値を取得
    var href = $(this).attr('href');
    // 文字列分割でGETの値を取得
    var hrefArray = href.split('?');
    hrefArray = hrefArray[1].split('&');
    // 文字列分割で
    // 年、月、日
    // を取得
    var year  = hrefArray[0].split('=');
    var month = hrefArray[1].split('=');
    var days  = hrefArray[2].split('=');
    var id    = [null,null];
    if(hrefArray.length == 4){
        id = hrefArray[3].split('=');
    }
    // Ajaxで送る日付
    $.ajax({
        url: 'schedule.php',
        data:{
            year : year[1],
            month: month[1],
            day  : days[1],
            id   : id[1]
        },
        type:'post'
    }).then(function(data,status){
        // 成功時
        // console.log(status);
        // console.log(data);
        //モーダルの中身
        var modal_html = '<div id="modal_wrap">' + '<div id="modal_close_button">X</div>' + data + '</div>';
        // main_boxの下にモーダル生成
        $('.main_box').after(modal_html);
        $('.main_box').after('<div id="modal_bk"></div>');
    },function(data,status){
        // 失敗時
        console.log(status);
    });
    // aタグで遷移しないようにする
    return false;
});

// 背景の黒いところをクリックしてモーダルを消す処理
$(document).on('click','#modal_bk',function(e){
    $('#modal_wrap').remove();
    $('#modal_bk').remove();
});
// モーダルの×ボタンをクリックしてモーダルを消す処理
$(document).on('click','#modal_close_button',function(e){
    $('#modal_wrap').remove();
    $('#modal_bk').remove();
});


//スケジュール登録処理
$(document).on('click','#regist',function(e){
    //開始時間
    var start_time_year  = $('.submit_time').eq(0).val();
    var start_time_month = $('.submit_time').eq(1).val();
    var start_time_day   = $('.submit_time').eq(2).val();
    var start_time_hour  = $('.submit_time').eq(3).val();
    var start_time_min   = $('.submit_time').eq(4).val();
    //終了時間
    var end_time_year  = $('.submit_time').eq(5).val();
    var end_time_month = $('.submit_time').eq(6).val();
    var end_time_day   = $('.submit_time').eq(7).val();
    var end_time_hour  = $('.submit_time').eq(8).val();
    var end_time_min   = $('.submit_time').eq(9).val();
    //タイトルと内容
    var title    = $('.submit_text').eq(0).val();
    var contents = $('.submit_text').eq(1).val();
    // console.log(title+contents);
    //ajaxで内容を格納
    $.ajax({
        url: 'registration.php',
        data:{
        //データ代入
        start_year : start_time_year,
        start_month: start_time_month,
        start_day  : start_time_day,
        start_hour : start_time_hour,
        start_min  : start_time_min,

        end_year : end_time_year,
        end_month: end_time_month,
        end_day  : end_time_day,
        end_hour : end_time_hour,
        end_min  : end_time_min,

        schedule_title : title,
        schedule_contents : contents
    },
    type:'post'
    }).then(function(data,status){
        // 成功時
        console.log(status);
        console.log(data);
        if(status === 'success'){
            view_calendar();
        } else {
            $('#error_msg').append(data);
        }
    },function(data,status){
        // 失敗時
        console.log(status);
    });
});

//スケジュール削除処理
$(document).on('click','#delete',function(e){
    //開始時間
    var start_time_year  = $('.submit_time').eq(0).val();
    var start_time_month = $('.submit_time').eq(1).val();
    var start_time_day   = $('.submit_time').eq(2).val();
    var start_time_hour  = $('.submit_time').eq(3).val();
    var start_time_min   = $('.submit_time').eq(4).val();
    //終了時間
    var end_time_year  = $('.submit_time').eq(5).val();
    var end_time_month = $('.submit_time').eq(6).val();
    var end_time_day   = $('.submit_time').eq(7).val();
    var end_time_hour  = $('.submit_time').eq(8).val();
    var end_time_min   = $('.submit_time').eq(9).val();
    //タイトル・内容・ID
    var title    = $('.submit_text').eq(0).val();
    var contents = $('.submit_text').eq(1).val();
    var id = $('#schedule_num').val();
    // console.log(title+contents);
    $.ajax({
        url: 'registration.php',
        data:{
            //データ代入
            start_year : start_time_year,
            start_month: start_time_month,
            start_day  : start_time_day,
            start_hour : start_time_hour,
            start_min  : start_time_min,

            end_year : end_time_year,
            end_month: end_time_month,
            end_day  : end_time_day,
            end_hour : end_time_hour,
            end_min  : end_time_min,

            schedule_title : title,
            schedule_contents : contents,
            schedule_id : id,
            schedule_delete : 'delete'
        },
        type:'post'
    }).then(function(data,status){
        // 成功時
        // console.log(status);
        // console.log(data);
        if(status === 'success'){
            view_calendar();
        }
    },function(data,status){
        // 失敗時
        console.log(status);
    });
    //alert('削除');
});

//スケジュール更新処理
$(document).on('click','#update',function(e){
    //開始時間
    var start_time_year  = $('.submit_time').eq(0).val();
    var start_time_month = $('.submit_time').eq(1).val();
    var start_time_day   = $('.submit_time').eq(2).val();
    var start_time_hour  = $('.submit_time').eq(3).val();
    var start_time_min   = $('.submit_time').eq(4).val();
    //終了時間
    var end_time_year  = $('.submit_time').eq(5).val();
    var end_time_month = $('.submit_time').eq(6).val();
    var end_time_day   = $('.submit_time').eq(7).val();
    var end_time_hour  = $('.submit_time').eq(8).val();
    var end_time_min   = $('.submit_time').eq(9).val();
    //タイトルと内容
    var title    = $('.submit_text').eq(0).val();
    var contents = $('.submit_text').eq(1).val();
    var id = $('#schedule_num').val();
    //console.log(id);
    $.ajax({
        url: 'registration.php',
        data:{
            //データを代入
            start_year : start_time_year,
            start_month: start_time_month,
            start_day  : start_time_day,
            start_hour : start_time_hour,
            start_min  : start_time_min,

            end_year : end_time_year,
            end_month: end_time_month,
            end_day  : end_time_day,
            end_hour : end_time_hour,
            end_min  : end_time_min,

            schedule_title : title,
            schedule_contents : contents,
            schedule_id : id
        },
        type:'post'
    }).then(function(data,status){
        // 成功時
        console.log(status);
        if(status === 'success'){
            view_calendar();
        } else {
            $('#error_msg').append(data);
        }
    },function(data,status){
        // 失敗時
        console.log(status);
    });
    //alert('アップデート');
});
