<?php
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

//インサート
function f_insert_tag($db_conn,$calendar_id,$tag)
{
   mb_language('Japanese');//←これ
   $img_alt=mb_convert_encoding($img_alt,'UTF-8','auto');

   $sql = "INSERT INTO `tbl_tag`(`id`, `calendarid`, `tag`, `createdate`) VALUES (NULL,'$calendar_id',CURRENT_TIMESTAMP)";
    $result = mysql_query($sql, $db_conn);
    if(!$result)
    {
        $rtn =  "NG";
    }else{
        $rtn = "OK";
    }
    return $rtn;
}



////////////////////////////////////////
$sql = 'SELECT `id`, `tags` FROM `tbl_calendar` WHERE `onflg` = "ON";';
$result = mysql_query($sql,$db_conn);
if($result)
{
    while($link = mysql_fetch_row($result))
    {
        list($calendar_id,$tags) = $link;
        $exm_url = urlencode($tags);

        f_insert_tag($db_conn,$calendar_id,$tag);
    }
}
echo "end";
?>