<?php
include_once("setup.php");
//-------------------設定區-----------------------//
$op=(empty($_REQUEST['op']))?"":$_REQUEST['op'];
$book_sn=(empty($_REQUEST['book_sn']))?"":$_REQUEST['book_sn'];

//---------------流程控制區----------------------//
switch($op){
    case "del_book":
    del_book($book_sn);
    header("location:{$_SERVER['PHP_SELF']}");
    break;
    //顯示電子書
    case "show_book":
    $content=bootstrap("顯示電子書，還沒有製作");
    break;
    default:
    $content=bootstrap(book_list());
}

//------------------輸出區----------------------//
echo $content;
//----------------------函數區-------------------------//

function book_list(){
    global $link,$tblBook,$cover_url,$op;
    $book_list="";
    $main="";
    if($op=='no_show_list'){
        $sql="select * from `{$tblBook}` where book_enable='no'";
    }else{
        $sql="select * from `{$tblBook}` where book_enable='yes'";
    }

    $result=mysql_query($sql,$link) or die_content("查詢{$tblBook}資料失敗");
    while($data=mysql_fetch_assoc($result)){
        foreach($data as $i=>$v){
            $$i=$v;
        }
        $book_list.="<tr>
        <td style='margin:0 auto;text-align:center;'>
        <a href='MoleskineNotebook/index.php?book_sn={$book_sn}'>
        <img src='{$cover_url}cover_{$book_sn}.jpg' class='span3 img-rounded img-polaroid'>
        <span class='label label-success' >{$book_title}</span>
        </a>
        </td>
        <td>{$book_content}</td>
        <td>{$book_keyword}</td>
        <td>{$book_date}</td>
        <td>{$book_click}</td>
        <td class='span2'>
        <a href='book_admin.php?op=modify_form&book_sn={$book_sn}' class='btn btn-warning'>修改</a>
        <a href='page_admin.php?op=pageadmin_form&book_sn={$book_sn}' class='btn btn-success'>頁面管理</a>
        <a href='{$_SERVER['PHP_SELF']}?op=del_book&book_sn={$book_sn}' class='btn btn-danger'>刪除書籍</a>
        </td></tr>
        ";
    }
    $main="
    <table class='table table-striped table-bordered table-condensed'>
    <tr><th>標題</th><th>簡介內容</th><th>關鍵字</th><th>出版日期</th><th>點閱率</th><th>其他功能</th></tr>
    {$book_list}
    </table>
    ";
    return  $main;
}

function modify_book_show($book_sn=""){
    global $link,$tblBook,$cover_url,$op;
    if($op=='modify_yes'){
        $sql="update `{$tblBook}` set book_enable='yes' where book_sn='{$book_sn}'";
    }elseif($op=='modify_no'){
        $sql="update `{$tblBook}` set book_enable='no' where book_sn='{$book_sn}'";
    }
    mysql_query($sql,$link) or die_content("更新{$tblBook}資料失敗".mysql_error());
}

function del_book($book_sn=""){
    global $link,$tblBook,$tblPage,$tblComment,$tblRubric,$cover_url,$cover_path;
    $sql="delete from `{$tblRubric}` where book_sn='{$book_sn}'";
    mysql_query($sql,$link) or die_content("刪除{$tblRubric}資料失敗".mysql_error());
    $sql="delete from `{$tblComment}` where book_sn='{$book_sn}'";
    mysql_query($sql,$link) or die_content("刪除{$tblComment}資料失敗".mysql_error());
    $sql="delete from `{$tblPage}` where book_sn='{$book_sn}'";
    mysql_query($sql,$link) or die_content("刪除{$tblPage}資料失敗".mysql_error());
    unlink("{$cover_path}cover_{$book_sn}.jpg");
    $sql="delete from `{$tblBook}` where book_sn='{$book_sn}'";
    mysql_query($sql,$link) or die_content("刪除{$tblBook}資料失敗".mysql_error());
}
?>