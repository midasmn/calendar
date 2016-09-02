<?php
$host = "mysql1005.xserver.jp";   // 接続するMySQLサーバー
$user = "midasmn_admin";      // MySQLのユーザー名
$pass = "nBxYzMxX47u";      // MySQLのパスワード
$dbname= "midasmn_calendar";      // DBの名前
// データベースに接続
$db_conn = new mysqli($host, $user, $pass, $dbname)
or die("データベースとの接続に失敗しました");
$db_conn->set_charset('utf8');
// $result = mysqli_query($db_conn,$sql);
// $cnt = 16;


// //リセット
// $sql = "UPDATE `tbl_calendar` SET `order` = 999999 WHERE `order` < 999999";
// $result = mysql_query($sql, $db_conn);


// function f_rank_update($db_conn,$rank, $calendar_id)
// {
//     $sql = "UPDATE `tbl_calendar` SET `order` = '$rank' WHERE id = '$calendar_id'" ;
//     $result = mysql_query($sql, $db_conn);
//     return $id;
// }

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
