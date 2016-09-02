<?php

require 'simple_html_dom.php';

require '/home/midasmn/faceapglezon.info/public_html/calendar/lib/mysql-ini.php';
// データベースに接続
$db_conn = new mysqli($host, $user, $pass, $dbname)
or die("データベースとの接続に失敗しました");
$db_conn->set_charset('utf8');
// $result = mysqli_query($db_conn,$sql);
// $cnt = 16;

//google
function f_google_scrape_img_YAHOO($db_conn,$exm_url,$calendar_id,$list_title,$yyyy,$mm,$dd,$name,$order)
{
    $html = file_get_html($exm_url);
    $img_cnt=0;
    //画像
    foreach ($html->find('img') as $element)
    {
            $rtn['img'][$img_cnt] = $element->src;
            if($img_cnt==0)
            {
                if($order>5)
                {

                }else{
             f_insert_ymd($db_conn,$calendar_id,$yyyy,$mm,$dd,$list_title,$rtn['img'][$img_cnt],$name,"",$order);  
                }
                $img_cnt++;    
            }
    }
    // 解放する
    $html->clear();
    unset($rtn);
    return "ok";
}

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
//////////////////
/////////////////
$calendar_id = 343; //yahoo人物デイリー総数
$list_title = "Yahoo人物総数ランキング(デイリー)";
$get_href = "http://searchranking.yahoo.co.jp/total_ranking/people/";
$exm_find_st = '.patD a';
$rtn_st = f_yahoo($db_conn,$calendar_id,$list_title,$get_href,$yyyy,$mm,$dd,$exm_find_st );//
echo $rtn_st;
$calendar_id = 344; //yahoo人物デイリー総数
$list_title = "Yahooキーワードのランキング(デイリー)";
$get_href = "http://searchranking.yahoo.co.jp/total_ranking/general/";
$exm_find_st = '.patD a';
$rtn_st = f_yahoo($db_conn,$calendar_id,$list_title,$get_href,$yyyy,$mm,$dd,$exm_find_st );//
echo $rtn_st;
$calendar_id = 345; //yahoo人物デイリー総数
$list_title = "Yahoo動画検索のランキング(デイリー)";
$get_href = "http://searchranking.yahoo.co.jp/video_buzz/";
$exm_find_st = '.patB a';
$rtn_st = f_yahoo($db_conn,$calendar_id,$list_title,$get_href,$yyyy,$mm,$dd,$exm_find_st );//
echo $rtn_st;
$calendar_id = 346; //yahoo人物デイリー総数
$list_title = "Yahoo人物検索のランキング(デイリー)";
$get_href = "http://searchranking.yahoo.co.jp/people_buzz/";
$exm_find_st = '.patB a';
$rtn_st = f_yahoo($db_conn,$calendar_id,$list_title,$get_href,$yyyy,$mm,$dd,$exm_find_st );//
echo $rtn_st;
$calendar_id = 347; //yahoo人物デイリー総数
$list_title = "Yahoo人物検索のランキング(デイリー)";
$get_href = "http://searchranking.yahoo.co.jp/image_buzz/";
$exm_find_st = '.patB a';
$rtn_st = f_yahoo($db_conn,$calendar_id,$list_title,$get_href,$yyyy,$mm,$dd,$exm_find_st );//
echo $rtn_st;

function f_yahoo($db_conn,$calendar_id,$list_title,$get_href,$yyyy,$mm,$dd,$exm_find_st )
{
    $rtn = array();
    $cnt=0;
    //ページ取得
    $html = file_get_html($get_href);
    //キーワード取得
    foreach ($html->find($exm_find_st) as $element)
    {
        //ランク取得
        $rtn['rank'][$cnt] = $element->plaintext; 
        //画像検索用エンコード
        $get_img = "https://www.google.co.jp/search?hl=ja&source=lnms&tbm=isch&tbs=isz:lt,islt:svga&q=";
        if($calendar_id==343)
        {
            $exm_url = $get_img.urlencode("人物 ".$rtn['rank'][$cnt]);  
        }elseif($calendar_id==344||$calendar_id==345||$calendar_id==346||$calendar_id==347)
        {
            $exm_url = $get_img.urlencode($rtn['rank'][$cnt]);
        }
        // 画像スクレイピング処理
        $rtn_img = f_google_scrape_img_YAHOO($db_conn,$exm_url,$calendar_id,$list_title,$yyyy,$mm,$dd,$rtn['rank'][$cnt],$cnt+1);
        $cnt++;
    }
    // 解放する
    $html->clear();
    unset($rtn);
    echo $get_href;
}
?>