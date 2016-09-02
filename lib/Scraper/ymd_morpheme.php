<?php
// header('Content-Type: text/html; charset=utf-8');

$host = "localhost";   // 接続するMySQLサーバー
$user = "root";      // MySQLのユーザー名
$pass = "mn9621mnZ+";      // MySQLのパスワード
$dbname= "icalendar";      // DBの名前

// データベースに接続
if(!$db_conn = mysql_connect($host, $user, $pass)){
print("データベースにアクセスできません。");
exit;
}
$rtn = mysql_query("SET NAMES utf8" , $db_conn);
mysql_select_db($dbname);


//caltag
function f_get_cal_tag($db_conn,$calid)
{
    $sql = "SELECT `id`, `tags` FROM `tbl_calendar` WHERE `id` = '$calid'";
    $result = mysql_query($sql,$db_conn);
    if($result)
    {
        while($link = mysql_fetch_row($result))
        {
            list($calid,$tags) = $link;
            // $exm_url = urlencode($tags);
            $rtn_st = $tags;

        }
    }
    return $rtn_st;
}



///////////////////////////////
//Yahoo形態素
////////////////////////////////
function f_yahoo_morpheme($db_conn,$calid,$ymdid,$description)
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
// echo "<br>uuuuuu=".$url;
    // $url = "http://jlp.yahooapis.jp/MAService/V1/parse?appid=" . $appid . "&results=ma&sentence=" . $word;

    //戻り値をパースする
    $parse = simplexml_load_file($url);

// var_dump($parse);
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


//形態素テーブルインサート
function f_insert_ymd_morpheme($db_conn,$calid,$ymdid,$tag)
{
   mb_language('Japanese');//←これ
   $img_alt=mb_convert_encoding($img_alt,'UTF-8','auto');

    $sql =  "INSERT INTO `tbl_ymd_morpheme`(`id`, `calid`, `ymdid`, `tag`, `createdate`) VALUES (NULL,'$calid','$ymdid','$tag',CURRENT_TIMESTAMP)";
    $result = mysql_query($sql, $db_conn);
    if(!$result)
    {
        $rtn =  "NG";
    }else{
        $rtn = "OK";
    }
    return $rtn;
}
///形態素フラグアップデート
function f_morpheme_update($db_conn,$id)
{
    $sql = "UPDATE `tbl_ymd` SET `morpheme_flg` = 'ON' WHERE `id` = '$id' and `morpheme_flg` = 'OFF'";
    $result = mysql_query($sql, $db_conn);
    return $id;
}

/////////////////
// 説明文有りかつ形態素フラグOFFかつ100以内
// $strSQL = 'SELECT `id`, `calendar_id`, `img_alt` FROM `tbl_ymd` WHERE `description` <> "" and `morpheme_flg` = "OFF"';
$strSQL = 'SELECT `id`, `calendar_id`, `img_alt` FROM `tbl_ymd` WHERE  `morpheme_flg` = "OFF" ORDER BY `createdate` DESC';
$tbl_tmp = mysql_query($strSQL, $db_conn);
if($tbl_tmp)
{
    while($link = mysql_fetch_row($tbl_tmp))
    {
        list($ymdid,$calid,$description) = $link;
        //
        // $st_caltag = f_get_cal_tag($db_conn,$calid);
        //yahoo形態素
        $tag = f_yahoo_morpheme($db_conn,$calid,$ymdid,$description);
        // echo "<br>".$tag;
        //形態素テーブルインサート
        $rtn = f_insert_ymd_morpheme($db_conn,$calid,$ymdid,$tag);
        if($rtn=="OK"){
            ///形態素フラグアップデート
            $rtn = f_morpheme_update($db_conn,$ymdid);
        }
        sleep(1); 
    }
}
/////////////
?>
