<?php
include_once("setup.php");

//-------------------設定區-----------------------//
$op=(empty($_REQUEST['op']))?"":$_REQUEST['op'];
$book_sn=(empty($_REQUEST['book_sn']))?"":$_REQUEST['book_sn'];
$page_sn=(empty($_REQUEST['page_sn']))?"":$_REQUEST['page_sn'];

//---------------流程控制區----------------------//
switch($op){
    //ajax儲存頁面排序
    case "save_page_sort":
    $content=save_page_sort();
    break;
    //刪除頁面
    case "del_page":
    del_page($page_sn);
    header("location:{$_SERVER['PHP_SELF']}?op=pageadmin_form&book_sn={$book_sn}");
    break;
    //儲存頁面內容
    case "save_page_content":
    save_page_content($page_sn);
    //header("location:{$_SERVER['PHP_SELF']}?op=pageadmin_form&book_sn={$book_sn}");
    break;
    //ajax產生編輯頁面內容表單
    case "ckeditor":
    $content=ckeditor($page_sn);
    break;
    //儲存新增的頁面
    case "save_new_page":
    save_new_page($book_sn);
    header("location:{$_SERVER['PHP_SELF']}?op=pageadmin_form&book_sn={$book_sn}");
    break;
    
    default:
    $content=bootstrap(pageadmin_form($book_sn),ckedit_include(),"",add_page_js_fun());
}

//------------------輸出區----------------------//
echo $content;
//----------------------函數區-------------------------//
function pageadmin_form($book_sn=""){
    global $tblBook,$cover_url,$link,$img_url;
    //取得書籍內容
    $sql="select * from `{$tblBook}` where book_sn='{$book_sn}'";
    $result=mysql_query($sql,$link) or die_content("查詢資料失敗".mysql_error());
    while($data=mysql_fetch_assoc($result)){
        foreach($data as $i=>$v){
            $$i=$v;
        }
    }
    $show_image="<img src='{$cover_url}cover_{$book_sn}.jpg' class='img-rounded img-polaroid'>";
    $main="
    <ul class='thumbnails'>
    <li class='span7'>
    <div id='book_cover'>{$show_image}</div>
    </li>
    <li class='span5'>
    <table class='table table-condensed table-hover table-bordered table-striped'>
    <tr><td><h6>書籍名稱</h6></td></tr
    <tr><td>{$book_title}</td></tr>
    <tr><td><h6>書籍標題</h6></td></tr>
    <tr><td>{$book_content}</td></tr>
    <tr><td><h6>書籍關鍵字</h6></td></tr>
    <tr><td>{$book_keyword}</td></tr>
    <tr><td><h6>出版日期</h6></td></tr>
    <tr><td>{$book_date}</td></tr>
    <tr><td><h6>書籍點閱次數</h6></td></tr>
    <tr><td>{$book_click}</td></tr>
    </table>
    </li>
    </ul>
    ";
    //列出目前有的頁面
    $main.=list_page($book_sn);
    $main.="<div id='page_edit_area'></div>";
    $main.="
    <form class='well' method='post' action='{$_SERVER['PHP_SELF']}'>
    <div id='page_add_form'>
    <img src='{$img_url}add_form.png' id='add_pageform' title='輸入新增頁面標題'>
    <span class='label label-warning'>點選圖示增加頁面新增的表單</span>
    <div id='page_input'>直接填入頁面標題：<input type='text' name='page_title[]'></div>
    </div>
    <input type='hidden' name='op' value='save_new_page'>
    <input type='hidden' name='book_sn' value='{$book_sn}'> 
    <input type='submit' value='新增頁面'>
    </form>
    ";
    return $main;
}
function add_page_js_fun(){
    $main="
    <script>
    $(function(){
        $('#add_pageform').click(function(){
            $('#page_input').clone().appendTo('#page_add_form');
        });
        $('.btn-success').click(function(){
            var page_sn =$(this).attr('id');
            $.get('page_admin.php',{op:'ckeditor',page_sn:page_sn},function(data){
                if(CKEDITOR.instances.page_edit_contentarea){
                    CKEDITOR.instances.page_edit_contentarea.destroy();
                }
                $('#page_edit_area').html(data);    
            });
        });
        $('.btn-danger').click(function(){
            if (!confirm('確定進行點選的動作'))
            return false;
        });
        $( '#sortable' ).sortable({
            update : function () {
                $('#sort_info').html('排序中......');
                var sorted = $('#sortable').sortable('serialize');
                $('#sort_info').load('page_admin.php?op=save_page_sort&' + sorted); 
            } 
        });
    });
    </script>
    ";
    return $main;
}
//儲存頁面資料
function save_new_page($book_sn=""){
    global $link,$tblPage;
    if(!$_POST['page_title']) die_content("無新增頁面");
    foreach($_POST['page_title'] as $v){
        $sql="insert into `{$tblPage}` (`page_title`, `book_sn`) values ('{$v}','{$book_sn}')";
        $result = mysql_query($sql,$link) or die_content("新增電子書頁面失敗".mysql_error());
    }
}
function list_page($book_sn=""){
    global $link,$tblPage;
    $n=1;
    $main="
    <span id='sort_info' class='label label-important'></span>
    <table class='table table-condensed table-hover table-bordered table-striped'>
    <tr><th>頁碼順序</th><th>頁面標題</th><th>功能按鍵</th></tr>
    <tbody id='sortable'>
    ";
    $sql="select * from `{$tblPage}` where book_sn='{$book_sn}' order by page_sort,page_sn";
    $result=mysql_query($sql,$link) or die_content("查詢資料失敗".mysql_error());
    while($data=mysql_fetch_assoc($result)){
        foreach($data as $i=>$v){
            $$i=$v;
        }
        $main.="<tr id='page_{$page_sn}'><td>{$n}</td><td>{$page_title}</td><td>
        <a class='btn btn-danger' href='{$_SERVER['PHP_SELF']}?op=del_page&page_sn={$page_sn}&book_sn={$book_sn}'>刪除</a>
        <a class='btn btn-success' id='{$page_sn}'>修改內容</a>
        </td></tr>";
        $n++;
    }
    $main.="
    </tbody>
    </table>
    ";
    return $main;
}
function ckedit_include(){
    $main="
    <script src='js/ckeditor/ckeditor.js'></script>
    ";
    return $main;
}
function ckeditor($page_sn=""){
    global $link,$tblPage,$tblBook;
    $sql="select page_title,page_content,book_sn from {$tblPage} where page_sn='{$page_sn}'";
    $result=mysql_query($sql,$link) or die_content("查詢資料失敗".mysql_error());
    list($page_title,$page_content,$book_sn)=mysql_fetch_row($result); 
    $main="
    <script src='js/ckeditor/ckeditor.js'></script>
    <script>
    $(function(){
        $('#feedback_info').html('');
		CKEDITOR.replace( 'page_edit_contentarea', {
			filebrowserBrowseUrl: '/booklet/js/ckeditor/ckfinder/ckfinder.html',
			filebrowserUploadUrl: '/booklet/js/ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files'
		});
        $('#save_page_content').click(function(){
            var page_content = CKEDITOR.instances.page_edit_contentarea.getData();
            $.post('page_admin.php',
            {op:'save_page_content',page_sn:{$page_sn},page_content:page_content},
            function(){
                $('#feedback_info').html('修改完畢');
            });
        });
    });
    </script>
    <div class='alert alert-block'>
    <span id='feedback_info' class='label label-important'></span>
    <button type='button' class='close' data-dismiss='alert'>×</button>
    <form method='post' action='{$_SERVER['PHP_SELF']}' id='editor'>
    <h6>{$page_title}<input type='hidden' name='page_sn' value='{$page_sn}'>
    <textarea name='page_content' id='page_edit_contentarea'>{$page_content}</textarea>
    <input type='hidden' name='op' value='save_page_content'>
    <input type='hidden' name='book_sn' value='{$book_sn}'>
    <a class='btn btn-success' id='save_page_content'>儲存頁面</a>
    </h6>
    </form>
    </div>
    ";
    return  $main;
}
function save_page_content($page_sn=""){
    global $link,$tblPage,$tblBook;
    $sql="update `{$tblPage}` set page_content='{$_POST['page_content']}' where page_sn='{$page_sn}'";
    $result=mysql_query($sql,$link) or die_content("更新資料失敗".mysql_error());
}
function del_page($page_sn=""){
    global $link,$tblPage,$tblBook;
    $sql="delete from `{$tblPage}` where page_sn='{$page_sn}'";
    $result=mysql_query($sql,$link) or die_content("刪除資料失敗".mysql_error());
}

function save_page_sort(){
    global $link,$tblPage,$tblBook;
    foreach($_GET['page'] as $i=>$v){
        $sql="update `{$tblPage}` set page_sort='{$i}' where page_sn='{$v}'";
        $result=mysql_query($sql,$link) or die_content("頁面排序失敗".mysql_error());
    }
    return "頁面排序成功";
}
?>