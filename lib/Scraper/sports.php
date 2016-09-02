<?php

require 'simple_html_dom.php';
date_default_timezone_set('Asia/Tokyo');

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
function f_update_flg($db_conn,$node)
{
    $sql = "UPDATE `tbl_amazon` SET `cronflg` = 'ON' WHERE `node` = '$node'";
    $result = mysqli_query($db_conn,$sql);
}


//amazonランキング画像取得
// $get_url = 'http://www.amazon.co.jp/gp/bestsellers/books/2278488051'; //アマゾンコミックベストセラー
// function f_amazon_scrape_img($db_conn,$exm_url,$calendar_id,$yyyy,$mm,$dd)
// function f_amazon_scrape_img($db_conn,$get_url,$calendar_id,$description,$yyyy,$mm,$dd)
function f_sports_scrape_img($db_conn,$calendar_id,$title,$get_url,$sc_tag,$yyyy,$mm,$dd)
{
    // $assoc_tag = '/tag=mittellogeblo-22';
    // $get_url .= $assoc_tag;
    $rtn = array();
    // 画像取得
    // // 文字化け対策のおまじない的（？）なもの。
   // mb_language('Japanese');//←これ
   //  $html = mb_convert_encoding(file_get_html($get_url),'UTF-8','auto');
    $html = file_get_html($get_url);
   //  //
    $img_cnt=1;
    $list_title = $title;  
    $base_url = "http://faceapglezon.info/calendar/lib/Scraper/sports/";
    //画像
    // foreach ($html->find('.yjMS .yjM a text') as $element)
    foreach ($html->find($sc_tag) as $element)
    {
            $rtn['rank'][$img_cnt] = $img_cnt; 
            $rtn['list_title'][$img_cnt] = $element->plaintext; 
            $rtn['alt'][$img_cnt] = $rtn['list_title'][$img_cnt];
            switch ($rtn['list_title'][$img_cnt] ) {
                case 'ＤｅＮＡ':
                    $rtn['img'][$img_cnt] = $base_url.'baystars.jpg';
                    $rtn['href'][$img_cnt] = "http://www.npb.or.jp/cc/team/jump_db.html";
                    break;
                case '巨人':
                    $rtn['img'][$img_cnt] = $base_url.'giants.jpg';
                    $rtn['href'][$img_cnt] = "http://www.npb.or.jp/cc/team/jump_g.html";
                    break;
                case '中日':
                    $rtn['img'][$img_cnt] = $base_url.'doragons.jpg';
                    $rtn['href'][$img_cnt] = "http://www.npb.or.jp/cc/team/jump_d.html";
                    break;
                case 'ヤクルト':
                    $rtn['img'][$img_cnt] = $base_url.'yukult.jpg';
                    $rtn['href'][$img_cnt] = "http://www.npb.or.jp/cc/team/jump_s.html";
                    break;
                case '阪神':
                    $rtn['img'][$img_cnt] = $base_url.'tigers.jpg';
                    $rtn['href'][$img_cnt] = "http://www.npb.or.jp/cc/team/jump_t.html";
                    break;
                case '広島':
                    $rtn['img'][$img_cnt] = $base_url.'carp.jpg';
                    $rtn['href'][$img_cnt] = "http://www.npb.or.jp/cc/team/jump_c.html";
                    break;
                // 
                case '西武':
                    $rtn['img'][$img_cnt] = $base_url.'lions.jpg';
                    $rtn['href'][$img_cnt] = "http://www.npb.or.jp/cc/team/jump_l.html";
                    break;
                case 'ソフトバンク':
                    $rtn['img'][$img_cnt] = $base_url.'hawks.jpg';
                    $rtn['href'][$img_cnt] = "http://www.npb.or.jp/cc/team/jump_h.html";
                    break;
                case '日本ハム':
                    $rtn['img'][$img_cnt] = $base_url.'fighters.jpg';
                    $rtn['href'][$img_cnt] = "http://www.npb.or.jp/cc/team/jump_f.html";
                    break;
                case 'ロッテ':
                    $rtn['img'][$img_cnt] = $base_url.'marines.jpg';
                    $rtn['href'][$img_cnt] = "http://www.npb.or.jp/cc/team/jump_m.html";
                    break;
                case '楽天':
                    $rtn['img'][$img_cnt] = $base_url.'eagles.jpg';
                    $rtn['href'][$img_cnt] = "http://www.npb.or.jp/cc/team/jump_e.html";
                    break;
                case 'オリックス':
                    $rtn['img'][$img_cnt] = $base_url.'buffaloes.jpg';
                    $rtn['href'][$img_cnt] = "http://www.npb.or.jp/cc/team/jump_bs.html";
                    break;
                default:
                    # code...
                    break;
            }
            $img_cnt++;
    }
    $rtn_imgs = $rtn;

// var_dump($rtn_imgs);
    //DB
    $cnt = count($rtn_imgs['img']);
    $i = 1;
    while ($i  <= $cnt) 
    {
        //insert
        f_insert_ymd($db_conn,$calendar_id,$yyyy,$mm,$dd,$rtn_imgs['list_title'][$i],$rtn_imgs['img'][$i],$rtn_imgs['alt'][$i],$rtn_imgs['href'][$i],$i);
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

// $yyyy = 2016;
// $mm = 9;
// $dd = 1;
//////////////////
// DBからNODES読み込み
/////////////////
$rtn_array = array();
//クーロン対象で未処理＆表示対象ON
$sql = 'SELECT `id`, `title`, `sc_url`, `sc_tag` FROM `tbl_calendar` WHERE `group` = "sports"';
$result = mysqli_query($db_conn,$sql);
$cnt = 1;
if($result)
{
    while($link = mysqli_fetch_row($result))
    {
        list($calendar_id,$title,$get_url,$sc_tag) = $link;
        //スクレイピング処理
        $rtn_imgs = f_sports_scrape_img($db_conn,$calendar_id,$title,$get_url,$sc_tag,$yyyy,$mm,$dd);
        //フラグUPDATE
        // f_update_flg($db_conn,$node);
        // echo $cnt."件目<br>";
        $cnt++;
        sleep(1); // サーバへの負荷を減らすため 1 秒間遅延処理
    }
}
echo "end";
?>