<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("iblock");
global $USER, $DB, $_SESSION;
if(!$USER->IsAuthorized()) exit();
header('Content-type: application/json; charset=utf-8');
if(!empty($_POST)) {

    if(isset($_POST['group_id'])){


        $el = new CIBlockElement();
        $group_id = intval($_POST['group_id']);
        $contract_id = intval($_POST['contract_id']);

        $checkGroup = fetch(sprintf("SELECT COUNT(`id`) as CNT FROM `risk_cache` WHERE `contract_id` = %d", $contract_id));
        if($checkGroup) {
            if(intval($checkGroup['CNT']) > 0) {
                echo json_encode(array('status' => "error", 'message'=> "You cannot combine group risk with individual, please remove other risks before!"));
                exit;
            }
        }


        $UID = $USER->GetID();
        $partnerID = getPartnerIdByUserId($UID);
        $strQuery = sprintf("SELECT `risk_id`, `type` FROM `sttn_tp0_group_risk_link` WHERE `deleted` = 0 and `group_id` = %d", $group_id);

        $res = fetch_all($strQuery);
        if($res) {

            $j = 0;
            $e = 0;
            foreach($res as $item){

                switch($item['type']){
                    case 'risk': {
                        if(isset($_POST[$item['risk_id']])) {
                            $risk = $el->GetByID($item['risk_id']);
                            if($ar_props = $risk->Fetch()){
                                $name = sprintf("%s", $ar_props['NAME']);
                                $risk_type = 'ug';
                                $uid =  $UID;
                                $hash = md5($uid . date('Y-m-d'));
                                $strSql = sprintf("INSERT INTO `risk_cache`(`uid`, `risk_id`, `risk_type`, `risk_name`, `risk_sum`, `risk_premium`, `contract_id`) VALUES(%d,'%d','%s','%s','%d','%d','%d');", $uid, $item['risk_id'], $risk_type, $name, $_POST[$item['risk_id']], 0, $contract_id);
                                if($DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__)){
                                    $j++;
                                } else {
                                    $e++;
                                }
                            }
                        }

                    }
                        break;
                    case 'erisk':{

                        if(isset($_POST[$item['risk_id']])) {
                            $WERisk = fetch(sprintf("SELECT `id`, `risk_id`, `risk_code`, `risk_name`, `max_value`, `min_value`, `mandatory`, `tarif`, `rate`, `fixed_days`, `exactly`, `prefer`, `deleted` FROM `sttn_tp0_ext_risks` WHERE `id` = %d", $item['risk_id']));
                            if ($WERisk) {
                                $name = sprintf("%s/%s (Расширенный)", $WERisk['risk_code'], $WERisk['risk_name']);
                                $risk_type = 'eg';
                                $uid =  $UID;
                                $hash = md5($uid . date('Y-m-d'));
                                $strSql = sprintf("INSERT INTO `risk_cache`(`uid`, `risk_id`, `risk_type`, `risk_name`, `risk_sum`, `risk_premium`, `contract_id`) VALUES(%d,'%d','%s','%s','%d','%d','%d');", $uid, $item['risk_id'], $risk_type, $name, $_POST[$item['risk_id']], 0, $contract_id);
                                if($DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__)){
                                    $j++;
                                } else {
                                    $e++;
                                }
                            }
                        }
                    }
                        break;
                } // switch
            }

        }


        if($j>0 and $e==0) {
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