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


        $checkGroup = fetch(sprintf("SELECT COUNT(`id`) as CNT FROM `risk_cache` WHERE `risk_type` like '%%g' AND  `contract_id` = %d", $contract_id));
        if($checkGroup) {
            if(intval($checkGroup['CNT']) > 0) {
                echo json_encode(array('status' => "error", 'message'=> "You cannot combine Extension risk with group risks, please remove other risks before!"));
                exit;
            }
        }



        $WN = fetch(sprintf("SELECT GROUP_CONCAT(c.`name`) as CND FROM `sttn_tp0_conditions` c inner join `sttn_tp0_conditions_risk_link` cr on c.`id`= cr.`cndn` WHERE c.`deleted` = 0 and cr.`deleted` = 0 and cr.`risk` = %d", $_POST['id']));


        if ($WN) {
            $CNDS = $WN['CND'];
        }

        $WR = fetch(sprintf("SELECT `risk_id`, `risk_code`, `risk_name`  FROM `sttn_tp0_ext_risks` WHERE `id` = %d",$_POST['id']));
        if($WR) {
            $risk_id_iblock = $WR['risk_id'];
            $CHAR = $WR['risk_code'];
        }

        $arNonload =  Array();
        $arSelect = Array("ID", "NAME", "CODE", "PROPERTY_TARIF", "PROPERTY_RISK.CODE");
        $arFilter = Array("IBLOCK_CODE"=>'risklist', "ACTIVE"=>"Y", "ID"=>$risk_id_iblock);
        $res = $el->GetList(Array("ID"=>"ASC"), $arFilter, false, false, $arSelect);
        if($res) {

            if($ob = $res->GetNextElement())
            {
                $arFields = $ob->GetFields();
                $TARIF = floatval( $arFields['PROPERTY_TARIF_VALUE'] );
                $PREMIUM = floatval($_POST['sum']) * $TARIF;
                $CHAR = $arFields['PROPERTY_RISK_CODE'];
            }

            $NAME = sprintf("%s/%s[%s]", $arFields['NAME'], $WR ['risk_name'] , $CNDS);

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


        if(array_search($CHAR,$arNonload) === false) {
            $risk_type = 'e';
            $uid =  $USER->GetID();
            $hash = md5($uid . date('Y-m-d'));
            $strSql = sprintf("INSERT INTO `risk_cache`(`uid`, `risk_id`, `risk_type`, `risk_name`, `risk_sum`, `risk_premium`, `contract_id`) VALUES(%d,'%d','%s','%s','%d','%d','%d');", $uid, $_POST['id'], $risk_type, $NAME, $_POST['sum'], $PREMIUM, $contract_id);
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