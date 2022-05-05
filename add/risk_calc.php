<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("iblock");
global $USER, $DB;
if(!$USER->IsAuthorized()) exit();

header('Content-type: text/htnl; charset=windows-1251');
//=========================header====================================
//=========================body======================================

if (!empty($_POST['data'])) {

    if (
        isset($_POST['data']['DATAROGDENIA'])
        and isset($_POST['data']['DATAVILETA'])
        and (isset($_POST['data']['DATAEND']) or isset($_POST['data']['PERIOD_DAYS']))
        and isset($_POST['data']['FRANCHISE'])
        and isset($_POST['data']['TERRITORY'])
        and isset($_POST['data']['SPORT'])
        and isset($_POST['data']['CONTRACT'])
        and isset($_POST['data']['CURRENCY'])
    ) {

        $DATAROGDENIA	=		$_POST['data']['DATAROGDENIA'];
        $DATAVILETA		=		$_POST['data']['DATAVILETA'];
        $FRANCHISE		=		$_POST['data']['FRANCHISE'];
        $TERRITORY		=		$_POST['data']['TERRITORY'];
        $SPORT		    =		$_POST['data']['SPORT'];
        $PERIOD_DAYS    =		$_POST['data']['PERIOD_DAYS'];
        $DATAEND		=		$_POST['data']['DATAEND'];
        $CONTRACT       =		$_POST['data']['CONTRACT'];

        $params = Array(
            'DATAROGDENIA' => $DATAROGDENIA,
            'DATAVILETA' => $DATAVILETA,
            'DATAEND' => $DATAEND,
            'PERIOD_DAYS' => $PERIOD_DAYS,
            'FRANCHISE' => $FRANCHISE,
            'TERRITORY' => $TERRITORY,
            'SPORT' => $SPORT
        );

        $CURRENCY = ($_POST['data']['CURRENCY'] == 2)?get_storage('tp0_polic_eur'):get_storage('tp0_polic_usd');
        $premium = getTotalPremiumSum($CONTRACT, false, 'param', $params);
        $premiumSomoni = converUnitToSomoniByDate($premium, $_POST['data']['CURRENCY'], $DATAVILETA);
        printf('<p>Размер страховой премии: %s сомони</p>',$premiumSomoni);

    } else {
        // does't match count of parameters

    }
} else {
    // post empty
}
?>