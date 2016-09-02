<?php
$menu_tab1_st = "タグ検索";
$menu_tab2_st = "画像一覧";
$menu_tab3_st = "タグ一覧";
$menu_tab4_st = "セレクト一覧";
$menu_tab5_st = "キープ一覧";
$menu_tab6_st = "ボツ一覧";
$menu_tab7_st = "公開画像一覧";

$contents_cnt = f_get_comon_item($db_conn,'shub_cnt','cnt','flg_name','CONT');
$tag_cnt = f_get_comon_item($db_conn,'shub_cnt','cnt','flg_name','TAG');
$select_cnt = f_get_comon_item($db_conn,'shub_cnt','cnt','flg_name','SELECT');
$keep_cnt = f_get_comon_item($db_conn,'shub_cnt','cnt','flg_name','KEEP');
$trash_cnt = f_get_comon_item($db_conn,'shub_cnt','cnt','flg_name','TRASH');
  
$html_page = basename($_SERVER['PHP_SELF']);
switch ($html_page) {
  case 'index.php':
    $index_st = ' class="active" ' ;
    break;
  case 'list.php':
    $list_st =  ' class="active" ';
    break;
  case 'taglist.php':
    $taglist_st =  ' class="active" ';
    break;
  case 'select.php':
    $select_st =  ' class="active" ';
    break;
  case 'keep.php':
    $keep_st =  ' class="active" ';
    break;
  case 'trash.php':
    $trash_st =  ' class="active" ';
    break;
  case 'image.php':
    $image_st =  ' class="active" ';
    break;
}
?>
<nav class="navbar navbar-default">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbarEexample4">
        <span class="sr-only">Toggle navigation</span>
        メニュー
      </button>
      <a class="navbar-brand" href="/socialhub/">
        <!-- <img alt="Brand" src="/apple-touch-icon-57x57.png" style="height: 24px;"> -->
        ソーシャルハブ
      </a>
    </div>
    <div class="collapse navbar-collapse" id="navbarEexample4">
      <ul class="nav navbar-nav">
        <li  <?php echo $index_st;?>>
          <a href="index.php"><i class="fa fa-search" aria-hidden="true"></i><?php echo $menu_tab1_st;?></a>
        </li>
        <li  <?php echo $list_st;?>>
          <a href="list.php"><i class="fa fa-picture-o" aria-hidden="true"></i><?php echo $menu_tab2_st;?> <span class="badge"> <?php echo number_format($contents_cnt);?></span></a>
        </li>
        <li  <?php echo $taglist_st;?>>
          <a href="taglist.php"><i class="fa fa-tags" aria-hidden="true"></i><?php echo $menu_tab3_st;?> <span class="badge"><?php echo number_format($tag_cnt);?></span></a>
        </li>
        <li  <?php echo $select_st;?>>
          <a href="select.php"><i class="fa fa-heart" aria-hidden="true"></i><?php echo $menu_tab4_st;?> <span class="badge"><?php echo number_format($select_cnt);?></span></a>
        </li>
        <li  <?php echo $keep_st;?>>
          <a href="keep.php"><i class="fa fa-star" aria-hidden="true"></i><?php echo $menu_tab5_st;?> <span class="badge"><?php echo number_format($keep_cnt);?></span></a>
        </li>
        <li  <?php echo $trash_st;?>>
          <a href="trash.php"><i class="fa fa-trash" aria-hidden="true"></i><?php echo $menu_tab6_st;?> <span class="badge"><?php echo number_format($trash_cnt);?></span></a>
        </li>
        <li  <?php echo $image_st;?>>
          <a href="image.php"><i class="fa fa-birthday-cake" aria-hidden="true"></i><?php echo $menu_tab7_st;?> <span class="badge"><?php echo number_format($select_cnt);?></span></a>
        </li>
      </ul>
      <!-- 右メニュー -->
      <ul class="nav navbar-nav navbar-right hidden-xs hidden-sm hidden-md">
        <li class="dropdown ">
          <a href="" class="dropdown-toggle" data-toggle="dropdown">
            <i class="fa fa-cog" aria-hidden="true"></i>管理 <b class="caret"></b>
          </a>
          <ul class="dropdown-menu">
          
            <li class="">
              <a href="search-advanced.php">
                <i class="fa fa-search" aria-hidden="true"></i>高度な検索
              </a>
            </li>
            <li class="divider"></li>
            <li class="">
              <a href="tag_cron.php">
                <i class="fa fa-cogs" aria-hidden="true"></i>クーロン設定
              </a>
            </li>
          </ul>
        </li>
      </ul>
      <!-- 右メニュー -->
    </div>
  </div>
</nav>