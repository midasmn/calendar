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
$w = date('w');//曜日 1=月曜日
// $dd = sprintf("%02d", $dd -1);
// $w = 1;
//////////////////
/////////////////
$calendar_id = 350; //yahoo人物デイリー総数
$list_title = "日本映画興行成績ランキング(月曜更新)";
$get_href = "http://movie.walkerplus.com/ranking/japan/";
$rtn_st = f_moveie($db_conn,$calendar_id,$list_title,$get_href,$yyyy,$mm,$dd,$w);
echo $rtn_st;
$calendar_id = 351; //yahoo人物デイリー総数
$list_title = "全米映画興行成績ランキング(月曜更新)";
$get_href = "http://movie.walkerplus.com/ranking/usa/";
$rtn_st = f_moveie($db_conn,$calendar_id,$list_title,$get_href,$yyyy,$mm,$dd,$w);
echo $rtn_st;
//////////////////
/////////////////

function f_moveie($db_conn,$calendar_id,$list_title,$get_href,$yyyy,$mm,$dd)
{
    $rtn = array();
    $img_cnt=0;
    $title_cnt=0;
    //ページ取得
    $html = file_get_html($get_href);
    //////////////////
    if($w==1)
    {
        //月曜なら処理
        foreach ($html->find('.movieMeta img') as $element)
        {
            $rtn['img'][$img_cnt] = $element->src; 
            $img_cnt++;
        }
        //タイトル
         foreach ($html->find('.movieInfo strong a') as $element)
        {
            $rtn['title'][$title_cnt] = $element->plaintext; 
            $title_cnt++;
        }
        $rtn_imgs = $rtn;
        //
        $cnt = count($rtn_imgs['title']);
        $cnt = 5;
        $i = 0;
        while ($i<$cnt) 
        {
            //insert
            $rtn = f_insert_ymd($db_conn,$calendar_id,$yyyy,$mm,$dd,$list_title,$rtn_imgs['img'][$i],$rtn_imgs['title'][$i],"",$i+1);
            $i++;
        }  
        // 解放する
        $html->clear();
        unset($rtn);
        echo "<br>end";
    }else{
        echo "月曜じゃない";
    }
}

?>