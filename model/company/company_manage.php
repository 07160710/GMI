<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/6 0006
 * Time: 14:41
 */
require_once("../../public/check_login.php");
require_once("../../public/include/conn.php");
require_once("../../public/function.php");

if($_POST['action']=="add"){
    $new_id = get_new_id('gmi_company');
    $insert_key_str = "";
    $insert_val_str = "";
    foreach ($GLOBALS['company_fields'] as $key){
        if($key != "id"){
            if(isset($_POST['v_'.$key])){
                ${$key} = mysql_escape_string($_POST['v_'.$key]);
                if($key == "name"){
                    $sql = "SELECT COUNT(*) FROM gmi_company WHERE name='".trim($name)."'";
                    $has_company = mysql_result(mysql_query($sql),0);
                    if($has_company){
                        $arr = array(
                            'success' => 0,
                            'error' => '此公司已存在，请勿重复添加!'
                        );
                        echo json_encode($arr);
                        exit;
                    }
                }
                if($insert_key_str != "")$insert_key_str .= ",";
                $insert_key_str .= $key;

                if($insert_val_str != "")$insert_val_str .= ",";
                $insert_val_str .= "'".${$key}."'";
            }
        }
    }

    $sql = "INSERT INTO gmi_company(id,$insert_key_str,created_time) VALUE('$new_id',$insert_val_str,'".time()."')";

//    print_r($sql);
//    exit;
    if(mysql_query($sql)){
        save_log("c",$new_id,"创建");
        $arr = array(
            'success' => 1,
            'error' => "保存公司数据成功:"
        );
        echo json_encode($arr);
        exit;
    }else{
        $arr = array(
            'success'=>0,
            'error'=>"添加公司信息出错：".mysql_error()
        );
        echo json_encode($arr);
        exit;
    }
}

if($_POST['action'] == "edit"){
    $id = mysql_escape_string($_POST['id']);
    $update_field_str = "";

    foreach ($GLOBALS['company_fields'] as $key){
        if($key != "id"){
            if(isset($_POST['v_'.$key])){
                ${$key} = mysql_escape_string($_POST['v_'.$key]);

                if($update_field_str != "")$update_field_str .= ",";
                $update_field_str .= "$key='".${$key}."'";
            }
        }
    }
    $sql = "UPDATE gmi_company SET $update_field_str,update_time='".time()."' WHERE id='$id'";

    if(!mysql_query($sql)){
        $arr = array(
            'success' => 0,
            'error' => "更新公司信息出错:".mysql_error()
        );
        echo json_encode($arr);
        exit;
    }
    save_log("c",$id,"更新公司数据");
    $arr = array(
        'success' => 1
    );
    echo json_encode($arr);
    exit;

}

if($_POST['action'] == "delete_company"){
    if(isset($_REQUEST['scope']) && $_REQUEST['scope'] == "all"){
        $cond_str = "";
        include_once("fetch_company_cond.php");
        $sql = "SELECT id,name 
				FROM gmi_company 
				$cond_str";
        $get_company = mysql_query($sql);
        if(mysql_num_rows($get_company)>0){
            while($row = mysql_fetch_array($get_company)){
                $id = $row[0];
                $name = $row[1];

                $sql = "DELETE FROM gmi_company WHERE id='$id'";
                if(!mysql_query($sql)){
                    $arr = array(
                        'success'=>0,
                        'error'=>"删除公司信息出错：".mysql_error()
                    );
                    echo json_encode($arr);
                    exit;
                }

                save_log("c",$id,"删除[$name]");
            }
        }
    }
    else{
        if(isset($_REQUEST['company_id'])){
            foreach($_REQUEST['company_id'] as $company_id){
                $company_id = mysql_escape_string($company_id);

                $sql = "SELECT name FROM gmi_company WHERE id='$company_id'";
                $name = mysql_result(mysql_query($sql),0);

                $sql = "DELETE FROM gmi_company WHERE id='$company_id'";
                if(!mysql_query($sql)){
                    $arr = array(
                        'success'=>0,
                        'error'=>"删除公司信息出错：".mysql_error()
                    );
                    echo json_encode($arr);
                    exit;
                }

                save_log("c",$company_id,"删除[$name]");
            }
        }
    }

    $arr = array(
        'success'=>1
    );
    echo json_encode($arr);
    exit;
}

if($_REQUEST['action']=="import_company"){
    $save_path = _SAVE_PATH_."upload/";
    //echo $save_path;
    //exit;

    if (empty($_FILES) === false) {//if file exists
        $file_name = $_FILES['file_excel']['name'];
        $tmp_name = $_FILES['file_excel']['tmp_name'];
        $file_size = $_FILES['file_excel']['size'];
        $file_type = $_FILES['file_excel']['type'];

        if ($file_name!="") {
            //get extension
            $temp_arr = explode(".", $file_name);
            $file_ext = array_pop($temp_arr);
            $file_ext = trim($file_ext);
            $file_ext = strtolower($file_ext);
            //check extension
            if ($file_ext!="xls" && $file_ext!="csv") {
                $arr = array(
                    'success'=>0,
                    'error'=>"仅支持XLS和CSV格式！"
                );
                echo json_encode($arr);
                exit;
            }

            $target_dir = "excel/";
            if ($target_dir!="") {
                $target_path = $save_path.$target_dir;
                if (!is_dir($target_path)) {
                    if(!mkdirs($target_path)){
                        $arr = array(
                            'success'=>0,
                            'error'=>"创建上传目录失败！"
                        );
                        echo json_encode($arr);
                        exit;
                    }
                }
            }

            //create file name
            $new_file_name = date("Ymd")."_".rand_letter().".".$file_ext;
            $file_path = $target_path.$new_file_name;

            //move file
            if (move_uploaded_file($tmp_name, $file_path) === false) {
                $arr = array(
                    'success'=>0,
                    'error'=>"上传文件失败！"
                );
                echo json_encode($arr);
                exit;
            }
            @chmod($file_path, 0775);

            //Include PHPExcel_IOFactory
            include_once('../../public/PHPExcel/PHPExcel/IOFactory.php');

            $inputFileName = $file_path;

            //Read your Excel workbook
            try {
                $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                $objPHPExcel = $objReader->load($inputFileName);
            }
            catch(Exception $e){
                die('读取文件失败："'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
            }

            //Get worksheet dimensions
            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();

            //get database company
            $db_company_arr = array();
            $sql = "SELECT name FROM gmi_company";
            $stmt = mysql_query($sql);
            if(mysql_num_rows($stmt)>0){
                while($row = mysql_fetch_array($stmt)){
                    $db_company_arr[] = $row[0];
                }
            }

            //Loop through each row of the worksheet in turn
            $read_num = 0;
            $insert_num = 0;
            $update_num = 0;
            for ($row = 2; $row <= $highestRow; $row++){
                $read_num++;
                //Read a row of data into an array
                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                    NULL,
                    TRUE,
                    FALSE);

                //print_r($rowData);
                //exit;
                foreach($rowData as $k => $row_arr){

                    $name = trim($row_arr[0]);
                    //echo $name;
                    //exit;
                    $short_name = trim($row_arr[1]);

                    $nature_key = trim($row_arr[2]);
                    $short_name_arr = array_flip($GLOBALS['category_nature']);
                    $nature = $short_name_arr[$nature_key];

                    //echo $nature;
                    //exit;
                    $trade_key = trim($row_arr[3]);
                    $trade_name_arr = array_flip($GLOBALS['category_trade']);
                    $trade = $trade_name_arr[$trade_key];


                    $province = trim($row_arr[4]);
                    $sql = "SELECT id FROM region WHERE name='$province'";
                    $province = mysql_result(mysql_query($sql),0);

                    $city = trim($row_arr[5]);
                    $sql = "SELECT id FROM region WHERE name='$city'";
                    $city = mysql_result(mysql_query($sql),0);

                    $district = trim($row_arr[6]);
                    $sql = "SELECT id FROM region WHERE name='$district'";
                    $district = mysql_result(mysql_query($sql),0);

                    $address = trim($row_arr[7]);
                    $scale_key = trim($row_arr[8]);
                    $scale_name_arr = array_flip($GLOBALS['category_scale']);
                    $scale = $scale_name_arr[$scale_key];

                    $registered_fund = trim($row_arr[9]);
                    $contact = trim($row_arr[10]);

                    $telephone = trim($row_arr[11]);
                    $fixed_phone = trim($row_arr[12]);
                    $email = trim($row_arr[13]);
                    $remark = trim($row_arr[14]);

                    $key = $name;

                    if($key!=""){
                        if(in_array($key,$db_company_arr)){
                            $sql = "UPDATE gmi_company 
									SET province='$province',
										city='$city',
										district='$district',
										short_name='$short_name',
										nature = '$nature',
										trade = '$trade',
										scale = '$scale',
										address = '$address',
										contact = '$contact',
										telephone = '$telephone',
										fixed_phones = '$fixed_phone',
										remark = '$remark',
										email = '$email',
										update_time = '".time()."'
									WHERE name='$name'";
                            $update_num++;
                        }else{
                            $new_id = get_new_id('gmi_company');
                            $sql = "INSERT INTO gmi_company(id,name,short_name,nature,trade,province,city,district,address,scale,registered_fund,contact,telephone,fixed_phones,email,remark,created_time,update_time) VALUE('$new_id','$name','$short_name','$nature','$trade','$province','$city','$district','$address','$scale','$registered_fund','$contact','$telephone','$fixed_phones','$email','$remark','".time()."','".time()."')";
                            $insert_num++;
                        }
                        if(!mysql_query($sql)){
                            $arr = array(
                                'success'=>0,
                                'error'=>"导入公司记录出错：".mysql_error()
                            );
                            echo json_encode($arr);
                            exit;
                        }
                    }
                }
            }

            if(unlink($file_path)){
                $arr = array(
                    'success'=>1,
                    'read_num'=>$read_num,
                    'insert_num'=>$insert_num,
                    'update_num'=>$update_num,
                );
            }
            else{
                $arr = array(
                    'action'=>"read excel",
                    'success'=>0,
                    'error'=>"删除文档出错"
                );
            }
            echo json_encode($arr);
            exit;
        }
    }
}

if($_REQUEST['action']=="export_company"){
    $html = "";

    $cond_str = "";
    include_once("fetch_company_cond.php");

    $sort_str = "";
    if($_REQUEST['sort']){
        foreach($_REQUEST['sort'] as $sort){
            $sort_arr = explode("|",$sort);
            if($sort_str!="")$sort_str .= ",";
            $sort_str .= "CONVERT(".$sort_arr[0]." USING GBK) ".strtoupper($sort_arr[1]);
        }
    }
    if($sort_str!=""){
        $sort_str = "ORDER BY ".$sort_prefix.$sort_str;
    }
    else{
        $sort_str = "ORDER BY id";
    }

    $select_field_str = "";
    foreach($GLOBALS['company_list_fields'] as $key=>$val){
        if($select_field_str!="")$select_field_str .= ",";
        $select_field_str .= $key;
    }

    $i = 0;
    $head = "";
    $data = "";
    $sql = "SELECT 
				$select_field_str 
			FROM gmi_company 
			$cond_str 
			$sort_str";
    $stmt = mysql_query($sql);
    if(mysql_num_rows($stmt)>0){
        while($row = mysql_fetch_array($stmt)){
            if($i==0)$head .= '<tr height="40">';
            $data .= '<tr height="30">';
            foreach($GLOBALS['company_list_fields'] as $key=>$val){
                if($key!="file" && $key!="code"){
                    ${$key} = $row[$key];
                    if($key == "nature"){
                        foreach ($GLOBALS['category_nature'] as $k => $value){
                            if($row[$key] == $k){
                                ${$key} = $value;
                            }
                        }
                    }

                    if($key == "trade"){
                        foreach ($GLOBALS['category_trade'] as $k => $value){
                            if($row[$key] == $k){
                                ${$key} = $value;
                            }
                        }
                    }

                    if($key == "scale"){
                        foreach ($GLOBALS['category_scale'] as $k => $value){
                            if($row[$key] == $k){
                                ${$key} = $value;
                            }
                        }
                    }

                    $val_arr = explode("#",$val);
                    $title = $val_arr[0];
                    $width = ($val_arr[1]!="")?"width=\"".$val_arr[1]."\"":"";
                    if($i==0)$head .= '<th style="background:#eee;color:#000;" '.$width.'>'.$title.'</th>';

                    if(	$key=="province" ||
                        $key=="city" ||
                        $key=="district"
                    ){
                        $sql = "SELECT name FROM region WHERE id='".${$key}."'";
                        ${$key} = mysql_result(mysql_query($sql),0);
                    }
                    $data .= '<td align="center">'.${$key}.'</td>';
                }
            }
            if($i==0)$head .= '</tr>';
            $data .= '</tr>';
            $i++;
        }

        $html = <<<HTML
<html xmlns:o="urn:schemas-microsoft-com:office:office"
xmlns:x="urn:schemas-microsoft-com:office:excel"
xmlns="http://www.w3.org/TR/REC-html40">
	<head>
	<!--[if gte mso 9]>
		<xml>
			<x:ExcelWorkbook>
			<x:ExcelWorksheets>
			<x:ExcelWorksheet>
			<x:Name></x:Name>
			<x:WorksheetOptions>
			<x:DisplayGridlines/>
			</x:WorksheetOptions>
			</x:ExcelWorksheet>
			</x:ExcelWorksheets>
			</x:ExcelWorkbook>
		</xml>
	<![endif]-->
	</head>

<table border="1" bordercolor="#999999" style="border-collapse:collapse;font-family:Arial;color:#333;font-size:12px;">
	$head
	$data
</table>
HTML;
    }

    $filename = "company_".date('Y-m-d_His');
    $encoded_filename = urlencode($filename);
    $encoded_filename = str_replace("+", "%20", $encoded_filename);
    $ua = $_SERVER['HTTP_USER_AGENT'];

    header("Content-Type:application/vnd.ms-excel;charset=UTF-8");
    if (preg_match("/MSIE/", $ua)){
        header('Content-Disposition: attachment; filename="' . $encoded_filename . '.xls"');
    }
    else if (preg_match("/Firefox/", $ua)){
        header('Content-Disposition: attachment; filename*="utf8\'\'' . $filename . '.xls"');
    }
    else{
        header('Content-Disposition: attachment; filename="' . $filename . '.xls"');
    }
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Pragma: no-cache");
    header("Expires: 0");
    print $html;
    exit;
}