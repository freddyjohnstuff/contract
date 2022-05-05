<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("iblock");
global $USER;
if(!$USER->IsAuthorized()) exit();
    header('Content-type: text/html; charset=windows-1251');
    $UID = $USER->GetID();
    $UID = 2;
    $partnerID = getPartnerIdByUserId($UID);

    $query = sprintf("SELECT gp.`id`, gp.`name`, gp.`correlation`, gp.`exactly`, gp.`fixed_days`, gp.`prefer`, prg.`correlation` as partneer_correlation, prg.`exactly` as partneer_exactly, prg.`fixed_days` as partneer_fixed_days, prg.`prefer` as partneer_prefer, gp.`maximum`, gp.`minimum` FROM `sttn_tp0_group_risk` gp Inner join `partneer_sttn_group_tp0` prg ON gp.`id` = prg.`group` WHERE gp.`deleted` = 0 and prg.`deleted` = 0 and prg.`partner_id` = %d", $partnerID);



    $res = fetch_all($query);
    if ($res) {
        $options = Array();
        $options[] = '<option>Выберите риск</option>';

        foreach($res as $w){
            $options[] = sprintf('<option min="%s" max="%s" value="%s">%s[%s]</option>',
                $w['minimum'],
                $w['maximum'],
                $w['id'],
                $w['name'],
                $w['correlation']);
        }
    }else{
        $options[] = sprintf('<option>%s</option>',$DB->GetErrorMessage());
    }
    printf('%s', implode("\n",$options));

?>