<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("iblock");
global $USER, $DB, $_SESSION;
if(!$USER->IsAuthorized()) exit();
header('Content-type: application/json; charset=utf-8');
if(!empty($_POST)) {

    if(
        isset($_POST['id'])
        && isset($_POST['contract'])
        && isset($_POST['sum'])
    ){

        $contract_id = $_POST['contract'];
        $el =  new CIBlockElement();
        $arNonload =  Array();
        $arSelect = Array("ID", "NAME", "CODE", "PROPERTY_TARIF", "PROPERTY_RISK.CODE");
        $arFilter = Array("IBLOCK_CODE"=>'risklist', "ACTIVE"=>"Y", "ID"=>$_POST['id']);
        $res = $el->GetList(Array("ID"=>"ASC"), $arFilter, false, false, $arSelect);
        if($res) {

            if($ob = $res->GetNextElement())
            {
                $arFields = $ob->GetFields();
                $TARIF = floatval( $arFields['PROPERTY_TARIF_VALUE'] );
                $PREMIUM = floatval($_POST['sum']) * $TARIF;
                $NAME = $arFields['NAME'];
                $CHAR = $arFields['PROPERTY_RISK_CODE'];
            }


            $strSql = sprintf("SELECT SUBSTR(`risk_name` , 1, 1 ) as CHRX FROM  `risk_cache` WHERE `contract_id` = %d;", $contract_id);
            $res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
            if ($res) {
                while($data = $res->GetNext()) {
                    $arNonload[] = $data['CHRX'];
                }
            } else {
                echo json_encode(array('status' => "error", 'message'=> $DB->GetErrorMessage()));
            }


        } else {
            echo json_encode(array('status' => "error", 'message'=> $DB->GetErrorMessage()));
        }


        if( array_search($CHAR,$arNonload) === false) {

            $uid =  $USER->GetID();
            $hash = md5($uid . date('Y-m-d'));
            $strSql = sprintf("INSERT INTO `risk_cache`(`uid`, `risk_id`, `risk_name`, `risk_sum`, `risk_premium`, `contract_id`) VALUES(%d,'%d','%s','%d','%d','%d');", $uid, $hash ,1, $_POST['id'], $NAME, $_POST['sum'], $PREMIUM, $contract_id);
            if($DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__)){
                echo json_encode(array('status' => "ok", "id" => $_POST['id']));
            } else {
                echo json_encode(array('status' => "error", 'message'=> $DB->GetErrorMessage()));
            }

        } else {
            echo json_encode(array('status' => "error", "message"=>"Duplicate risk!"));
        }

    }else{
        echo json_encode(array('status' => "error", 'message'=> "Bad parameters!"));
    }

} else {
    echo json_encode(array('status' => "error", 'message'=> "Bad request!"));
}
?>