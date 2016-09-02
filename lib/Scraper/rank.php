<?php
// header('Content-Type: text/html; charset=utf-8');

$host = "localhost";   // 接続するMySQLサーバー
$user = "root";      // MySQLのユーザー名
$pass = "mn9621mnZ+";      // MySQLのパスワード
$dbname= "icalendar";      // DBの名前

// データベースに接続
if(!$db_conn = mysql_connect($host, $user, $pass)){
print("データベースにアクセスできません。");
exit;
}
$rtn = mysql_query("SET NAMES utf8" , $db_conn);
mysql_select_db($dbname);


//リセット
$sql = "UPDATE `tbl_calendar` SET `order` = 999999 WHERE `order` < 999999";
$result = mysql_query($sql, $db_conn);


function f_rank_update($db_conn,$rank, $calendar_id)
{
    $sql = "UPDATE `tbl_calendar` SET `order` = '$rank' WHERE id = '$calendar_id'" ;
    $result = mysql_query($sql, $db_conn);
    return $id;
}

/////////////////
// カレンダーランク
$strSQL = "SELECT  count(*) as rank,`calid`  FROM `tbl_logs` WHERE `exm` = 'calendar' group by `calid` order by rank desc";
$tbl_tmp = mysql_query($strSQL, $db_conn);
if($tbl_tmp)
{
    $rank = 1;
    while($link = mysql_fetch_row($tbl_tmp))
    {
        list($cnt,$calendar_id) = $link;

        $rtn = f_rank_update($db_conn,$rank, $calendar_id);
        echo "<br>".$rank."位:".$calendar_id;
        $rank++;
        
    }
}

?>
