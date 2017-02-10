//倒數開啟按鈕
function count_to_open_btn() {
    var initMessage = "下載影片",
        numberCount = 0;

    for (var i = numberCount;  i >= 0 ;  i--) {
        setTimeout(function() {
            $("#btn_download").html(initMessage+'倒數 ( '+ numberCount-- +' )').attr('disabled', true);

            if (numberCount == -1) {
                $("#btn_download").html(initMessage).attr('disabled', false);
            }
        }, 1000*i);
    }
}

//確認欲處理的時間是否有正確配置
function checkTime()
{
    var bool = true;
    var start_time = $('#start_time').val();
    var end_time = $('#end_time').val();
    if( start_time >= end_time )
    {
        alert('開始時間不能大於等於結束時間喔!!');
        bool = false;
    }
    return bool;
}

//秒數格式化成 HH:MM:SS
function toHHMMSS(sec_num){
    var sec_num = parseFloat(sec_num, 10); // don't forget the second param
    var hours   = Math.floor(sec_num / 3600);
    var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
    var seconds = sec_num - (hours * 3600) - (minutes * 60);

    if (hours   < 10) {hours   = "0"+hours;}
    if (minutes < 10) {minutes = "0"+minutes;}
    if (seconds < 10) {seconds = "0"+seconds;}
    return hours+':'+minutes+':'+seconds;
}

//取得今天的日期，讓使用者送出訊息時參考用
function getTodayDate() {
    var str = '';

    // 宣告日期物件
    var today = new Date();

    // 年
    var today_year = today.getFullYear();
    str += today_year;

    // 月
    var today_month = today.getMonth() + 1;
    if (today_month >= 10)
        str += '-' + today_month;
    else
        str += '-0' + today_month;

    // 日
    var today_date = today.getDate();
    if (today_date >= 10)
        str += '-' + today_date;
    else
        str += '-0' + today_date;

    var today_hour = today.getHours();
    if (today_hour >= 10)
        str += ' ' + today_hour;
    else
        str += ' 0' + today_hour;

    var today_minute = today.getMinutes();
    if (today_minute >= 10)
        str += ':' + today_minute;
    else
        str += ':0' + today_minute;

    var today_second = today.getSeconds();
    if (today_second >= 10)
        str += ':' + today_second;
    else
        str += ':0' + today_second;
    return str;
}