<?php

require 'simple_html_dom.php';

require 'lib/mysql-ini.php';
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
function f_update_flg($db_conn,$calendar_id)
{
    $sql = "UPDATE `tbl_rakuten` SET `cronflg` = 'ON' WHERE `calendar_id` = '$calendar_id'";
    $result = mysqli_query($db_conn,$sql);
}


//amazonランキング画像取得
// $get_url = 'http://www.amazon.co.jp/gp/bestsellers/books/2278488051'; //アマゾンコミックベストセラー
// function f_amazon_scrape_img($db_conn,$exm_url,$calendar_id,$yyyy,$mm,$dd)
// function f_amazon_scrape_img($db_conn,$get_url,$calendar_id,$description,$yyyy,$mm,$dd)
function f_rakuten_scrape_img($db_conn,$get_url,$calendar_id,$title,$yyyy,$mm,$dd)
{
    // $assoc_tag = '/tag=mittellogeblo-22';
    // $get_url .= $assoc_tag;
// echo "<br>".$get_url."<br>";
    $rtn = array();
    // 画像取得
    // // 文字化け対策のおまじない的（？）なもの。
   // mb_language('Japanese');//←これ
   //  $html = mb_convert_encoding(file_get_html($get_url),'UTF-8','auto');
    $html = file_get_html($get_url);
   //  //
    $img_cnt=0;
    $alt_cnt=0;
    $href_cnt=0;
    $list_title = $title;  
    //画像
    foreach ($html->find('.rnkRanking_imageBox a img') as $element)
    {
            $rtn['img'][$img_cnt] = $element->src; 
            $img_cnt++;
    }
    //alt
    foreach ($html->find('.rnkRanking_imageBox a img') as $element)
    {
            $rtn['alt'][$alt_cnt]= $element->alt; 
            $alt_cnt++;
    }
    //URL
    foreach ($html->find('.rnkRanking_imageBox a') as $element)
    {
            $rtn['href'][$href_cnt] = $element->href; 
            $href_cnt++;
    }
    $rtn_imgs = $rtn;
    //DB
    $cnt = count($rtn_imgs['img']);
    $cnt = 6;
    $i = 0;
    while ($i  <= $cnt) 
    {
        //insert
        f_insert_ymd($db_conn,$calendar_id,$yyyy,$mm,$dd,$list_title,$rtn_imgs['img'][$i],$rtn_imgs['alt'][$i],$rtn_imgs['href'][$i],$i+1);
        $i++;
    }   
    // 解放する
    $html->clear();
    unset($rtn);
    return "ok";
}

$yyyy = date('Y');
$mm = date('m');
$dd = date('d');

// $dd = "02";

//////////////////
// DBからNODES読み込み
/////////////////
$rtn_array = array();
//クーロン対象で未処理＆表示対象ON
$sql = 'SELECT `url`, `title`, `calendar_id` FROM `tbl_rakuten` WHERE `cronflg` = "OFF" and `onflg` = "ON"';
// echo $sql;
// $sql = 'SELECT `url`, `node`, `description`, `calendar_id` FROM `tbl_amazon` WHERE `cronflg` = "OFF" and `onflg` = "ON";';
$result = mysqli_query($db_conn,$sql);
$cnt = 1;
if($result)
{
    while($link = mysqli_fetch_row($result))
    {
        list($exm_url,$title, $calendar_id) = $link;
        //処理用URL
// echo "<br>".$exm_url;
        //スクレイピング処理
        $rtn_imgs = f_rakuten_scrape_img($db_conn,$exm_url,$calendar_id,$title,$yyyy,$mm,$dd);
        //フラグUPDATE
        f_update_flg($db_conn,$calendar_id);
        // echo $cnt."件目".$title."<br>";
        $cnt++;
        sleep(1); // サーバへの負荷を減らすため 1 秒間遅延処理
    }
}
echo "end";
?>