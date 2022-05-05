<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("iblock");
global $USER, $DB;
if(!$USER->IsAuthorized()) exit();
header('Content-type: text/html; charset=windows-1251');
if(!empty($_POST) && intval($_POST['id'])>0){

    $el = new CIBlockElement();
    $id = $_POST['id'];
    $UID = $USER->GetID();
    $partnerID = getPartnerIdByUserId($UID);
    $strQuery = sprintf("SELECT `risk_id`, `type` FROM `sttn_tp0_group_risk_link` WHERE `deleted` = 0 and `group_id` = %d", $id);
    //$strQuery = sprintf("SELECT er.`id`, er.`risk_id`, er.`risk_code`, er.`risk_name`, er.`max_value`, er.`min_value`, er.`mandatory`, er.`tarif`, er.`rate`, er.`fixed_days`, er.`exactly`, er.`prefer`, lp.`correlation`, lp.`exactly` as exactlyPartner, lp.`fixed_days` as fixedDaysPartner, lp.`note`FROM `sttn_tp0_ext_risks` er INNER JOIN `partneer_sttn_erisk_tp0` lp ON er.`id` = lp.`erisk` WHERE er.`deleted` = 0 and lp.`deleted` = 0 and er.`id` = %d", $id);
    $res = fetch_all($strQuery);
    if($res) {

        $row = [];
        foreach($res as $item){

            $col = [];
            switch($item['type']){
                case 'risk': {
                    $risk = $el->GetByID($item['risk_id']);
                    if($ar_props = $risk->Fetch()){


                        $MAX = getPropertyByIblockId($item['risk_id'],"risklist","MAX");
                        $MIN = getPropertyByIblockId($item['risk_id'],"risklist","MIN");
                        $name = sprintf("%s", $ar_props['NAME']);
                        $col[] = sprintf('<td><input class="gtext readonly" readonly type="text" id="name_%d" value="%s"/></td>',
                            $ar_props['ID'], $name);
                        $col[] = sprintf('<td><input placeholder="Сумма" class="gsumm" type="text" id="sum_%d" value="" max="%s" min="%s"/></td>',$ar_props['ID'], $MAX, $MIN);
                        $row[] = sprintf('<tr class="grecord" id="%s">%s</tr>', $item['risk_id'], implode('' , $col));
                    }
                }
                break;
                case 'erisk':{

                    $WERisk = fetch(sprintf("SELECT `id`, `risk_id`, `risk_code`, `risk_name`, `max_value`, `min_value`, `mandatory`, `tarif`, `rate`, `fixed_days`, `exactly`, `prefer`, `deleted` FROM `sttn_tp0_ext_risks` WHERE `id` = %d", $item['risk_id']));
                     if ($WERisk) {
                         $name = sprintf("%s/%s (Расширенный)", $WERisk['risk_code'], $WERisk['risk_name']);
                         $col[] = sprintf('<td><input class="gtext readonly" readonly type="text" id="name_%d" value="%s"/></td>',
                             $item['risk_id'],$name);
                         $col[] = sprintf('<td><input placeholder="Сумма" class="gsumm digitonly" type="text" id="sum_%d" value="" max="%s" min="%s" /></td>',$item['risk_id'], $WERisk['max_value'], $WERisk['min_value']);
                         $row[] = sprintf('<tr class="grecord" id="%s">%s</tr>', $item['risk_id'], implode('' , $col));
                     }
                }
                break;
            } // switch
        }



        $Wgroup = fetch(sprintf("SELECT `name`, `maximum`, `minimum` FROM `sttn_tp0_group_risk` WHERE `deleted` = 0  and  `id`= %d ", $id));
        if ($Wgroup) {
            $name = $Wgroup['name'];
            $brow = sprintf('<tr class="thead"><th colspan="2">%s</th></tr>', $name);

            $total = sprintf('<input readonly placeholder="Итог" class="total" id="total" value="" max="%s" min="%s" />',$Wgroup['maximum'],$Wgroup['minimum']);

            $row[] = sprintf('<tr class="tfoot"><th>Итог</th><th>%s</th></tr>', $total );
        }

        $table = sprintf('<table class="risk_list">%s</table>', $brow . implode('', $row));
    }
    echo $table;
}
?>