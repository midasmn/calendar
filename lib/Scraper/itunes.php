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


//インサート
function f_insert_ymd($db_conn,$calendar_id,$yyyy,$mm,$dd,$list_title,$img_path,$img_alt,$href,$order)
{
   mb_language('Japanese');//←これ
   // $img_alt=mb_convert_encoding($img_alt,'UTF-8','auto');
    $sql = "INSERT INTO `tbl_ymd`(`id`, `calendar_id`, `yyyy`, `mm`, `dd`, `name`,`img_path`, `img_alt`, `href`, `order`, `createdate`) VALUES (NULL, '$calendar_id', '$yyyy', '$mm', '$dd', '$list_title','$img_path', '$img_alt', '$href', '$order', CURRENT_TIMESTAMP)";
    $result = mysqli_query($db_conn,$sql);
    if(!$result)
    {
        $rtn =  "NG";
        echo $result;
    }else{
        $rtn = "OK";
    }
    return $rtn;
}
/////////////
$yyyy = date('Y');
$mm = date('m');
$dd = date('d');
$dd = sprintf("%02d", $dd);
// $dd = "03";
//////////////////
/////////////////
$calendar_id = 352;
$list_title = "iTunesソング";
$get_href = "http://www.apple.com/jp/itunes/charts/songs/";
$rtn_st = f_itunes($db_conn,$calendar_id,$list_title,$get_href,$yyyy,$mm,$dd);//
echo $rtn_st;
$calendar_id = 353;
$list_title = "iTunesアルバム";
$get_href = "http://www.apple.com/jp/itunes/charts/albums/";
$rtn_st = f_itunes($db_conn,$calendar_id,$list_title,$get_href,$yyyy,$mm,$dd);//
echo $rtn_st;
$calendar_id = 354;
$list_title = "iTunes映画";
$get_href = "http://www.apple.com/jp/itunes/charts/movies/";
$rtn_st = f_itunes($db_conn,$calendar_id,$list_title,$get_href,$yyyy,$mm,$dd);//
echo $rtn_st;
$calendar_id = 355;
$list_title = "iTunesブック";
$get_href = "http://www.apple.com/jp/itunes/charts/paid-books/";
$rtn_st = f_itunes($db_conn,$calendar_id,$list_title,$get_href,$yyyy,$mm,$dd);//
echo $rtn_st;
$calendar_id = 356;
$list_title = "iTunes無料App";
$get_href = "http://www.apple.com/jp/itunes/charts/free-apps/";
$rtn_st = f_itunes($db_conn,$calendar_id,$list_title,$get_href,$yyyy,$mm,$dd);//
echo $rtn_st;
$calendar_id = 357;
$list_title = "iTunes有料App";
$get_href = "http://www.apple.com/jp/itunes/charts/paid-apps/";
$rtn_st = f_itunes($db_conn,$calendar_id,$list_title,$get_href,$yyyy,$mm,$dd);//
echo $rtn_st;
$calendar_id = 358;
$list_title = "iTunesミュージックビデオ";
$get_href = "http://www.apple.com/jp/itunes/charts/music-videos/";
$rtn_st = f_itunes($db_conn,$calendar_id,$list_title,$get_href,$yyyy,$mm,$dd);//
echo $rtn_st;



function f_itunes($db_conn,$calendar_id,$list_title,$get_href,$yyyy,$mm,$dd)
{

    $rtn = array();
    $rnk_cnt = 0;
    $url_cnt = 0;
    $img_cnt = 0;
    $title_cnt = 0;
    $auth_cnt = 0;
    $ccnt = 0;
    //ページ取得
    $html = file_get_html($get_href);
    //ランク
    foreach ($html->find('li strong') as $element)
    {
        $rtn['rnk'][$rnk_cnt] = $element->plaintext; 
        $rnk_cnt++;
    }
    //画像
     foreach ($html->find('li a img') as $element)
    {
        $rtn['alt'][$img_cnt] = $element->alt; 
        $rtn['img_url'][$img_cnt] = $element->src; 
        $img_cnt++;
    }
    //タイトル
     foreach ($html->find('h3 a') as $element)
    {
        $rtn['url'][$title_cnt] = $element->href;
        $rtn['title'][$title_cnt] = $element->plaintext; 
        $title_cnt++;
    }
    //作家
     foreach ($html->find('h4 a') as $element)
    {
        $rtn['auth'][$auth_cnt] = $element->plaintext; 
        $auth_cnt++;
    }

    $rtn_imgs = $rtn;
    //z
    $cnt = count($rtn_imgs['title']);
    $cnt=5;
    $i = 0;
    while ($i<$cnt) 
    {

        $rtnB = f_insert_ymd($db_conn,$calendar_id,$yyyy,$mm,$dd,$list_title,$rtn_imgs['img_url'][$i],$rtn_imgs['title'][$i].'-'.$rtn_imgs['auth'][$i],$rtn_imgs['url'][$i],$rtn_imgs['rnk'][$i]);
        $i++;
    }  
    // 解放する
    $html->clear();
    unset($rtn);
    return  $get_href;
}
?>