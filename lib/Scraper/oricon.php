<?php

require 'simple_html_dom.php';

require '/home/midasmn/faceapglezon.info/public_html/calendar/lib/mysql-ini.php';
// データベースに接続
$db_conn = new mysqli($host, $user, $pass, $dbname)
or die("データベースとの接続に失敗しました");
$db_conn->set_charset('utf8');
/////////////////////////////
//Yahoo形態素
////////////////////////////////
function f_yahoo_morpheme($description)
{
    $rtn_st = "";
    //アプリケーションIDのセット
    $appid = "dj0zaiZpPVJXSGJNOTdoeWEwTSZzPWNvbnN1bWVyc2VjcmV0Jng9Mjc-";
    //形態素解析したい文章
    // mb_language('Japanese');//←これ
    $description=mb_convert_encoding($description,'UTF-8','auto');
    $word = $description;
    //URLの組み立て
    $url = "http://jlp.yahooapis.jp/MAService/V1/parse?appid=".$appid."&results=ma,uniq&uniq_filter=9&sentence=".urlencode($word);
    //戻り値をパースする
    $parse = simplexml_load_file($url);
    //戻り値（オブジェクト）からループでデータを取得する
    foreach($parse->ma_result->word_list->word as $value){
        //品詞を「,」で区切る
        $tmp_st = $value->surface;
        if(strlen($tmp_st)>1){
            $rtn_st .= $value->surface;
            $rtn_st .=  ",";    //カンマ区切りに
        }
    }
    return $rtn_st;
}
//インサート
function f_insert_ymd($db_conn,$calendar_id,$yyyy,$mm,$dd,$list_title,$img_path,$img_alt,$href,$order)
{
   mb_language('Japanese');//←これ
   $img_alt=mb_convert_encoding($img_alt,'UTF-8','auto');
   // 
   $tag = f_yahoo_morpheme($img_alt);

    $sql = "INSERT INTO `tbl_ymd`(`id`, `calendar_id`, `yyyy`, `mm`, `dd`, `name`,`img_path`, `img_alt`, `href`, `order`, `tag`,`createdate`) VALUES (NULL, '$calendar_id', '$yyyy', '$mm', '$dd', '$list_title','$img_path', '$img_alt', '$href', '$order', '$tag', CURRENT_TIMESTAMP)";
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