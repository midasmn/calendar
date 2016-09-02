<?php
require_once("lib/lib.php");
require_once("lib/mysql-ini.php");
// require_once("lib/twitter.php");

$page = isset($_GET['page']) ? $_GET['page'] : 1;
$target_page = isset($_GET['target_page']) ? $_GET['target_page'] : 'index.php';

if (is_mobile())
{
  $page_size = 10;
}else{
  $page_size = 30;
}

// データベースに接続
$db_conn = new mysqli($host, $user, $pass, $dbname)
or die("データベースとの接続に失敗しました");
$db_conn->set_charset('utf8');
////////////////////////////////////
$lang = "jp";
////////////////////////////////////
if ( isset($_REQUEST['language_id']) ) {
  $language_id = (int)$_REQUEST['language_id'];
} else {
  $language_id= 9; // 日本
}
$mode=$_POST['mode'];
if(!$mode){
  $mode=$_GET['mode'];
}
$search_tag=$_POST['search_tag'];
if(!$search_tag){
  $search_tag=$_GET['search_tag'];
}

if($mode=="search_tag"||$page)
{
  if($search_tag)
  {
    $search_tag = f_twitter_tag($db_conn,$search_tag);
    $sns_id = 1;
    $rtn_ifream_st = f_get_img_page($db_conn,$page_size,$page,$target_page,$search_tag);
  }
}

if($search_tag){
  $title = "ソーシャルハブ | ".$search_tag;
}else{
  $title = "ソーシャルハブ";
}

$keywords = "";
$description = "";
$h1_index = ($search_tag=="" ? '<i class="fa fa-search" aria-hidden="true"></i>タグ検索':'<i class="fa fa-search" aria-hidden="true"></i>'.$search_tag.':タグ検索');
$h2_index = $description;

$description .= '('.date(Y).'-'.date(m).'-'.date(d).':'.$tgcnt.')';
$site_name = $title;
//
$og_title = $title;
$og_image = (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
$og_url = (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
$og_site_name = $title;
$og_description = $description;
$h1_st = $title;
$h1_st_s = "  ";
$culhtml = "https://faceapglezon.info/socialhub/";
$crlhtmltitle = $title;
$footer_sitename = $title;
$itemprop_name = $title;
$itemprop_description = $description;
$itemprop_author = "https://faceapglezon.info/socialhub/";
// //
$fb_app_id = 557991774408353;
$article_publisher = "https://www.facebook.com/faceapglezon";
// //
$twitter_site = "@FaceApGleZon";
$sns_url = "http://".$_SERVER["HTTP_HOST"].htmlspecialchars($_SERVER["PHP_SELF"]);

$rtn_tab2 = "favicon-192x192.png";
?>
<?php require('header.php');?>
<body>
<div id="wrap">
<?php require('menu.php');?>
<!-- ページのコンテンツすべてをwrapする（フッター以外） -->
  <div class="container"  style="margin-top: 1px;">




    <div class="page-header">
      <h3></h3>
        <div class="btn-group">
          <button class="btn" data-calendar-nav="prev"><<</button>
          <button class="btn" data-calendar-nav="today">[Today]</button>
          <button class="btn" data-calendar-nav="next">>></button>
        </div>
        <div class="btn-group">
          <button class="btn" data-calendar-view="year">Year</button>
          <button class="btn" data-calendar-view="month">Month</button>
          <button class="btn" data-calendar-view="week">Week</button>
          <button class="btn" data-calendar-view="day">Day</button>
        </div>
    </div>



    <div id="calendar"></div>








      <!-- ページトップへ -->
      <a href="" class="btn btn-default pull-right" id="page-top">
        <i class="fa fa-angle-up fa-fw"></i>
      </a>

  </div><!-- .container -->
</div><!-- .wrap -->
<?php require('footer.php');?>
<script type="text/javascript">
(function($) {

  "use strict";
  var events = {
            "id": 293,                    // id
            "title": "Event 1",           // タイトル
            "url": "http://kwski.net/jquery/1222",  // リンク
            "class": "event-important",   // class
            "start": 1412125748000,       // 開始日時(ミリ秒まで)
            "end": 1412989748000          // 終了日時(ミリ秒まで)
        };
  var options = {
    tmpl_path: 'https://dl.dropboxusercontent.com/u/59384927/jquery/CALENDAR/bootstrap-calendar/0.2.4/tmpls/',
    events_source: function () { return [events]; },
    language: 'ja-JP',
    onAfterViewLoad: function(view) {
      $('.page-header h3').text(this.getTitle());
      $('button[data-calendar-view="' + view + '"]').addClass('active');
    },
  };

  var calendar = $('#calendar').calendar(options);

  $('.btn-group button[data-calendar-nav]').each(function() {
    var $this = $(this);
    $this.click(function() {
      calendar.navigate($this.data('calendar-nav'));
    });
  });

  $('.btn-group button[data-calendar-view]').each(function() {
    var $this = $(this);
    $this.click(function() {
      calendar.view($this.data('calendar-view'));
    });
  });

}(jQuery));
</script> 
</body>
</html>
