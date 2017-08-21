<?php
include_once '../setup.php';

$main="";
$op=(empty($_REQUEST['op']))?"":$_REQUEST['op'];
$book_sn=($_REQUEST['book_sn'])?intval($_REQUEST['book_sn']):"";
switch($op){
    case "save_rubric":
    save_rubric($book_sn);
    header("location:{$_SERVER['PHP_SELF']}?book_sn={$book_sn}");
    break;
    
    case "save_comment":
    save_comment();
    header("location:{$_SERVER['PHP_SELF']}?book_sn={$book_sn}");
    break;
    
    default:
    add_click();
    $content=show_book($book_sn);
}

echo $content;

//增加點閱率
function add_click($book_sn=""){
    global $link,$tblBook;
$sql="update `{$tblBook}` set book_click=book_click+1 where book_sn='{$book_sn}'";
mysql_query($sql,$link) or die_content("新增點閱失敗".mysql_error());
}

function show_book($book_sn=""){
    global $link,$tblBook,$cover_url,$tblPage;
$main="";
//取得書籍資料
$sql="select * from `{$tblBook}` where book_sn='{$book_sn}'";
//die($sql);
$result=mysql_query($sql,$link) or die_content("查詢資料失敗".mysql_error());
while($data=mysql_fetch_assoc($result)){
    foreach($data as $i=>$v){
        $$i=$v;
    }
}
$main.="
<div>
<h1>{$book_title}</h1>
<p>{$book_content}</p>
<img src={$cover_url}cover_{$book_sn}.jpg>
</div>
";

//取得所有頁面資料，並將頁面資料放在div標籤中。
$sql="select * from `{$tblPage}` where book_sn='{$book_sn}' order by page_sort";
$result=mysql_query($sql,$link) or die_content("查詢資料失敗".mysql_error());
while($data=mysql_fetch_assoc($result)){
    foreach($data as $i=>$v){
        $$i=$v;
    }
    $main.="<div>
    <h1>{$page_title}</h1>
    {$page_content}</div>";
}

$content="
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
<html>
    <head>
        <meta http-equiv='content-type' content='text/html; charset=utf-8'>
		<link href='booklet/jquery.booklet.1.3.1.css' type='text/css' rel='stylesheet' media='screen' />
		<link rel='stylesheet' href='css/style.css' type='text/css' media='screen'/>
		<link rel='stylesheet' href='http://code.jquery.com/ui/1.8.1/themes/sunny/jquery-ui.css' type='text/css' media='screen'/>
		<link rel='stylesheet' href='QapTcha/jquery/QapTcha.jquery.css' type='text/css' media='screen'/>
        <script type='text/javascript' src='https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js'></script>
		<script type='text/javascript' src='https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js'></script>
		<script src='booklet/jquery.easing.1.3.js' type='text/javascript'></script>
		<script src='booklet/jquery.booklet.1.3.1.min.js' type='text/javascript'></script>
		<script src='QapTcha/jquery/jquery.ui.touch.js' type='text/javascript'></script>
		<script src='QapTcha/jquery/QapTcha.jquery.js' type='text/javascript'></script>
        <script src='Highcharts/js/highcharts.js'></script>
        <script src='Highcharts/js/highcharts-more.js'></script>
        <script src='Highcharts/js/modules/exporting.js'></script>
    </head>
		<h1 class='title'>{$book_title}<div class='fb-like' data-send='true' data-width='450' data-show-faces='true' style='display:inline;'></div></h1>
        <p>點閱數：{$book_click}</p>
		<div class='book_wrapper'>
			<a id='next_page_button'></a>
			<a id='prev_page_button'></a>
			<div id='loading' class='loading'>Loading pages...</div>
			<div id='mybook' style='display:none;'>
				<div class='b-load'>
					{$main}
                    <div>
                    <img src='images/end.png' style='vertical-align:middle;text-align:center;' />
                    <h1>書籍內容結束</h1>
                    </div>
			</div>
            </div>
    <!--jQuery UI 頁籤介面開始-->
    <div id='tabs' style='pdding-bottom:20px;'>

        <ul>
            <li><a href='#tabs-1'>Facebook留言</a></li>
            <li><a href='#tabs-2'>系統留言</a></li>
            <li><a href='#tabs-3'>評分結果</a></li>
        </ul>
        <div id='tabs-1'>
    <!--facebook留言介面開始-->
<div class='fb-comments' data-href='"._WEB_ROOT_URL."MoleskineNotebook/index.php?book_sn={$book_sn}' data-num-posts='6' data-width='850'></div>
        </div>
    <!--facebook留言介面結束-->
        <div id='tabs-2'>
        ".commemt($book_sn)."
        </div>
        <div id='tabs-3'>
        ".rubric_form($book_sn)."
        </div>
    <!--jQuery UI 頁籤介面結束-->
        </div>
    <!--facebook引入檔案開始-->
    <div id='fb-root'></div>
    <script>(function(d, s, id) {
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) return;
      js = d.createElement(s); js.id = id;
      js.src = '//connect.facebook.net/zh_TW/all.js#xfbml=1&appId=211973985502246';
      fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
    </script>
    <!--facebook引入檔案結束-->
        <!-- The JavaScript -->
        <script type='text/javascript'>
			\$(function() {
            $('.QapTcha').QapTcha({
                txtLock:'未解鎖，不可以送出表單',
                txtUnlock:'已解鎖，可以送出表單',
                PHPfile : 'QapTcha/php/Qaptcha.jquery.php'
            });
            $('.jtable th').addClass('ui-state-default');
            $('.jtable td').addClass('ui-widget-content');
            $('.jtable tr').hover(
                function(){
                    $(this).children('td').addClass('ui-state-hover');},
                function(){
                    $(this).children('td').removeClass('ui-state-hover');
            });
             $('.jtable tr').click(function(){
                $(this).children('td').toggleClass('ui-state-highlight');
              })
                $( '#tabs' ).tabs();
				var \$mybook 		= \$('#mybook');
				var \$bttn_next		= \$('#next_page_button');
				var \$bttn_prev		= \$('#prev_page_button');
				var \$loading		= \$('#loading');
				var \$mybook_images	= \$mybook.find('img');
				var cnt_images		= \$mybook_images.length;
				var loaded			= 0;
				\$mybook_images.each(function(){
					var \$img 	= \$(this);
					var source	= \$img.attr('src');
					\$('<img/>').load(function(){
						++loaded;
						if(loaded == cnt_images){
							\$loading.hide();
							\$bttn_next.show();
							\$bttn_prev.show();
							\$mybook.show().booklet({
			name:'{$book_title}',
			width:800,height:500,speed:1000,direction:'LTR',
			startingPage:0,easing:'easeInOutQuad',easeIn:'easeInQuad',
			easeOut:'easeOutQuad',closed:true,closedFrontTitle:null,
			closedFrontChapter: null,closedBackTitle:null,
			closedBackChapter:null,covers:false,
			pagePadding:10,pageNumbers:true,hovers:false,overlays:true,tabs:false,            
			tabWidth:60,tabHeight:20,arrows:false,cursor:'pointer',hash:true,keyboard:true,             
			next:\$bttn_next,prev:\$bttn_prev,menu:null,pageSelector:false,chapterSelector:false,            
			shadows:true,shadowTopFwdWidth:166,              
			shadowTopBackWidth: 166,shadowBtmWidth:     50,
			before:             function(){},     
			after:              function(){}      
							});
						}
					}).attr('src',source);
				});
				
			});
        </script>
";
return $content;
}

function commemt($book_sn=""){
    global $link,$tblComment;
    $main="<table class='jtable' cellspacing=0 >
    <tr><th style='width:400px;'>留言內容</th><th>留言ip</th><th>留言時間</th></tr>
    ";
    $sql="select * from `{$tblComment}` where book_sn='{$book_sn}'";
    $result=mysql_query($sql,$link) or die_content("查詢資料失敗".mysql_error());
    while($data=mysql_fetch_assoc($result)){
        foreach($data as $i=>$v){
            $$i=$v;
        }
        $main.="<tr><td>{$comment_content}</td><td>{$comment_ip}</td><td>{$comment_time}</td></t>";
    }
    $main.="</table>";
    $main.="
    <div style='height:120px;'>
    <form method='post' action='{$_SERVER['PHP_SELF']}'>
     <input type='text' name='comment_content' size=80 style='font-size:1.2em;'>
     <input type='hidden' name='op' value='save_comment'>
     <input type='hidden' name='book_sn' value='{$book_sn}'>
     <input type='submit' value='儲存留言'><br>
     <div class='QapTcha'></div>
    </form>
    </div>
    ";
    return $main;
}

function save_comment(){
    global $link,$tblComment;
    if(empty($_POST['comment_content'])) return false;
    $now_date=date("Y-m-d H:i:s");
    if (!empty($_SERVER["HTTP_CLIENT_IP"])){
        $ip = $_SERVER["HTTP_CLIENT_IP"];
    }elseif(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])){
        $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
    }else{
        $ip = $_SERVER["REMOTE_ADDR"];
    }
    $sql="insert into `{$tblComment}` 
    (`book_sn`, `comment_content`, `comment_ip`, `comment_time`) values 
    ('{$_POST['book_sn']}','{$_POST['comment_content']}','{$ip}','{$now_date}')";
    mysql_query($sql,$link) or die_content("新增留言失敗".mysql_error());
}

function rubric_form($book_sn=""){
    global $link,$tblRubric,$rubric_desc_arr,$rubric_arr,$tblBook,$rubric_arr_nokey;
    //取得書籍資料
    $sql="select * from `{$tblBook}` where book_sn='{$book_sn}'";
    //die($sql);
    $result=mysql_query($sql,$link) or die_content("查詢資料失敗".mysql_error());
    while($data=mysql_fetch_assoc($result)){
        foreach($data as $i=>$v){
            $$i=$v;
        }
    }
    //取得量表分數的json
    $val=array();
    foreach($rubric_arr as $i=>$v){
        $sql="select AVG(rubric_val) as avg,rubric_type from `{$tblRubric}` where book_sn='{$book_sn}' AND rubric_type='{$i}'";
        $result=mysql_query($sql,$link) or die_content("查詢資料失敗".mysql_error());
        while($data=mysql_fetch_assoc($result)){
            foreach($data as $i=>$v){
                $$i=$v;
            }
            $val[]=floatval($avg) ;
        }
        
    }
    $val_json=json_encode($val);
    $main="
		<script type='text/javascript'>
$(function () {
    var chart;
    var polar;
    var rubric =new Array();
        rubric[0]='主題與內容';
        rubric[72]='遣詞與造句';
        rubric[144]='情意與表達';
        rubric[216]='美觀和表現';
        rubric[288]='媒體與技術';
    $(document).ready(function() {
        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'container',
                type: 'bar'
            },
            title: {
                text: '{$book_title}評分直條圖'
            },
            subtitle: {
                text: 'aandd booklet'
            },
            xAxis: {
                categories: ".json_encode($rubric_arr_nokey).",
            },
            yAxis: {
                min: 0,
                title: {
                    text: '量表分數'
                },
                labels: {
                    overflow: 'justify'
                }
            },
            plotOptions: {
                bar: {
                    dataLabels: {
                        enabled: true
                    }
                }
            },
            legend: {
                layout: 'vertical',
                align: 'left',
                verticalAlign: 'bottom',
                x: 0,
                y: 5,
                floating: true,
                borderWidth: 1,
                backgroundColor: '#FFFFFF',
                shadow: true
            },
            credits: {
                enabled: false
            },
            series: [{
                name: ' ',
                data: {$val_json}
            }]
        });
    });
    polar = new Highcharts.Chart({
	    chart: {
	        renderTo: 'polar',
	        polar: true
	    },
	    title: {
	        text: '{$book_title}極座標圖'
	    },
	    pane: {
	        startAngle: 0,
	        endAngle: 360
	    },
	    xAxis: {
	        tickInterval: 72,
	        min: 0,
	        max: 360,
	        labels: {
	        	formatter: function () {
	        		return rubric[this.value];
	        	}
	        }
	    },
	    yAxis: {
	        min: 0,
            max:5
	    },
        tooltip:{
        	formatter: function () {
        		var tip='<b>' + rubric[this.x] + '</b><br/>平均分數：' +  this.y ;
                return tip;
        	}
        },
	    plotOptions: {
	        series: {
	            pointStart: 0,
	            pointInterval: 72
	        }
	    },
	    series: [{
	        type: 'line',
	        name: '平均分數',
	        data: {$val_json}
	    }]
	});
    
});
		</script>
    ";
    $main.="<form action='{$_SERVER['PHP_SELF']}' method='post' style='height:200px;'>";
    foreach($rubric_arr as $i =>$v){
        $main.="<span title='{$rubric_desc_arr[$i]}'>{$v}：</span>";
        for($n=1;$n<=5;$n++){
            $main.="{$n}<input type='radio' name='rubric_type[{$i}]' value='{$n}'>";
        }
        $main.="<br>";
    }
    $main.="
        <input type='hidden' name='op' value='save_rubric'>
        <input type='hidden' name='book_sn' value='{$book_sn}'>
        <input type='submit' value='儲存評分'><br>
        <div class='QapTcha'></div>
    ";
    $main.="</form>";
    $total_content="
    <table>
    <tr><td>{$main}</td><td>
    <div id='container' style='min-width: 300px; height: 300px; margin: 0 auto'></div>
    </td><td>
    <div id='polar' style='min-width: 300px; height: 300px; margin: 0 auto;'></div>
    </td></tr>
    </table>
    ";
    return $total_content;
}

function save_rubric($book_sn=""){
    global $link,$tblRubric,$rubric_desc_arr,$rubric_arr;
    foreach($_POST['rubric_type'] as $i => $v){
        $sql="insert into `{$tblRubric}` (`book_sn`, `rubric_type`, `rubric_val`) values 
            ('{$_POST['book_sn']}','{$i}','{$v}')";
        mysql_query($sql,$link) or die_content("新增評分失敗".mysql_error());
    }
}
?>