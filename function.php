<?php
//輸入陣列產生單選表單
function array_to_radio($arr=array(),$use_v=false,$name="default",$default_val="",$validate=false){
	if(empty($arr))return;
	$opt="";
	foreach($arr as $i=>$v){
		$val=($use_v)?$v:$i;
		$checked=($val==$default_val)?"checked='checked'":"";
		$validate_check=($validate)?"class='required'":"";
		$opt.="<input type='radio' name='{$name}' id='{$val}' value='{$val}' $validate_check $checked><label for='{$val}' style='margin-right:15px;'> $v</label>";
	}
	return $opt;
}
?>