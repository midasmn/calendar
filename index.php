<?php
require_once("lib/lib.php");
require_once("lib/mysql-ini.php");
require_once("lib/twitter.php");

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
    <div class="row" >
        <!-- タイトル -->
        <div class="col-md-12" style="margin-top: 15px;text-align: center;">
          <h1 class="h3 " style="color:white;text-transform: none; ">
            <?php echo $h1_index; ?>
          </h1>
          <h2 class="h5" style = "color:#A6A6A6;">
            <?php echo $h2_index; ?>
          </h2>
        </div>
        <!-- .タイトル -->
    </div>

        <!-- 検索窓 -->
    <div class="bootsnipp-search" style="margin-top: 20px;">
      <div class="container">
        <form  id="exm-form" method="post" name="form" action="index.php">

          <div class="input-group">
            <input type="hidden" name="mode" value="search_tag">
            <input type="text" class="form-control" id="q" name="search_tag" value="<?php echo $search_tag; ?>" placeholder="#ハッシュタグを入力">
              <span class="input-group-btn">
                <button class="btn btn-danger" type="submit" style="border: 1px solid #fff;">
                  <span class="glyphicon glyphicon-search"></span>
                </button>
              </span>
          </div>

        </form>  
      </div>
    </div>
    <!-- .検索窓 -->


    <div class="row col-md-12 " style="margin-top: 20px;">
      <?php echo $rtn_ifream_st; ?>
    </div>

    <!-- <iframe src="http://pixabay.com/" frameborder="1" width="600" height="600" style="width:100%;"></iframe> -->
    <!-- <iframe src="http://pixabay.com/" frameborder="1" width="600" height="600" style="zoom:0.55"></iframe> -->

    <?php
    if($search_tag)
    {

    }else
    {
    ?>
    <!-- 使いかた -->
    <div id="howto" class="hidden-xs col-md-12" style="margin-top: 10px;text-align: center;">
      <div id="howto" class="col-xs-12 col-md-12" style="margin-top: 10px;text-align: center;">
        <h2 class="h3" style="color:white;">ソーシャルハブの使いかた</h2>
        <div class="row" style="margin-top: 20px;">
          <div class="col-xs-12 col-sm-4" style="text-align: center;">
            <ul>
              <li class="icon">
                <i class="fa fa-search"  style="font-size:50px;"></i>
              </li>
              <li class="title">#タグ（キーワード）検索</li>
              <li class="text">
                <p>#は入力不要です</p>
              </li>
            </ul>
          </div>

          <div class="col-xs-12 col-sm-4" style="text-align: center;">
            <ul>
              <li class="icon">
                <i class="fa fa-caret-square-o-left" style="font-size:20px;"></i>
                <i class="fa fa-picture-o" style="font-size:50px;"></i>
                <i class="fa fa-caret-square-o-right" style="font-size:20px;"></i>
              </li>
              <li class="title">検索結果から画像を選択</li>
                <li class="text">
                  <p>ランディングページURLなど設定</p>
                </li>
            </ul>
          </div>

          <div class="col-xs-12 col-sm-4" style="text-align: center;">
            <ul>
              <li class="icon">
                <i class="fa fa-caret-square-o-left" style="font-size:20px;"></i>
                <i class="fa fa-picture-o" style="font-size:50px;"></i>
                <i class="fa fa-caret-square-o-right" style="font-size:20px;"></i>
              </li>
              <li class="title">検索結果から画像を選択</li>
              <li class="text">
                <p>チェックボックスを選択します</p>
              </li>
            </ul>
          </div>
        </div>
    </div>
    <?php
    }
    ?>

      <!-- ページトップへ -->
      <a href="" class="btn btn-default pull-right" id="page-top">
        <i class="fa fa-angle-up fa-fw"></i>
      </a>

    </div><!-- .row -->
  </div><!-- .container -->
</div><!-- .wrap -->

<?php require('footer.php');?>
<script>
$(document).ready(function() {
    $('[data-toggle="popover"]').popover();
  $(".delbtn").click(function(){
    var id = $(this).data('id');
    $.post('/socialhub/ajax_exm.php', {
        id: id,
        exm: 'del'
      }, function(rs) {
      $(".deltggle_"+id).toggle();
    });
    });
  $(".heartbtn").click(function(){
    var id = $(this).data('id');
    $.post('/socialhub/ajax_exm.php', {
        id : id,
        exm: 'heart'
      }, function(rs) {
        if ($(".hearttggle_"+id).hasClass('btn-success')) {
          $(".hearttggle_"+id).removeClass("btn-success").addClass("btn-default");
          $(".startggle_"+id).removeClass("btn-primary").addClass("btn-default");
        } else {
          $(".hearttggle_"+id).removeClass("btn-default").addClass("btn-success");
          $(".startggle_"+id).removeClass("btn-primary").addClass("btn-default");
        }
    });
    });
  $(".starbtn").click(function(){
    var id = $(this).data('id');
    $.post('/socialhub/ajax_exm.php', {
        id: id,
        exm: 'star'
      }, function(rs) {
        if ($(".startggle_"+id).hasClass('btn-primary')) {
          $(".hearttggle_"+id).removeClass("btn-success").addClass("btn-default");
          $(".startggle_"+id).removeClass("btn-primary").addClass("btn-default");
        } else {
          $(".hearttggle_"+id).removeClass("btn-success").addClass("btn-default");
          $(".startggle_"+id).removeClass("btn-default").addClass("btn-primary");
        }
    });
    });
});
</script>
<?php if($language_id) :?>
  <script>// Let's call it 2 times just for fun...
// Show full page Loading Overlay
$.LoadingOverlay("show");
// Hide it after 3 seconds
setTimeout(function(){
    $.LoadingOverlay("hide");
}, 1000);
</script>
<?php endif; ?>
</body>
</html>
