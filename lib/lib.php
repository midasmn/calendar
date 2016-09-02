<?php
// モバイル判定
function is_mobile()
{
    $useragents = array(
        'iPhone', // iPhone
        'iPod', // iPod touch
        'iPad', // iPad
        'Android.*Mobile', // 1.5+ Android *** Only mobile
        'Windows.*Phone', // *** Windows Phone
        'dream', // Pre 1.5 Android
        'CUPCAKE', // 1.5+ Android
        'blackberry9500', // Storm
        'blackberry9530', // Storm
        'blackberry9520', // Storm v2
        'blackberry9550', // Storm v2
        'blackberry9800', // Torch
        'webOS', // Palm Pre Experimental
        'incognito', // Other iPhone browser
        'webmate' // Other iPhone browser
    );
    $pattern = '/'.implode('|', $useragents).'/i';
    return preg_match($pattern, $_SERVER['HTTP_USER_AGENT']);
}
// iPad判定
function is_ipad()
{
  $useragents = array('iPad');
  $pattern = '/'.implode('|', $useragents).'/i';
  return preg_match($pattern, $_SERVER['HTTP_USER_AGENT']);
}
###################################################################################
#汎用名前取得エンコード関数 //////////////////////////////////////////
###################################################################################
function f_get_comon_item($db_conn,$tableName,$Namefiled,$Idfiled,$id)
{
  $aryname = array();
  $strSQL = "SELECT $Idfiled,$Namefiled FROM $tableName WHERE $Idfiled = '$id'";
// echo "<br>sql=".$strSQL;
  $tbl_tmp = mysqli_query($db_conn, $strSQL);
  if($tbl_tmp)
  {
    while($rec_tmp = mysqli_fetch_row($tbl_tmp))
    {
      $aryname[$rec_tmp[0]] = $rec_tmp[1];
    }
  }
  foreach ($aryname as $key => $value)
  {
    $name = $value;
  }
  return $name;
}
// 
// SNSテーブルインサート
function f_insert_shub_cron($db_conn,$search_tag)
{
  $sql = "INSERT INTO shub_cron (search_tag) VALUES ('$search_tag')";
// echo "sql=".$sql;
  $result = mysqli_query( $db_conn,$sql);
  if(!$result)
  {
    $rtn = "NG";
  }else{
    $rtn = "OK";
  }
  f_update_shub_cnt ($db_conn,'TAG');
  return $rtn;
}
// SNSテーブルインサート
function f_insert_shub_contents ($db_conn,$sns_id, $search_tag, $user_id, $screen_name, $tweet_name, $tweet_icon, $id_str, $tweet_text, $tweet_date, $tweet_hashtag, $tweet_img, $tweet_img_short, $tweet_img_display_url)
{
  $user_agent =  $_SERVER['HTTP_USER_AGENT'];
  $remote_addr =  $_SERVER["REMOTE_ADDR"] ;
  $search_tag = mysqli_real_escape_string($db_conn,$search_tag);
  $tweet_name = mysqli_real_escape_string($db_conn,$tweet_name);
  $tweet_text = mysqli_real_escape_string($db_conn,$tweet_text);
  $tweet_hashtag = mysqli_real_escape_string($db_conn,$tweet_hashtag);
  $sql = "INSERT INTO shub_contents (sns_id, search_tag, user_id, screen_name, tweet_name, tweet_icon, id_str, tweet_text, tweet_date, tweet_hashtag, tweet_img, tweet_img_short, tweet_img_display_url,user_agent, remote_addr) VALUES ($sns_id, '$search_tag', '$user_id', '$screen_name', '$tweet_name', '$tweet_icon', '$id_str', '$tweet_text', '$tweet_date', '$tweet_hashtag', '$tweet_img', '$tweet_img_short', '$tweet_img_display_url'
  ,'$user_agent', '$remote_addr')";
// echo "sql=".$sql;
  $result = mysqli_query( $db_conn,$sql);
  if(!$result)
  {
    $rtn = "NG";
  }else{
    $rtn = "OK";
  }
  // 
  f_update_shub_cnt ($db_conn,'CONT');
  f_update_shub_cnt ($db_conn,'TAG');
  return $rtn;
}
// カウンタインサート
function f_update_shub_cnt ($db_conn,$flg_name)
{
  $cnt=0;
  switch ($flg_name) 
  {
    case 'CONT':
      $cnt = f_get_contents_cnt($db_conn);
      break;
    case 'TAG':
      $cnt = f_get_tag_cnt($db_conn);
      break;
    case 'SELECT':
      $cnt = f_get_page_all_cnt($db_conn,"SELECT","");
      break;
    case 'KEEP':
      $cnt = f_get_page_all_cnt($db_conn,"KEEP","");
      break;
    case 'TRASH':
      $cnt = f_get_page_all_cnt($db_conn,"TRASH","");
      break;
  }
  $sql = "UPDATE  `shub_cnt` SET  `cnt` = $cnt  WHERE `flg_name`  = '$flg_name'";
// echo "sql=".$sql;
  $result = mysqli_query( $db_conn,$sql);
  if(!$result)
  {
    $rtn = "NG";
  }else{
    $rtn = "OK";
  }
  return $rtn;
}
// // MAX
function f_get_sns_id_max($db_conn,$sns_id,$search_tag)
{
  $aryname = array();
  $strSQL = "SELECT id , max(id_str) FROM shub_contents WHERE sns_id = '$sns_id' AND search_tag = '$search_tag'";
  $tbl_tmp = mysqli_query($db_conn, $strSQL);
  if($tbl_tmp)
  {
    while($rec_tmp = mysqli_fetch_row($tbl_tmp))
    {
      $aryname[$rec_tmp[0]] = $rec_tmp[1];
    }
  }
  foreach ($aryname as $key => $value)
  {
    $name = $value;
  }
  return $name;
}
// 登録数カウント
function f_get_contents_cnt($db_conn)
{
  $aryname = array();
  $strSQL = "SELECT id , count(id) FROM shub_contents ";
  $tbl_tmp = mysqli_query($db_conn, $strSQL);
  if($tbl_tmp)
  {
    while($rec_tmp = mysqli_fetch_row($tbl_tmp))
    {
      $aryname[$rec_tmp[0]] = $rec_tmp[1];
    }
  }
  foreach ($aryname as $key => $value)
  {
    $name = $value;
  }
  return $name;
}
// タグカウント
function f_get_tag_cnt($db_conn)
{
  $aryname = array();
  $strSQL = "SELECT  distinct search_tag FROM shub_contents group by search_tag";
  $tbl_tmp = mysqli_query($db_conn, $strSQL);
  $numrows = mysqli_num_rows($tbl_tmp);
  return $numrows;
}
// 詳細
function f_get_datail($db_conn,$id)
{
  $rtn_st = "";
  $strSQL = "SELECT `id`, `sns_id`, `search_tag`, `user_id`, `screen_name`, `tweet_name`, `tweet_icon`, `id_str`, `tweet_text`, `tweet_date`, `tweet_hashtag`, `tweet_img`, `tweet_img_short`, `tweet_img_display_url` FROM `shub_contents` WHERE `id` = $id";
  $result = mysqli_query($db_conn,$strSQL);
  if($result)
  {
    while($link = mysqli_fetch_row($result))
    {
      list($id,$sns_id,$search_tag,$user_id,$screen_name,$tweet_name,$tweet_icon,$id_str,$tweet_text,$tweet_date,$tweet_hashtag,$tweet_img,$tweet_img_short,$tweet_img_display_url) = $link;
      // 
      $url ='https://twitter.com/search?q='.$screen_name.'" target="_blank"';
      if($sns_id==1){$sns_st='<i class="fa fa-twitter-square  social-tw"></i>';}
      elseif($sns_id==2){$sns_st='<i class="fa fa-facebook-square fa-3x social-fb" ></i>';}
      elseif($sns_id==3){$sns_st='<i  class="fa fa-instagram-square fa-3x social-gp" ></i>';}
      $rtn_st .= '<div  style="color:white;>';
      $rtn_st .= '<div class="modal-body">';
      $rtn_st .= '<img src="'.$tweet_img.'" class="img-responsive" style="background-color:#f0f0f0;">';
      $rtn_st .= '<p class="h3">'.$sns_st.'</p>';
      $rtn_st .= '<a href="'.$url .'"><img src="'.$tweet_icon.'" class="img-responsive img-circle" style="background-color:#f0f0f0;"></a>';
      $rtn_st .= '<p><label class="h3">ユーザID：</label>'.$user_id.'</p>';
      $rtn_st .= '<p><label class="h3">表示名：</label><a href="'.$url .'">'.$screen_name.'</a></p>';
      $rtn_st .= '<p><label class="h3">Tweet_NAME：</label>'.$tweet_name.'</p>';
      $rtn_st .= '<p><label class="h3">Tweet_ID：</label>'.$id_str.'</p>';
      $rtn_st .= '<p><label class="h3">投稿日：</label>'.$tweet_date.'</p>';
      $rtn_st .= '<p><label class="h3">画像短縮URL：</label>'.$tweet_img_short.'</p>';
      $rtn_st .= '<p><label class="h3">画像表示URL：</label>'.$tweet_img_display_url.'</p>';

      if($tweet_hashtag)
      {
        $tag_arr = explode( ',', $tweet_hashtag );
        foreach($tag_arr as $tag_itm)
        { 
          $rtn_st .= '<p><h3><span class="label label-default">'.$tag_itm.'</span></h3></p>';
        }
      }
      $rtn_st .= '<p><label class="h3">Tweet：</label>'.$tweet_text.'</p>';
      $rtn_st .= '</div>';
    }
  }
  return $rtn_st; 
}
// タグリスト
function f_get_taglist($db_conn)
{
  $rtn_st = "";
  $strSQL = "SELECT distinct (`search_tag`) FROM `shub_contents`  order by id desc";
  $result = mysqli_query($db_conn,$strSQL);
  if($result)
  { 
    while($link = mysqli_fetch_row($result))
    {
      list($search_tag) = $link;
      $search_tag_st = str_replace("#", "", $search_tag);
      $tag_cnt = f_get_page_all_cnt($db_conn,"TAG",$search_tag);
      // 
      $rtn_st .= '<a  href="list.php?search_tag='.$search_tag_st.'" stlye="margin:0 5px 5px; padding: 5px;">';
      $rtn_st .= '<button class="btn btn-warning" style="margin:6px 2px 6px 2px;">';
      $rtn_st .= '<small><i class="fa fa-tag"></i>'.$search_tag.'<span class="badge">'.number_format($tag_cnt).'</span></small></button>';
      $rtn_st .= '</a>';
      f_insert_shub_cron($db_conn,$search_tag);
    }
  }
  return $rtn_st; 
}
// 全件
function f_get_page_all_cnt($db_conn,$all,$search_tag)
{
  $aryname = array();
  if($all=="ALL"){
    $strSQL = "SELECT  id FROM shub_contents"; 
  }elseif($all=="SELECT"){
    $strSQL = "SELECT  id FROM shub_contents WHERE star_flg = 1"; 
  }elseif($all=="KEEP"){
    $strSQL = "SELECT  id FROM shub_contents WHERE star_flg = 2"; 
  }elseif($all=="TRASH"){
    $strSQL = "SELECT  id FROM shub_contents WHERE del_flg = 0"; 
  }elseif($all=="CRON"){
    $strSQL = "SELECT  id FROM shub_cron"; 
  }else{
    $strSQL = "SELECT  id FROM shub_contents WHERE search_tag = '$search_tag' order by id_str desc";
  }
  $tbl_tmp = mysqli_query($db_conn, $strSQL);
  $numrows = mysqli_num_rows($tbl_tmp);
  return $numrows;
}
// 
function f_update_flg($db_conn,$table,$item,$value,$id)
{
  $sql = "UPDATE $table SET  $item = $value ,`update_date` = now() WHERE `id` = $id";
  $result = mysqli_query($db_conn,$sql);
  if(!$result)
  {
    $rtn_st =$sql;
  }else{
    $rtn_st ="OK";
  }
  return $rtn_st;
}
//////////////////////////////////////////////////////////////////////////
// 画像一覧
//   $page = isset($_GET['page']) ? $_GET['page'] : 1;
// $page_size = 50;
//////////////////////////////////////////////////////////////////////////
function f_get_img_page($db_conn,$page_size,$page,$target_page,$search_tag)
{
  if($search_tag)
  {
    if(strpos($search_tag,'#') === false){
    $search_tag = '#'.$search_tag;    
    }
    $where_sql = "WHERE  `search_tag` = '$search_tag'";
    $pager_url = "&search_tag=".$search_tag;
    $search_tag_st = $search_tag."：";
  }else{
    $where_sql = "";
    $search_tag_st = "";
  }
  // 
  if($target_page=="select.php")
  {
    //セレクト画像
    $page_all_cnt = f_get_page_all_cnt($db_conn,"SELECT","");
    $base_sql = "SELECT `id`, `sns_id`, `search_tag`, `user_id`, `screen_name`, `tweet_name`, `tweet_icon`, 
    `id_str`, `tweet_text`, `tweet_date`, `tweet_hashtag`, `tweet_img`, `tweet_img_short`, `tweet_img_display_url` ,`star_flg`,`del_flg`
    FROM `shub_contents`  WHERE star_flg = 1 AND del_flg <> 0 order by id_str desc  ";
  }elseif($target_page=="keep.php")
  {
    //KEEP画像
    $page_all_cnt = f_get_page_all_cnt($db_conn,"KEEP","");
    $base_sql = "SELECT `id`, `sns_id`, `search_tag`, `user_id`, `screen_name`, `tweet_name`, `tweet_icon`, 
    `id_str`, `tweet_text`, `tweet_date`, `tweet_hashtag`, `tweet_img`, `tweet_img_short`, `tweet_img_display_url` ,`star_flg`,`del_flg`
    FROM `shub_contents`  WHERE star_flg = 2  AND del_flg <> 0 order by id_str desc  ";
  }elseif($target_page=="trash.php") 
  {
    //ぼつ画像
    $page_all_cnt = f_get_page_all_cnt($db_conn,"TRASH","");
    $base_sql = "SELECT `id`, `sns_id`, `search_tag`, `user_id`, `screen_name`, `tweet_name`, `tweet_icon`, 
    `id_str`, `tweet_text`, `tweet_date`, `tweet_hashtag`, `tweet_img`, `tweet_img_short`, `tweet_img_display_url` ,`star_flg`,`del_flg`
    FROM `shub_contents`  WHERE del_flg = 0 order by id_str desc  ";
  }else{
    // 通常一覧
    if($search_tag) 
    {
      $page_all_cnt = f_get_page_all_cnt($db_conn,"TAG",$search_tag);
    }else{
      $page_all_cnt = f_get_page_all_cnt($db_conn,"ALL","");
    
    }
  // タグ検索
    $base_sql = "SELECT `id`, `sns_id`, `search_tag`, `user_id`, `screen_name`, `tweet_name`, `tweet_icon`, 
    `id_str`, `tweet_text`, `tweet_date`, `tweet_hashtag`, `tweet_img`, `tweet_img_short`, `tweet_img_display_url` ,`star_flg`,`del_flg`
    FROM `shub_contents`  $where_sql order by id_str desc  ";
  }
  $page = (!isset($page)) ? 1 : $page;
  $page_count = ceil($page_all_cnt / $page_size);
  $start = ($page - 1) * ($page_size);
  $end = ($page * $page_size - 1 < $page_all_cnt) ? $page * $page_size - 1 : $page_all_cnt;

  $page_sql = " limit $start ,$page_size ";
  $sql = $base_sql.$page_sql;
// echo "<br>sql=".$sql;
  $result = mysqli_query($db_conn,$sql);
  if(!$result)
  {
  }else
  {
    $numrows = mysqli_num_rows($result);

    if($numrows>0)
    {
      // 件数
      $cur_cnt = $page_size*$page;
      if($cur_cnt>$page_all_cnt)
      {
        $cur_cnt = $cur_cnt-$page_size+$numrows;
      }
      // 
      $from_cnt = $cur_cnt-$numrows+1;
      $rtn_st = '';
      $rtn_st .= '<div class="col-xs-12 col-md-12" >';
      $rtn_st .=   '<h5 class="col-md-8" style="margin-bottom: 20px;;font-weight:200;1px;text-shadow:1px1px 0 rgba(0,0,0,0.1);color:#fff;">'."\n";
      $rtn_st .= $search_tag_st."(".number_format($page_all_cnt)."点中".number_format($from_cnt)."-".number_format($cur_cnt)."点表示)".number_format($page)."/".number_format($page_count)."ページ";
      $rtn_st .= '</h5>';
      $rtn_st .= '</div>';
      $cnt = 1;
      while($link = mysqli_fetch_row($result))
     {
        list($id,$sns_id,$search_tag,$user_id,$screen_name,$tweet_name,$tweet_icon,$id_str,$tweet_text,$tweet_date,$tweet_hashtag,$tweet_img,$tweet_img_short,$tweet_img_display_url,$star_flg,$del_flg) = $link;
        // 
        $url ='https://twitter.com/search?q='.$screen_name.'" target="_blank"';
        // タブ用画像パス
        $img_path = $tweet_img;
        $url_ch = "detail.php?id=".$id;
        $rtn_st .= '<div class="col-xs-12 col-sm-6 col-md-4 col-lg-2" >';
        $rtn_st .= '<div class="thumbnail  ">';
        $rtn_st .= '<a href="'.$url_ch.'"><img '.($del_flg==0 ? 'class="img-circle" style="border:5px solid #FF0000;"':'').' src="'.$img_path.'">';
        $rtn_st .= '<div class="caption">';
        $rtn_st .= '<p><a href="'.$url.'" target="_blank"><img class="img-circle" src="'.$tweet_icon.'">'.$screen_name.'</a>&nbsp;&nbsp;'.$tweet_date.'</p>';
        if($tweet_hashtag) //タグ分割
        {
          $tag_arr = explode( ',', $tweet_hashtag );
          foreach($tag_arr as $tag_itm)
          { 
            $rtn_st .= '<p><h5><a href="index.php?mode=search_tag&search_tag='.$tag_itm.'"><span class="label label-default">'.$tag_itm.'</span></a></h5></p>';
          }
        }
        $rtn_st .= '<p class="text-left">';
        // 優先ボタン
        $rtn_st .= '<a href="javascript: void(0)" class="heartbtn" data-id="'.$id.'" >'."\n";
        $rtn_st .= '<span class="glyphicon glyphicon-heart hearttggle_'.$id.' btn-sm btn-'.($star_flg==1 ? 'success': 'default').'" title="オウンド公開"></span>'."\n";
        $rtn_st .= '</a>';
        // いいね
        $rtn_st .= '<a href="javascript: void(0)" class="starbtn" data-id="'.$id.'" >'."\n";
        $rtn_st .= '<span  class="glyphicon glyphicon-star startggle_'.$id.' btn-sm btn-'.($star_flg==2 ? 'primary':'default').'" title="キープ"></span>'."\n";
        $rtn_st .= '</a>'."\n";
        // .優先ボタン
        $rtn_st .= '</p>';
        $rtn_st .= '<p class="text-right">';
        // ボツボタン
        $rtn_st .= '<a href="javascript: void(0)" class="delbtn" data-id="'.$id.'">';
        if($del_flg=="0"){
          $rtn_st .= '<span  class="glyphicon glyphicon-trash deltggle_'.$id.' btn-xs btn-danger" title="戻す"></span>';
          $rtn_st .= '<span  style="display: none" class="glyphicon glyphicon-trash deltggle_'.$id.' btn-xs btn-default" title="ボツに"></span>';
        }else{
          $rtn_st .= '<span  style="display: none" class="glyphicon glyphicon-trash deltggle_'.$id.' btn-xs btn-danger" title="戻す"></span>';
          $rtn_st .= '<span  class="glyphicon glyphicon-trash deltggle_'.$id.' btn-xs btn-default" title="ボツに"></span>';
        }
        $rtn_st .= '</a>';
        // .ボツボタン
        $rtn_st .= '</p>';
        // 
        $rtn_st .= '</div>';
        $rtn_st .= '</div>';
        $rtn_st .= '</div>';
        $cnt++;
      }
      $rtn_st .= '</div>';
      // ページャ
      $pager_url = str_replace("#", "", $pager_url);
      $rtn_pager_st = pager($page,$page_all_cnt,$page_size,$target_page,$pager_url);
      $rtn_st .= $rtn_pager_st;
      // ページャ
      return $rtn_st;
    }else
    {
      return  '検索結果はありません。';
    }
  }
  return "NG";
}
//////////////////////////////////
// タイル表示
//////////////////////////////////
function f_get_tile_page($db_conn,$page_size,$page,$target_page)
{
  //セレクト画像
  $page_all_cnt = f_get_page_all_cnt($db_conn,"SELECT","");
  $base_sql = "SELECT `id`, `sns_id`, `search_tag`, `user_id`, `screen_name`, `tweet_name`, `tweet_icon`, 
  `id_str`, `tweet_text`, `tweet_date`, `tweet_hashtag`, `tweet_img`, `tweet_img_short`, `tweet_img_display_url` ,`star_flg`,`del_flg`
  FROM `shub_contents`  WHERE star_flg = 1 AND del_flg <> 0 order by id_str desc ";
  $page = (!isset($page)) ? 1 : $page;
  $page_count = ceil($page_all_cnt / $page_size);
  $start = ($page - 1) * ($page_size);
  $end = ($page * $page_size - 1 < $page_all_cnt) ? $page * $page_size - 1 : $page_all_cnt;
  $page_sql = " limit $start ,$page_size ";
  $sql = $base_sql.$page_sql;
// echo "<br>sql=".$sql;
  $result = mysqli_query($db_conn,$sql);
  if(!$result)
  {
  }else
  {
    $numrows = mysqli_num_rows($result);
    if($numrows>0)
    {
      // 件数
      $cur_cnt = $page_size*$page;
      if($cur_cnt>$page_all_cnt)
      {
        $cur_cnt = $cur_cnt-$page_size+$numrows;
      }
      // 
      $from_cnt = $cur_cnt-$numrows+1;
      $rtn_st = '';
      $rtn_st .= '<div class="col-xs-12 col-md-12" >';
      $rtn_st .=   '<h5 class="col-md-8" style="margin-bottom: 20px;;font-weight:200;1px;text-shadow:1px1px 0 rgba(0,0,0,0.1);color:#fff;">'."\n";
      $rtn_st .= $search_tag_st."(".number_format($page_all_cnt)."点中".number_format($from_cnt)."-".number_format($cur_cnt)."点表示)".number_format($page)."/".number_format($page_count)."ページ";
      $rtn_st .= '</h5>';
      $rtn_st .= '</div>';
      $cnt = 1;
      while($link = mysqli_fetch_row($result))
     {
        list($id,$sns_id,$search_tag,$user_id,$screen_name,$tweet_name,$tweet_icon,$id_str,$tweet_text,$tweet_date,$tweet_hashtag,$tweet_img,$tweet_img_short,$tweet_img_display_url,$star_flg,$del_flg) = $link;
        // 
        $url ='https://twitter.com/search?q='.$screen_name.'" target="_blank"';
        // タブ用画像パス
        $img_path = $tweet_img;
        $url_ch = "detail.php?id=".$id;
        $rtn_st .= '<div class="col-xs-12 col-sm-6 col-md-4 col-lg-2" >';
        $rtn_st .= '<div class="thumbnail  ">';
        $rtn_st .= '<a href="'.$url_ch.'"><img  src="'.$img_path.'">';
        $rtn_st .= '</div>';
        $rtn_st .= '</div>';
      }
      $rtn_st .= '</div>';
      // ページャ
      $pager_url = str_replace("#", "", $pager_url);
      $rtn_pager_st = pager($page,$page_all_cnt,$page_size,$target_page,$pager_url);
      $rtn_st .= $rtn_pager_st;
      // ページャ
      return $rtn_st;
    }else
    {
      return  '検索結果はありません。';
    }
  }
  return "NG";
}
// ページャー
function pager($page,$page_all_cnt,$page_size,$target_page,$pager_url) 
{
  $rtn_st = "";
  $current_page = $page;     //現在のページ
  $total_rec = $page_all_cnt;    //総レコード数
  $page_rec   = $page_size;   //１ページに表示するレコード
  $total_page = ceil($total_rec / $page_rec); //総ページ数
  // $show_nav = 10;  //表示するナビゲーションの数
  // $target_page //ターゲットファイル名
  // $pager_url  //キーワードとか
  if (is_mobile())
  {
    $show_nav = 5; //モバイルなら5個
  }else{
    $show_nav = 10; //PCTなら10個
  }
  //全てのページ数が表示するページ数より小さい場合、総ページを表示する数にする
  if ($total_page < $show_nav) {
      $show_nav = $total_page;
  }
  //トータルページ数が2以下か、現在のページが総ページより大きい場合表示しない
  if ($total_page <= 1 || $total_page < $current_page ) return;
  //総ページの半分
  $show_navh = floor($show_nav / 2);
  //現在のページをナビゲーションの中心にする
  $loop_start = $current_page - $show_navh;
  $loop_end = $current_page + $show_navh;
  //現在のページが両端だったら端にくるようにする
  if ($loop_start <= 0) {
      $loop_start  = 1;
      $loop_end = $show_nav;
  }
  if ($loop_end > $total_page) {
      $loop_start  = $total_page - $show_nav +1;
      $loop_end =  $total_page;
  }
  // 
  $rtn_st .= '<nav class="text-center">';
  $rtn_st .= '<ul class="pagination pagination-sm">';
  $rtn_st .= '<li>';

  //最初のページ以外だったら「前へ」を表示
  if ( $current_page > 1) $rtn_st .= '<li><a href="'. $target_page.'?page=' . ($current_page-1).$pager_url.'" aria-label="前のページへ"><span aria-hidden="true">前へ</span></a></li>';
  //2ページ移行だったら「一番前へ」を表示
  if ( $current_page > 2) $rtn_st .= '<li><a href="' . $target_page.'?page=1'.$pager_url.'" aria-label="一番前のページへ"><span aria-hidden="true">1</span></a></li><li class="disabled"><span aria-hidden="true">･･･</span></li>';
  
  for ($i=$loop_start; $i<=$loop_end; $i++) 
  {
    if ($i > 0 && $total_page >= $i) 
    {
        if($i == $current_page) $rtn_st .= '<li class="active">';
        else $rtn_st .= '<li>';
        $rtn_st .= '<a href="' . $target_page.'?page='.$i.$pager_url.'">'.$i.'</a>';
        $rtn_st .= '</li>';
    }
  }
  
  //最後から２ページ前だったら「一番最後へ」を表示
  if ( $current_page < $total_page - 1) $rtn_st .= '<li class="disabled"><span aria-hidden="true">･･･</span></li><li class="next"><a href="'. $target_page.'?page=' . $total_page.$pager_url.'" aria-label="最後のページへ"><span aria-hidden="true">'.$total_page.'</span></a></li>';
  //最後のページ以外だったら「次へ」を表示
  if ( $current_page < $total_page) $rtn_st .= '<li class="next"><a href="'. $target_page.'?page=' . ($current_page+1).$pager_url.'" aria-label="次のページへ"><span aria-hidden="true">次へ</span></a></li>';
  $rtn_st .= '</ul>';
  $rtn_st .= '</nav>';
  // 
  return $rtn_st;
}
?>
