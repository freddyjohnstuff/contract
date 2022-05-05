<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("iblock");
global $USER, $DB;
if(!$USER->IsAuthorized()) exit();
header('Content-type: application/json; charset=utf-8');
if(!empty($_POST)) {

    if(isset($_POST['id'])){
        $uid =  $USER->GetID();
        $strSql = sprintf("SELECT SUBSTR(  `risk_name` , 1, 1 ) as CHRX FROM  `risk_cache` WHERE `id` = %d LIMIT 1;", $_POST['id']);
        $res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
        if ($res) {
            if ($data = $res->GetNext()) {
                $_SESSION['nonload'] = array_remove_by_value($_SESSION['nonload'],$data['CHRX']);
            }
        }

        $strSql = sprintf("DELETE FROM `risk_cache` WHERE `id` = %d LIMIT 1;",
$_POST['id']);
      if($DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__)){
            echo json_encode(array('status' => "ok"));
        } else {
            echo json_encode(array('status' => "error", 'message'=> $DB->GetErrorMessage()));
        }

    }else{
        echo json_encode(array('status' => "error", 'message'=> "Bad parameters!"));
    }

} else {
    echo json_encode(array('status' => "error", 'message'=> "Bad request!"));
}
?>