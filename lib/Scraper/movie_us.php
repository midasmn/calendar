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
$w = date('w');//曜日 1=月曜日
// $dd = sprintf("%02d", $dd -1);
// $w = 1;
//////////////////
/////////////////
$calendar_id = 351; //yahoo人物デイリー総数
$list_title = "全米映画興行成績ランキング(月曜更新)";
$get_href = "http://movie.walkerplus.com/ranking/usa/";
// $get_url = "https://
// echo $get_href;

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
        // echo "<br>".$rtn['img'][$img_cnt];
        // $rtn['img'][$img_cnt] = str_replace('%22', '', $element->src); 
        // echo "<br>".$img_cnt."<img src=".$rtn['img'][$img_cnt].">";
        $img_cnt++;
    }
    //タイトル
     foreach ($html->find('.movieInfo strong a') as $element)
    {
        $rtn['title'][$title_cnt] = $element->plaintext; 
        // echo "<br>".$artist_cnt."title".$rtn['title'][$title_cnt];
        $title_cnt++;
    }
    $rtn_imgs = $rtn;
    //
    $cnt = count($rtn_imgs['title']);
    $cnt = 6;
    $i = 0;
    while ($i<$cnt) 
    {
        //insert
        $rtn = f_insert_ymd($db_conn,$calendar_id,$yyyy,$mm,$dd,$list_title,$rtn_imgs['img'][$i],$rtn_imgs['title'][$i],"",$i+1);
        $i++;
    // echo "<br>".$i;
    }  

    // 解放する
    $html->clear();
    unset($rtn);
    echo "<br>end";
}else{
    echo "月曜じゃない";
}


?>