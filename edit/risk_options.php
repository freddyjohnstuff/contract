<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("iblock");
global $USER, $DB, $_SESSION;
if(!$USER->IsAuthorized()) exit();
header('Content-type: text/html; charset=windows-1251');

    $elGet = new CIBlockElement();
    if(isset($_SESSION['tp0_edit_id'])){
        $db_props = $elGet->GetProperty(10, $_SESSION['tp0_edit_id'], "sort","asc", Array("CODE"=>"PARTNER"));
        if($ar_props = $db_props->Fetch()){
            //print_r($ar_props);
            $partnerID = intval($ar_props['VALUE']);
        } else {
            $partnerID = 0;
        }
    }else {
        $partnerID = 0;
    }

    //print_r($partnerID );
    //print_r($elGet->LAST_ERROR);
    $strQuery = sprintf("SELECT `risk_id`, `policy`, `fixed_sum` FROM `partneer_sttn_policy_tp0` WHERE `partner_id` = %d and `deleted` = 0", $partnerID);


$res = $DB->Query($strQuery, false, "File: ".__FILE__."<br>Line: ".__LINE__);
if ($res && $res->SelectedRowsCount()>0) {
    $arrAllowedRisk = array();
    while($w = $res->GetNext()){
        $arrAllowedRisk[] = $w['risk_id'];
        $risks[$w['risk_id']] = $w;
    }
}

$el =  new CIBlockElement();
$arSelect = Array("ID", "NAME", "CODE", "PROPERTY_RISK", "PROPERTY_RISK.CODE","PROPERTY_MIN","PROPERTY_MAX","PROPERTY_MANDATORY");
$arFilter = Array("IBLOCK_CODE"=>'risklist', "ACTIVE"=>"Y",  "PROPERTY_RISK"=>$arrAllowedRisk);
$res = $el->GetList(Array("NAME"=>"ASC"), $arFilter, false, false, $arSelect);
$options = array();
$options[] = '<option>Выберите риск</option>';

while($ob = $res->GetNextElement())
{
    $arFields = $ob->GetFields();
    $options[] = sprintf(
        '<option min="%d" max="%d" fixed="%s" policy="%s"  mandatory="%s" value="%d">%s</option>',
        $arFields['PROPERTY_MIN_VALUE'],
        $arFields['PROPERTY_MAX_VALUE'],
        $risks[$arFields['PROPERTY_RISK_VALUE']]['fixed_sum'],
        $risks[$arFields['PROPERTY_RISK_VALUE']]['policy'],
        $arFields['PROPERTY_MANDATORY_VALUE'],
        $arFields['ID'],
        $arFields['NAME']);

}
printf('%s', implode('',$options));
?>