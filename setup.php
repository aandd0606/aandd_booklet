<?php
//系統基本資料
define("_WEB_ROOT_URL","http://{$_SERVER['SERVER_NAME']}/booklet/");
define("_WEB_ROOT_PATH","{$_SERVER['DOCUMENT_ROOT']}/booklet/");

//系統變數
$title="網路地圖相簿";
$page_menu=array(
    "首頁"=>"index.php",
    "新增翻頁電子書"=>"book_admin.php",
    "管理未開放閱讀電子書"=>"index.php?op=no_show_list"
    );
$tblBook="book";
$tblPage="page";
$tblComment="comment";
$tblRubric="rubric";

$yorn_arr=array(
    "yes"=>"開放",
    "no"=>"不開放"
);
$rubric_arr=array(
    "content"=>"主題與內容",
    "statement"=>"遣詞與造句",
    "article"=>"情意與表達",
    "layout"=>"美觀和表現",
    "media"=>"媒體與技術"
);
$rubric_arr_nokey=array("主題與內容","遣詞與造句","情意與表達","美觀和表現","媒體與技術");

$rubric_desc_arr=array(
    "content"=>"內容週延精緻，主題鮮明，主題發展布局流暢。",
    "statement"=>"句子結構合理，且銜接流利有助於觀念的連結。",
    "article"=>"文章真誠具說服力，能藉由文字間感受作者的存在。",
    "layout"=>"整體書籍佈局清楚、層次脈絡分明；設計主軸鮮明，風格貫穿全局；標題和字型編排出眾，有助閱讀和導覽。",
    "media"=>"書籍多媒體呈現各種元素都經過最佳化處理，能適切地使用各種高品質的多媒體資源。"
);

$cover_path=_WEB_ROOT_PATH."cover/";
$cover_url=_WEB_ROOT_URL."cover/";
$img_url=_WEB_ROOT_URL."img/";
//資料庫連線
$db_id="booklet";//資料庫使用者//
$db_passwd="123456";//資料庫使用者密碼//
$db_name="booklet";//資料庫名稱//
//動態產生導覽列
$top_nav=dy_nav($page_menu);

//連入資料庫
$link=@mysql_connect("localhost",$db_id,$db_passwd) or die_content("資料庫無法連線");
if(!mysql_select_db($db_name)) die_content("無法選擇資料庫".mysql_error());

//設定資料庫編碼
mysql_query("SET NAMES 'utf8'");

//自定輸出錯誤訊息
function die_content($content=""){
    $main="
        <!DOCTYPE html>
        <html lang='zh_TW'>
        <head>
        <meta charset='utf-8'>
        <title>輸出錯誤訊息</title>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <meta name='description' content='輸出錯誤訊息'>
        <meta name='author' content='aandd'>
        
        <link href='css/bootstrap.css' rel='stylesheet'>
        <link href='css/bootstrap-responsive.css' rel='stylesheet'>        
        <link href='css/jquery-ui-1.8.23.custom.css' rel='stylesheet'>
       
        <!-- 引入js檔案開始 -->
        <script src='js/jquery1.8.js'></script>
        <script src='js/jquery-ui-1.8.23.custom.min.js'></script>
        <script src='js/bootstrap.js'></script>
        <!-- 引入js檔案結束 -->
        </head>
        <body>
        <!--放入網頁主體-->
        <div class='container' id='main_content'>
          <!-- 主要內容欄位開始 -->
            <div class='hero-unit'>
            <div class='alert alert-error'>
            <a class='close' data-dismiss='alert'>×</a>
            <strong>{$content}</strong>
            </div>
            </div>
          
          <!-- 主要內容欄位結束 -->
          <!-- 頁腳開始 -->
          <footer>
          </footer>
          <!-- 頁腳結束 -->
        </div> 
        <!-- 主要內容欄位結束 -->
        </body>
        </html>
    ";
    die($main);
}

//產生動態導覽列
function dy_nav($page_menu=array()){
    $main="
      <!--導覽列開始  navbar-fixed-bottom 固定在下方  navbar-fixed-top在上方-->
      <div class='navbar navbar-fixed-top'>
      <div class='navbar-inner'>
      <div class='container'>
      <a class='btn btn-navbar' data-toggle='collapse' data-target='.nav-collapse'>
        <span class='icon-bar'></span>
        <span class='icon-bar'></span>
        <span class='icon-bar'></span>
      </a>
        <a class='brand'>翻頁電子書</a>
      <div class='nav-collapse'>
      <ul class='nav'>
    ";
    //$file_name=basename($_SERVER['PHP_SELF']);
    $file_name=basename($_SERVER['REQUEST_URI']);
    foreach($page_menu as $i=>$v){
        $class=($file_name==$v)?"class='active'":"";
        $main.="<li {$class}><a href='{$v}'>{$i}</a></li>";
    }
    $main.="
      </ul>
      </div>
      </div>
      </div>
      </div>
      <!--導覽列結束-->
    ";
    return $main;
}

function bootstrap($content="",$js_link="",$css_link="",$js_fun=""){
    global $top_nav,$title;
    $main="
        <!DOCTYPE html>
        <html lang='zh_TW'>
        <head>
        <meta charset='utf-8'>
        <title>{$title}</title>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <meta name='description' content='{$title}'>
        <meta name='author' content='aandd'>
        <link href='css/bootstrap.css' rel='stylesheet'>
        <link href='css/bootstrap-responsive.css' rel='stylesheet'>        
        <link href='css/jquery-ui-1.8.23.custom.css' rel='stylesheet'>
        <style type='text/css'>
          body {
            padding-top: 60px;
            padding-bottom: 20px;
          }
        </style>
        <!-- 引入js檔案開始 -->
        <script src='js/jquery1.8.js'></script>
        <script src='js/jquery-ui-1.8.23.custom.min.js'></script>
        <script src='js/bootstrap.js'></script>
        <!-- 引入js檔案結束 -->
        <!--引入額外的css檔案以及js檔案開始-->
        {$js_link}
        {$css_link}
        <!--引入額外的css檔案以及js檔案結束-->
        <!--jquery語法開始-->
        {$js_fun}
        <!--jquery語法結束-->
        </head>
        <body>
        <!--放入網頁主體-->
        <div class='container' id='main_content'>
          <!-- 主要內容欄位開始 -->
          {$top_nav}
          {$content}
          <!-- 主要內容欄位結束 -->
          <!-- 頁腳開始 -->
          <footer>
          </footer>
          <!-- 頁腳結束 -->
        </div> 
        <!-- 主要內容欄位結束 -->
        </body>
        </html>
    ";
    return $main;
}
?>