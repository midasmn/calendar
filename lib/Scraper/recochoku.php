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
/////////////////
$calendar_id = 6882; //レコチョクシングル
$list_title = "レコチョクシングルデイリーランキング";
$get_href = "http://recochoku.jp/ranking/single/daily/";
$rtn_st = f_recochoku($db_conn,$calendar_id,$list_title,$get_href,$yyyy,$mm,$dd);//
echo $rtn_st;
$calendar_id = 6883; //レコチョクシングル
$list_title = "レコチョクアルバムデイリーランキング";
$get_href = "http://recochoku.jp/ranking/album/daily/";
$rtn_st = f_recochoku($db_conn,$calendar_id,$list_title,$get_href,$yyyy,$mm,$dd);//
echo $rtn_st;
$calendar_id = 6884; //レコチョクシングル
$list_title = "レコチョクビデオクリップデイリーランキング";
$get_href = "http://recochoku.jp/ranking/video/daily/";
$rtn_st = f_recochoku($db_conn,$calendar_id,$list_title,$get_href,$yyyy,$mm,$dd);//
echo $rtn_st;
$calendar_id = 6885; //レコチョクシングル
$list_title = "レコチョク着うたデイリーランキング";
$get_href = "http://recochoku.jp/ranking/uta/daily/";
$rtn_st = f_recochoku($db_conn,$calendar_id,$list_title,$get_href,$yyyy,$mm,$dd);//
echo $rtn_st;
$calendar_id = 6886; //レコチョクシングル
$list_title = "レコチョク着ボイスデイリーランキング";
$get_href = "http://recochoku.jp/ranking/voice/daily/";
$rtn_st = f_recochoku($db_conn,$calendar_id,$list_title,$get_href,$yyyy,$mm,$dd);//
echo $rtn_st;
$calendar_id = 6887; //レコチョクシングル
$list_title = "レコチョク呼出音デイリーランキング";
$get_href = "http://recochoku.jp/ranking/rbt/daily/";
$rtn_st = f_recochoku($db_conn,$calendar_id,$list_title,$get_href,$yyyy,$mm,$dd);//
echo $rtn_st;
/////////////////
/////////////////


function f_recochoku($db_conn,$calendar_id,$list_title,$get_href,$yyyy,$mm,$dd)
{
    $rtn = array();
    $img_cnt=0;
    $title_cnt=0;
    $artist_cnt=0;
    $ccnt = 0;
    //ページ取得
    $html = file_get_html($get_href);
    if($calendar_id==6885||$calendar_id==6886||$calendar_id==6887)
    {
        foreach ($html->find('.info  img') as $element)
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
    }else{
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