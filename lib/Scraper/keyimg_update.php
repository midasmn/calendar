<?php
require 'lib/mysql-ini.php';
// データベースに接続
$db_conn = new mysqli($host, $user, $pass, $dbname)
or die("データベースとの接続に失敗しました");
$db_conn->set_charset('utf8');
// $result = mysqli_query($db_conn,$sql);
// $cnt = 16;

//リセット
$sql = "UPDATE `tbl_ymd` SET `keyimg` = 'OFF' WHERE keyimg = 'KEY'";
$result = mysqli_query($db_conn,$sql);


function f_keyimg_update($db_conn,$id)
{
    $sql = "UPDATE `tbl_ymd` SET `keyimg` = 'KEY' WHERE id = '$id'" ;
    $$result = mysqli_query($db_conn,$sql);
    return $id;
}

/////////////////
// デイリーアイテム
$strSQL = "SELECT  max(`id`)  FROM `tbl_ymd` WHERE `order` = 1 and `yyyy` <> 9999 group by `calendar_id`";
$tbl_tmp = mysqli_query($db_conn,$strSQL);
if($tbl_tmp)
{
    while($link = mysqli_fetch_row($tbl_tmp))
    {
        list($id) = $link;
        $rtn = f_keyimg_update($db_conn,$id);
        // echo "<br>".$rtn ;
    }
}
/////////////////
// 年関係なしアイテム
$mm = date('m');
$dd = date('d');
$strSQL = "SELECT max(`id`) FROM `tbl_ymd` WHERE `yyyy` = 9999 and `mm` = '$mm' and `dd` = '$dd' and `order` = 1 group by `calendar_id` ";
$tbl_tmp = mysqli_query($db_conn,$strSQL);
if($tbl_tmp)
{
    while($link = mysqli_fetch_row($tbl_tmp))
    {
        list($id) = $link;
        $rtn = f_keyimg_update($db_conn,$id);
        // echo "<br>".$rtn ;
    }
}
/////////////
?>
