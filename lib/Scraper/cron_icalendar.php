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


$sql = "UPDATE `tbl_amazon` SET `cronflg` = 'OFF' ";
$result = mysqli_query($db_conn,$sql);
//
$sqlR = "UPDATE `tbl_rakuten` SET `cronflg` = 'OFF' ";
$result = mysqli_query($db_conn,$sqlR);

echo "end";
?>