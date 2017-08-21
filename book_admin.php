<?php
include_once("setup.php");
include_once "class/class.upload/class.upload.php";
//-------------------設定區-----------------------//
$op=(empty($_REQUEST['op']))?"":$_REQUEST['op'];
$book_sn=(empty($_REQUEST['book_sn']))?"":$_REQUEST['book_sn'];

//---------------流程控制區----------------------//
switch($op){
    //修改電子書動作
    case "update_book":
    update_book($book_sn);
    header("location:index.php");
    break;
    
    //修改電子書表單
    case "modify_form":
    $content=bootstrap(add_book_form($book_sn),add_book_js_link());
    break;
    
    //新增電子書
    case "add_book":
    add_book();
    header("location:index.php");
    break;
    
    default:
    $content=bootstrap(add_book_form(),add_book_js_link());
}

//------------------輸出區----------------------//
echo $content;
//----------------------函數區-------------------------//
function add_book_form($book_sn=""){
    global $yorn_arr,$tblBook,$cover_url,$link;
    if(empty($book_sn)){
    //若$book_sn為空值為新增表單
        $book_title="";
        $book_content="" ;
        $book_keyword="";
        $book_date="";
        $book_enable="";
        $show_image="";
        $hidden="
            <input type='hidden' name='op' value='add_book'>
            <input type='submit' value='新增電子書'>
        ";
    }else{
    //若$book_sn不為空值為修改表單
        $sql="select * from `{$tblBook}` where book_sn='{$book_sn}'";
        $result=mysql_query($sql,$link) or die_content("查詢資料失敗".mysql_error());
        while($data=mysql_fetch_assoc($result)){
            foreach($data as $i=>$v){
                $$i=$v;
            }
        }
        $show_image="<img src='{$cover_url}cover_{$book_sn}.jpg' class='img-rounded img-polaroid'>";
        $hidden="
            <input type='hidden' name='op' value='update_book'>
            <input type='hidden' name='book_sn' value='{$book_sn}'>
            <input type='submit' value='修改電子書'>
        ";
    }
    
    $main="
        <form class='well' enctype='multipart/form-data' method='post' action='{$_SERVER['PHP_SELF']}'>
        <ul class='thumbnails'>
        <li class='span7'>
        <div id='book_cover'>{$show_image}</div>
        </li>
        <li class='span4'>
            <fieldset>
            <label>請輸入書籍標題：</label>
            <input type='text' name='book_title' value='{$book_title}'>
            <label>請輸入書籍簡介：
            </label><textarea name='book_content'>{$book_content}</textarea>
            <label>請輸入書籍關鍵字：（以,區隔）</label>
            <input type='text' name='book_keyword' value='{$book_keyword}'>
            <label>請輸入出版日期：</label>
            <input type='text' name='book_date' 
            onClick='WdatePicker({isShowWeek:true,firstDayOfWeek:1,doubleCalendar:true})' 
            value='{$book_date}'>
            <label>是否開放閱讀：<br>
            ".array_to_radio($yorn_arr,false,"book_enable",$book_enable)."
            <label>上傳封面圖檔：<br>
            <input type='file' name='cover'><br>
            {$hidden}
            </fieldset>
        </li>
        </ul>
        </form>
    ";
    return $main;
}

function add_book_js_link(){
    $main="
    <script src='js/My97DatePicker/WdatePicker.js'></script>
    ";
    return $main;
}

//輸入陣列產生單選表單
function array_to_radio($arr=array(),$use_v=false,$name="default",$default_val=""){
	if(empty($arr))return;
	$opt="";
	foreach($arr as $i=>$v){
		$val=($use_v)?$v:$i;
		$checked=($val==$default_val)?"checked='checked'":"";
		$opt.="<input type='radio' name='{$name}' id='{$val}' value='{$val}' $checked><label for='{$val}' style='margin-right:15px;'> $v</label>";
	}
	return $opt;
}

function add_book(){
    global $link,$tblBook;
    $sql="insert into `{$tblBook}` (`book_title`, `book_content`, `book_keyword`, `book_date`, `book_enable`) values ('{$_POST['book_title']}','{$_POST['book_content']}','{$_POST['book_keyword']}','{$_POST['book_date']}','{$_POST['book_enable']}')";
    //執行資料庫查詢
    $result = mysql_query($sql,$link) or die_content("新增電子書失敗".mysql_error());
    $insertid=mysql_insert_id();
    upload_files_by_class($insertid);
    return $insertid;
}

function upload_files_by_class($insertid=""){
    global $cover_path;
    $handle = new Upload($_FILES['cover'],"zh_TW");
    //取消上傳時間限制
    set_time_limit(0);
    //設置上傳大小
    ini_set('memory_limit', '80M');
    $handle->allowed=array(
    'image/*'
    );
    if ($handle->uploaded){
        $handle->file_safe_name = false;
        if($handle->file_is_image){
            $handle->file_overwrite = true;
            $handle->image_resize         = true;
            $handle->image_ratio_y         = true;
            $handle->image_x              = 1000;
            $handle->image_border = '30px';
            $handle->image_border_color = '#ffffff';
            $handle->image_convert = 'jpg';
            $handle->file_new_name_body="cover_{$insertid}";
        }
        $handle->process($cover_path);
        if (!$handle->processed) {
            die_content($handle->error);
        }
        $handle-> Clean();
    }else{
        die_content($handle->error);
    }
}

function update_book($book_sn=""){
    global $link,$tblBook;
    $sql="replace into `{$tblBook}` (`book_sn`, `book_title`, `book_content`, `book_keyword`, `book_date`, `book_enable`) values ('{$_POST['book_sn']}','{$_POST['book_title']}','{$_POST['book_content']}','{$_POST['book_keyword']}','{$_POST['book_date']}','{$_POST['book_enable']}')";
    $result = mysql_query($sql,$link) or die_content("新增電子書失敗".mysql_error());
    $insertid=mysql_insert_id();
    if(!empty($_FILES["cover"]["name"])){
        $del_file="{$cover_path}cover_{$book_sn}.jpg";
        unlink($del_file);
        upload_files_by_class($insertid);
    }
    return $insertid;
}
?>