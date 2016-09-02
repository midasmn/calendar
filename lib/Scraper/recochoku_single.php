<?php

require 'simple_html_dom.php';

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


//インサート
function f_insert_ymd($db_conn,$calendar_id,$yyyy,$mm,$dd,$list_title,$img_path,$img_alt,$href,$order)
{
   mb_language('Japanese');//←これ
   $img_alt=mb_convert_encoding($img_alt,'UTF-8','auto');

    $sql = "INSERT INTO `tbl_ymd`(`id`, `calendar_id`, `yyyy`, `mm`, `dd`, `name`,`img_path`, `img_alt`, `href`, `order`, `createdate`) VALUES (NULL, '$calendar_id', '$yyyy', '$mm', '$dd', '$list_title','$img_path', '$img_alt', '$href', '$order', CURRENT_TIMESTAMP)";
    $result = mysqli_query($db_conn,$sql);
    if(!$result)
    {
        $rtn =  "NG";
    }else{
        $rtn = "OK";
    }
    return $rtn;
}
/////////////
$yyyy = date('Y');
$mm = date('m');
$dd = date('d');
// $dd = sprintf("%02d", $dd -1);
// $dd = "03";
//////////////////
//日付チェック
if($yyyy&&$mm){
    $timeStamp = strtotime($yyyy .'-'.$mm. "-".$dd);
    if($timeStamp === false)
    {
        $yyyy = date("Y");
        $mm = date("n");
        $dd = date("n");
    }
}else{
    $yyyy = date("Y");
    $mm = date("n");
    $dd = date("d");
}

// $yyyy = '2014';
// $mm = '11';
// $dd = '07';


$datetete = date("Y-m-d",mktime(0,0,0,$mm,$dd-1,$yyyy)); 
/////////////////
$calendar_id = 6882; //レコチョクシングル
$list_title = "レコチョクシングルデイリーランキング";

$get_href = "http://recochoku.jp/ranking/single/daily/";
// $get_href = "http://recochoku.jp/ranking/album/daily/";
// $get_href .= $datetete."/";
$rtn = array();
$img_cnt=0;
$title_cnt=0;
$artist_cnt=0;
$ccnt = 0;
//ページ取得

echo $get_href;
$html = file_get_html($get_href);


//画像

// $es = $html->find(‘table td[align=center]’);

foreach ($html->find('.info a img') as $element)
{
    $rtn['img'][$img_cnt] = $element->style; 
    if(strlen($rtn['img'][$img_cnt])<1){}else
    {
        $rtn['img'][$img_cnt] = str_replace('background:url(//', 'http://', $rtn['img'][$img_cnt]);
        $rtn['img'][$img_cnt] = str_replace(') no-repeat 50% 50%;', '', $rtn['img'][$img_cnt]);
        // 
        $rtn_alt = $element->alt; 
        $rtn_alts = explode("&nbsp;", $rtn_alt);
        $rtn['title'][$img_cnt]=$rtn_alts[0];
        $rtn['artist'][$img_cnt]=$rtn_alts[1];
        $img_cnt++;  
    }
    
}



$rtn_imgs = $rtn;
//
$cnt = count($rtn_imgs['title']);
$cnt = 6;
$i = 0;
while ($i<$cnt) 
{
    //insert

    $rtn = f_insert_ymd($db_conn,$calendar_id,$yyyy,$mm,$dd,$list_title,$rtn_imgs['img'][$i],$rtn_imgs['title'][$i].'-'.$rtn_imgs['artist'][$i],"",$i+1);
    $i++;
}  

// 解放する
$html->clear();
unset($rtn);

echo "end";
?>