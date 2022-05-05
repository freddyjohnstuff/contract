<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("iblock");
global $USER, $DB, $_SESSION;
if(!$USER->IsAuthorized()) exit();
header('Content-type: application/json; charset=utf-8');
if(!empty($_POST)) {
    if(
        isset($_POST['id'])
        and isset($_POST['type'])
        and isset($_POST['contract_id'])
    ){
        $uid =  $USER->GetID();
        $id = intval($_POST['id']);
        $contract_id = intval($_POST['contract_id']);
        $type = trim($_POST['type']);

        if ($type == 'u' or $type == 'e') {
            $strSql = sprintf("DELETE FROM `risk_cache` WHERE `id` = %d LIMIT 1;", $id);
        } else {
            $strSql = sprintf("DELETE FROM `risk_cache` WHERE `contract_id` = %d;", $contract_id);
        }

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