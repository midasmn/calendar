<?php

require 'simple_html_dom.php';

require 'lib/mysql-ini.php';
// データベースに接続
$db_conn = new mysqli($host, $user, $pass, $dbname)
or die("データベースとの接続に失敗しました");
$db_conn->set_charset('utf8');
// $result = mysqli_query($db_conn,$sql);

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
// $yyyy = '2016';
// $mm = '08';
// $dd = '30';
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
$datetete = date("Y-m-d",mktime(0,0,0,$mm,$dd-1,$yyyy)); 
/////////////////

//////////////////
/////////////////
$calendar_id = 349; //yahoo人物デイリー総数
$list_title = "オリコンCDアルバムデイリーランキング";
$get_href = "http://www.oricon.co.jp/rank/ja/d/";
$get_href .= $datetete."/";
$rtn_st = f_orikon($db_conn,$calendar_id,$list_title,$get_href,$yyyy,$mm,$dd);//
echo $rtn_st;
$calendar_id = 348; //yahoo人物デイリー総数
$list_title = "オリコンCDシングルデイリーランキング";
$get_href = "http://www.oricon.co.jp/rank/js/d/";
$get_href .= $datetete."/";
$rtn_st = f_orikon($db_conn,$calendar_id,$list_title,$get_href,$yyyy,$mm,$dd);//
echo $rtn_st;
//////////////////
/////////////////

function f_orikon($db_conn,$calendar_id,$list_title,$get_href,$yyyy,$mm,$dd)
{
    $rtn = array();
    $img_cnt=0;
    $title_cnt=0;
    $artist_cnt=0;
    $ccnt = 0;
    //ページ取得
    $html = file_get_html($get_href);
    foreach ($html->find('div .inner .image img') as $element)
    {
        $rtn['img'][$img_cnt] = $element->src; 
        // echo '<br><img src="'.$rtn['img'][$img_cnt] .'">';
        $img_cnt++;
    }
    //タイトル
    foreach ($html->find('div .inner .wrap-text h2') as $element)
    {
        $rtn['title'][$title_cnt] = $element->plaintext; 
        // echo "<br>".$rtn['title'][$title_cnt] ;
        $title_cnt++;
    }
    foreach ($html->find('div .inner .wrap-text p') as $element)
    {
        $rtn['artist'][$artist_cnt] = $element->plaintext; 
        // echo "<br>".$rtn['artist'][$artist_cnt] ;
        $artist_cnt++;
    }
    $rtn_imgs = $rtn;
    //
    $cnt = count($rtn_imgs['title']);
    $cnt = 5;
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
    return  $get_href;
}
?>