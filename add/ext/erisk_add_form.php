<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("iblock");
global $USER, $DB;
if(!$USER->IsAuthorized()) exit();
header('Content-type: text/html; charset=windows-1251');
if(!empty($_POST) && intval($_POST['id'])>0){
    $id = $_POST['id'];
    $UID = $USER->GetID();
    $partnerID = getPartnerIdByUserId($UID);

    $strQuery = sprintf("SELECT er.`id`, er.`risk_id`, er.`risk_code`, er.`risk_name`, er.`max_value`, er.`min_value`, er.`mandatory`, er.`tarif`, er.`rate`, er.`fixed_days`, er.`exactly`, er.`prefer`, lp.`correlation`, lp.`exactly` as exactlyPartner, lp.`fixed_days` as fixedDaysPartner, lp.`note`FROM `sttn_tp0_ext_risks` er INNER JOIN `partneer_sttn_erisk_tp0` lp ON er.`id` = lp.`erisk` WHERE er.`deleted` = 0 and lp.`deleted` = 0 and er.`id` = %d", $id);
    $res = fetch($strQuery);
    if($res) {
        $risk = getRiskByProgram($res['risk_id']);
    }

    $query = sprintf("SELECT `id`,`risk_name`, `policy`, `fixed_sum` FROM `partneer_sttn_policy_tp0` WHERE `partner_id` = %d and `risk_id` = %d and `deleted` = 0", $partnerID, $res['risk_id']);
    $wrxaq = fetch($query);

    if ($wrxaq) {
        $fixed = $wrxaq['fixed_sum'];
    } else {
        $fixed = 0;
    }

    if (intval($fixed) == 1) {
        $el =  new CIBlockElement();
        $arSelect = Array("ID", "NAME", "CODE", "PROPERTY_AMMOUNT");
        $arFilter = Array("IBLOCK_CODE"=>"riskfixedamount", "ACTIVE"=>"Y", "PROPERTY_RISK"=>$risk);
        $res = $el->GetList(Array("AMMOUNT"=>"ASC"), $arFilter, false, false, $arSelect);
        $options = array();
        while($ob = $res->GetNextElement())
        {
            $arFields = $ob->GetFields();
            $options[] = sprintf('<option value="%s" id="%d">%s</option>',
                $arFields['PROPERTY_AMMOUNT_VALUE'],
                $arFields['ID'],
                $arFields['PROPERTY_AMMOUNT_VALUE']);
        }
        printf('<label class="theLabel">Фиксированная сумма:<select id="sum">%s</select></label>', implode('',$options));
    }else{
        print '<label class="theLabel">Cумма: <input id="sum" type="text" /></label>';
    }
}
?>