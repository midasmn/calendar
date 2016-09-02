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
// tbl_calendar
// status=0　年関係なし
// status=1    検索ページ固定
// status=2 google画像「キーワード検索」
/////////////////////////////////////////////
// status=2 google画像「キーワード検索」
////////////////////////////////////////

//インサート
function f_insert_ymd($db_conn,$calendar_id,$yyyy,$mm,$dd,$list_title,$img_path,$img_alt,$href,$order,$keying)
{
   mb_language('Japanese');//←これ
   $img_alt=mb_convert_encoding($img_alt,'UTF-8','auto');

    $sql = "INSERT INTO `tbl_ymd`(`id`, `calendar_id`, `yyyy`, `mm`, `dd`, `name`,`img_path`, `img_alt`, `href`, `order`,`keyimg`, `createdate`) VALUES (NULL, '$calendar_id', '$yyyy', '$mm', '$dd', '$list_title','$img_path', '$img_alt', '$href', '$order', '$keying',CURRENT_TIMESTAMP)";
    $result = mysql_query($sql, $db_conn);
    if(!$result)
    {
        $rtn =  "NG";
    }else{
        $rtn = "OK";
    }
    return $rtn;
}
/////////////
function f_update_flg($db_conn,$table,$id)
{
    $sql = "UPDATE `$table` SET `cronflg` = '-' WHERE `id` = '$id'";
    $result = mysql_query($sql, $db_conn);
}


//amazonランキング画像取得

        //スクレイピング処理
        //フラグUPDATE
function f_bing_scrape_img($db_conn,$yyyy,$mm,$dd,$calendar_id,$list_title,$keyword,$img_url,$img_tag)
{
    $rtn = array();
    // 画像取得
    // // 文字化け対策のおまじない的（？）なもの。
    $exm_url = $img_url.urlencode($keyword);

    $html = file_get_html($exm_url);
    // $html = file_get_html($img_url.$keyword);

var_dump($html);
    
    $img_cnt=0;
    //画像
    foreach ($html->find('.dg_u') as $element)
    {
            
            // $rtn['t1'][$img_cnt] = $element->text; 
            $rtn['img'][$img_cnt] = $element->src; 
            if($img_cnt==0){$keying="KEY";}

            if($img_cnt>30)
            {}else{
// echo '<img src="'.$rtn['img'][$img_cnt].'">';
echo "<br>";
// $rtn['t1'][$img_cnt];
echo "<br>";
      //          f_insert_ymd($db_conn,$calendar_id,$yyyy,$mm,$dd,$list_title,$rtn['img'][$img_cnt],$rtn['alt'][$img_cnt] ,"",$img_cnt+1,$keying);
            }
            
// echo "<br>".$rtn['img'][$img_cnt] ;
            $img_cnt++;
    }
    //D
    // 解放する
    $html->clear();
    unset($rtn);
    return "ok";
}



//////////////////
// DBからNODES読み込み
/////////////////
// $calendar_id = 2; //カレンダーID
// $get_url = "https://www.google.co.jp/search?hl=ja&source=lnms&tbm=isch&tbs=isz:m&q=";  //取得URL Mサイズ


$rtn_array = array();
$yyyy = date('Y');
$mm = date('m');
$dd = date('d');

$sql = 'SELECT `id`, `title`, `keyword`, `img_url`, `img_tag` FROM `tbl_calendar` WHERE  `group` = "bing" and `status` = 2 and `onflg` = "ON" order by `order` limit 1;';
$result = mysql_query($sql,$db_conn);
$cnt = 1;
if($result)
{
    while($link = mysql_fetch_row($result))
    {
        list($calendar_id,$list_title,$keyword,$img_url,$img_tag) = $link;
        //スクレイピング処理
        $rtn_img = f_bing_scrape_img($db_conn,$yyyy,$mm,$dd,$calendar_id,$list_title,$keyword,$img_url,$img_tag);

        $cnt++;
        // sleep(1); // サーバへの負荷を減らすため 1 秒間遅延処理
    }
}
echo "end";
?>