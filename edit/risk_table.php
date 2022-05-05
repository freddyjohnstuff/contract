<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("iblock");
global $USER, $DB;
if(!$USER->IsAuthorized()) exit();
header('Content-type: text/htnl; charset=windows-1251');
if (isset($_GET['id'])) {
    $uid =  $USER->GetID();
    $fixedArr = getFixedArray($uid);
    //printf('<pre>%s<pre>', print_r($fixedArr,1));


    $sqlQuery = sprintf("SELECT `id`, `risk_name`, `risk_id`, `risk_sum`, `risk_premium` FROM `risk_cache` WHERE `contract_id` = %d  ORDER BY `risk_name` ASC;", $_GET['id']);
    $res = $DB->Query($sqlQuery, false, "File: ".__FILE__."<br>Line: ".__LINE__);

    if ($res) {

        $rows[] = '<tr><th>Риск</th><th>Сумма</th><th>Удалить</th></tr>';
        $el = new CIBlockElement();
        $sum = 0;
        while($w = $res->GetNext()){
            $fixedArr = array_remove_by_value($fixedArr,  getRiskByProgram($w['risk_id']));
            $cols = array();
            $cols[] = sprintf('<td>%s</td>', $w['risk_name']);
            $cols[] = sprintf('<td class="right">%s</td>', $w['risk_sum']);
            //$cols[] = sprintf('<td class="right">%s</td>', $w['risk_premium']);
            $cols[] = sprintf('<td class="center"><button id="%d" ibid="%d" class="remove-risk btn btn-danger">X</button></td>', $w['id'], $w['risk_id']);
            $rows[] = sprintf('<tr class="risk">%s</tr>', implode('',$cols));
            $sum += floatval($w['risk_sum']);
        }

        $mandatory = (empty($fixedArr)) ? 'Y' : 'N';
        $rows[] = sprintf('<tr><th>&nbsp;</th><th>%d</th><th>&nbsp;</th></tr>', $sum);
        printf('<table class="risk_list" cellpadding="0" cellspacing="0" mandatory="%s">%s</table>', $mandatory, implode('',$rows));
    } else {
        echo $DB->GetErrorMessage();
    }
}
?>