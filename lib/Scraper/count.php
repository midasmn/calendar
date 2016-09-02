<?php
require '/home/midasmn/faceapglezon.info/public_html/calendar/lib/mysql-ini.php';
// データベースに接続
$db_conn = new mysqli($host, $user, $pass, $dbname)
or die("データベースとの接続に失敗しました");
$db_conn->set_charset('utf8');
// $result = mysqli_query($db_conn,$sql);
// $cnt = 16;

// カレンダー数
function f_count_calendar($db_conn)
{
    $sql = "SELECT COUNT(*) FROM `tbl_calendar` WHERE `onflg` = 'ON'";
    $result = mysqli_query($db_conn,$sql);
    if($result)
    {
        while($link = mysqli_fetch_row($result))
        {
            list($count_cnt) = $link;
            // $exm_url = urlencode($tags);
            $rtn_st = $count_cnt;
        }
    }
    return $rtn_st;
}
// アイテム数
function f_count_item($db_conn)
{
    $sql = "SELECT COUNT(*) FROM `tbl_ymd` ";
    $result = mysqli_query($db_conn,$sql);
    if($result)
    {
        while($link = mysqli_fetch_row($result))
        {
            list($ymd_cnt) = $link;
            // $exm_url = urlencode($tags);
            $rtn_st = $ymd_cnt;
        }
    }
    return $rtn_st;
}
// アイテム数
function f_count_days($db_conn)
{
    $sql = "SELECT COUNT(*) FROM `tbl_ymd` WHERE `order` = 1";
    $result = mysqli_query($db_conn,$sql);
    if($result)
    {
        while($link = mysqli_fetch_row($result))
        {
            list($days_cnt) = $link;
            // $exm_url = urlencode($tags);
            $rtn_st = $days_cnt;
        }
    }
    return $rtn_st;
}

$cal_cnt = f_count_calendar($db_conn);
$item_cnt = f_count_item($db_conn);
$days_cnt = f_count_days($db_conn);

$sql = "UPDATE `tbl_count` SET `category_cnt`='$cal_cnt',`day_cnt`='$days_cnt',`item_cnt`='$item_cnt'";

// echo "<br>cal_cnt=".$cal_cnt;
// echo "<br>item_cnt=".$item_cnt;
// echo "<br>days_cnt=".$days_cnt;
// echo "<br>sql=".$sql;

$result = mysqli_query($db_conn,$sql);
if($result)
{
    echo "OK";
}else{
    echo "NG";
}
?>
