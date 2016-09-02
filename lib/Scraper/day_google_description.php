<?php

require 'simple_html_dom.php';
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

/////////////////////////////////////////////
/////////////
function f_update_dayscription($db_conn,$id,$keyword)
{
    $sql = "UPDATE `tbl_ymd` SET `description` = '$keyword' WHERE `id` = '$id'";
    $result = mysql_query($sql, $db_conn);
}


//amazonランキング画像取得

        //スクレイピング処理
        //フラグUPDATE
function f_google_dayscription($db_conn,$id,$limg_alt,$img_url)
{
    $rtn = array();
    // 画像取得
    // // 文字化け対策のおまじない的（？）なもの。
    $exm_url = $img_url.urlencode($limg_alt);
    $html = file_get_html($exm_url);

// var_dump($html);
// <span class="st">
    
    $img_cnt=1;
    foreach ($html->find('span[class=st]') as $element){
        $rtn['item'][$img_cnt] = $element->plaintext;
        if($img_cnt>1)
        {}else{
            // echo $rtn['item'][$img_cnt];
            f_update_dayscription($db_conn,$id,$rtn['item'][$img_cnt]);
            $img_cnt++;
        } 
    }
    $html->clear();
    unset($rtn);
    return "ok";
}


$img_url = 'https://www.google.com/search?q=';
$sql = 'SELECT `id`, `img_alt` FROM `tbl_ymd` WHERE `order` = 1 and `description` = "" order by id desc limit 100';
///////////////////////////////////////
$result = mysql_query($sql,$db_conn);
$cnt = 1;
if($result)
{
    while($link = mysql_fetch_row($result))
    {
        list($id,$limg_alt) = $link;
        //スクレイピング処理
        $rtn_img = f_google_dayscription($db_conn,$id,$limg_alt,$img_url);
        $cnt++;
        // sleep(1); // サーバへの負荷を減らすため 1 秒間遅延処理
    }
}
echo "end";
?>