<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("iblock");
global $USER, $DB;
if(!$USER->IsAuthorized()) exit();
header('Content-type: text/html; charset=windows-1251');
if(!empty($_POST) && intval($_POST['id'])>0){
    $id = $_POST['id'];
    $policy = $_POST['policy'];
    $fixed = $_POST['fixed'];
    $UID = $USER->GetID();
    $partnerID = getPartnerIdByUserId($UID);
    $risk = getRiskByProgram($id);

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