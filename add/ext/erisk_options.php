<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("iblock");
global $USER, $DB, $_SESSION;
if(!$USER->IsAuthorized()) {
    echo json_encode(
        array(
            'status' => "error",
            'message' => "Unauthorized"
        ));
    exit;
}
header('Content-type: text/html; charset=windows-1251');
$UID = $USER->GetID();
$UID = 2;
$partnerID = getPartnerIdByUserId($UID);

$strQuery = sprintf("SELECT er.`id`, er.`risk_id`, er.`risk_code`, er.`risk_name`, er.`max_value`, er.`min_value`, er.`mandatory`, er.`tarif`, er.`rate`, er.`fixed_days`, er.`exactly`, er.`prefer`, lp.`correlation`, lp.`exactly` as exactlyPartner, lp.`fixed_days` as fixedDaysPartner, lp.`note`FROM `sttn_tp0_ext_risks` er INNER JOIN `partneer_sttn_erisk_tp0` lp ON er.`id` = lp.`erisk` WHERE er.`deleted` = 0 and lp.`deleted` = 0 and lp.`partner_id` = %d", $partnerID);
$res = fetch_all($strQuery);
//print_r($res);die();
$options = array();
$options[] = '<option>Выберите риск</option>';

if ($res) {
    foreach($res as $w) {


        $options[] = sprintf('<option min="%s" max="%s" value="%s" mandatory="%s">%s[%s]</option>',
            $w['min_value'],
            $w['max_value'],
            $w['id'],
            ($w['mandatory']==1)?'Y':'N',
            $w['risk_name'],
            $w['risk_code']);
    }
    printf('%s', implode("\n",$options));
}