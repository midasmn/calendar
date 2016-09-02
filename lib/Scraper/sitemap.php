<?php

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

// $domain	= "http://icalendar.xyz/";;

////////////////////////////////////////
//静的ファイルサイトマップ
// out 除外PHPファイル名
// stat_name xmlファイル名
////////////////////////////////////////
function f_stat_sitemap()
{
	$base_dir = "/usr/share/nginx/html/icalendar.xyz/";
	$baseurl = 'http://icalendar.xyz/';
	$filename = $base_dir.'statsitemap.xml';
	$arr_stat_php  = array("smart","newer","random","sitemap","about","terms","faq","privacy","supportform");//静的PHP
	if (FALSE == ($fp = fopen($filename, "w"))){
		print "サイトマップを作成に失敗。ディレクトリのパーミッションを見直してください";
		exit();
	}
	//
	$time = date("c");
  	$temp ='<?xml version="1.0" encoding="UTF-8"?>'."\n";
  	$temp .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
  	//
  	foreach ($arr_stat_php as $key => $value)
  	{
  		$temp .= '<url>'."\n";
	    $temp .= '<loc>'.$baseurl.$value.'</loc>'."\n";
	    $temp .= '<lastmod>'.$time.'</lastmod>'."\n";
	    $temp .= '<changefreq>weekly</changefreq>'."\n";
	    $temp .= '<priority>0.5</priority>'."\n";
	  	$temp .= '</url>'."\n";
	  	
	}
	$temp .= '</urlset>'."\n";
	fputs($fp,$temp);
	fclose($fp);
echo "<br>stat";
	return "END";
}
////////////////////////////////////////
////////////////////////////////////////
function f_cal_sitemap($db_conn)
{
	//googleサイトマップ制限 URL50000,50MB 500ファイル
	$base_dir = "/usr/share/nginx/html/icalendar.xyz/";
	$calsitemap = "calsitemap";
	$baseurl = 'http://icalendar.xyz/calendar/';
	$max_url = 50000;	//1ファイルURL制限
	$max_filecnt = 500;	//xmlファイル数制限
	$urlcnt = 1;	//url数初期値
	$filecnt = 0;	//xmlファイル数初期値
	//
	$sql = "SELECT `id` FROM `tbl_calendar` WHERE `onflg` = 'ON' order by `order` desc";
	$result = mysql_query($sql, $db_conn);
	$rows = mysql_num_rows($result);
	while($link = mysql_fetch_row($result))
	{
		list($calendarid) = $link;	
		if($urlcnt<=$max_url)	//5000URL制限
		{
			if($urlcnt==1)
			{
				////xmlファイル500制限まで
				if($filecnt<$max_filecnt)
				{
					//ファイル作成
					$filecnt++;	//xmlファイル数
					$filename = $base_dir.$calsitemap.$filecnt.'.xml';
					if (FALSE == ($fp = fopen($filename, "w")))
					{
						echo "<br>filename".$filename;
						print "サイトマップを作成に失敗。ディレクトリのパーミッションを見直してください";
						exit();
					}else{
						echo "<br>filecnt=".$filecnt;
					}
				}else{
					//終了
					exit();
				}
  				$temp ='<?xml version="1.0" encoding="UTF-8"?>'."\n";
  				$temp .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
			}
			$time = date("c");
			$url = $baseurl.$calendarid;
	  		$temp .= '<url>'."\n";
		    $temp .= '<loc>'.$url.'</loc>'."\n";
		    $temp .= '<lastmod>'.$time.'</lastmod>'."\n";
		    $temp .= '<changefreq>weekly</changefreq>'."\n";
		    $temp .= '<priority>0.5</priority>'."\n";
		  	$temp .= '</url>'."\n";
		  	$urlcnt++;

		}
		if($urlcnt==$max_url)
		{
			$temp .= '</urlset>'."\n";
			fputs($fp,$temp);
			fclose($fp);
			$urlcnt = 1;
			// $filecnt++;
			$temp ="";
		}	
	}
	$temp .= '</urlset>'."\n";
	fputs($fp,$temp);
	fclose($fp);

	return $filecnt;
}
////////////////////////////////////////
function f_day_sitemap($db_conn)
{
	//googleサイトマップ制限 URL50000,50MB 500ファイル
	$base_dir = "/usr/share/nginx/html/icalendar.xyz/";
	$dailysitemapname = "dailysitemap";
	$baseurl = 'http://icalendar.xyz/daylist/';
	$max_url = 50000;	//1ファイルURL制限
	$max_filecnt = 500;	//xmlファイル数制限
	$urlcnt = 1;	//url数初期値
	$filecnt = 0;	//xmlファイル数初期値
	//
	$sql = "SELECT `tbl_ymd`.`calendar_id`, `tbl_ymd`.`yyyy`, `tbl_ymd`.`mm`, `tbl_ymd`.`dd` FROM `tbl_ymd`,`tbl_calendar` WHERE `tbl_calendar`.`id` = `tbl_ymd`.`calendar_id` and `tbl_calendar`.`onflg` = 'ON' and `tbl_ymd`.`yyyy` <> 9999 and `tbl_ymd`.`order` = 1 order by `tbl_ymd`.`dd` desc";
	$result = mysql_query($sql, $db_conn);
	$rows = mysql_num_rows($result);
	while($link = mysql_fetch_row($result))
	{
		list($calendarid,$yyyy,$mm,$dd) = $link;	
		if($urlcnt<=$max_url)	//5000URL制限
		{
			if($urlcnt==1)
			{
				////xmlファイル500制限まで
				if($filecnt<$max_filecnt)
				{
					//ファイル作成
					$filecnt++;	//xmlファイル数
					$filename = $base_dir.$dailysitemapname.$filecnt.'.xml';
					if (FALSE == ($fp = fopen($filename, "w")))
					{
						echo "<br>filename".$filename;
						print "サイトマップを作成に失敗。ディレクトリのパーミッションを見直してください";
						exit();
					}else{
						echo "<br>filecnt=".$filecnt;
					}
				}else{
					//終了
					exit();
				}
  				$temp ='<?xml version="1.0" encoding="UTF-8"?>'."\n";
  				$temp .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
			}
			$time = date("c");
			$url = $baseurl.$calendarid.'/'.$yyyy.'/'.$mm.'/'.$dd;
	  		$temp .= '<url>'."\n";
		    $temp .= '<loc>'.$url.'</loc>'."\n";
		    $temp .= '<lastmod>'.$time.'</lastmod>'."\n";
		    $temp .= '<changefreq>weekly</changefreq>'."\n";
		    $temp .= '<priority>0.5</priority>'."\n";
		  	$temp .= '</url>'."\n";
		  	$urlcnt++;

		}
		if($urlcnt==$max_url)
		{
			$temp .= '</urlset>'."\n";
			fputs($fp,$temp);
			fclose($fp);
			$urlcnt = 1;
			// $filecnt++;
			$temp ="";
		}	
	}
	$temp .= '</urlset>'."\n";
	fputs($fp,$temp);
	fclose($fp);

	return $filecnt;
}
////////////////////
function f_sitemap_write($domain,$cal_cnt,$day_cnt)
{
	$statxml = 'http://icalendar.xyz/statsitemap.xml';	//静的ページXML
	$calxml =	'http://icalendar.xyz/calsitemap';	//カレンダーページXML
	$dayxml = 'http://icalendar.xyz/dailysitemap';	//日付base	
	//
	$base_dir = "/usr/share/nginx/html/icalendar.xyz/";
	$filename = $base_dir.'sitemap.xml';
	if (FALSE == ($fp = fopen($filename, "w"))){
		print "サイトマップを作成に失敗。ディレクトリのパーミッションを見直してください";
		exit();
	}
	$time = date("c");
	$tmp = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
	$tmp .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
	//静的XMLサイトマップ
	$tmp .= '<sitemap>'."\n";
	$tmp .= '<loc>'.$statxml.'</loc>'."\n";
	$tmp .= '<lastmod>'.$time.'</lastmod>'."\n";
	$tmp .= '</sitemap>'."\n";
	//calsitemap
	for($c=1;$c<=$cal_cnt;$c++)
	{
		$tmp .= '<sitemap>'."\n";
		$tmp .= '<loc>'.$calxml.$c.'.xml</loc>'."\n";
		$tmp .= '<lastmod>'.$time.'</lastmod>'."\n";
		$tmp .= '</sitemap>'."\n";
	}
	//daysitemap
	for($d=1;$d<=$day_cnt;$d++)
	{
		$tmp .= '<sitemap>'."\n";
		$tmp .= '<loc>'.$dayxml.$d.'.xml</loc>'."\n";
		$tmp .= '<lastmod>'.$time.'</lastmod>'."\n";
		$tmp .= '</sitemap>'."\n";
	}
	$tmp .= '</sitemapindex>';
	fputs($fp,$tmp);
	fclose($fp);
}




//静的サイトマップ作成
$rtn = f_stat_sitemap();
//
$cal_cnt = f_cal_sitemap($db_conn);
//DBサイトマップ作成
$day_cnt = f_day_sitemap($db_conn);
//サイトマップ作成
$rtn = f_sitemap_write($domain,$cal_cnt,$day_cnt);
//
echo $rtn_cnt;

?>