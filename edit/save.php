<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("iblock");

if(array($_POST) && isset($_POST['DOGID']) && intval($_POST['DOGID'])>0)
{

    global $USER;
    $el = new CIBlockElement;
    $elUpd = new CIBlockElement;
    $elUserUpd = new CIBlockElement;

    $User=$USER->GetID();
    $DOGID = intval($_POST['DOGID']);
    $userUpdated = false;
    $userCanges = trim($_POST['USER_CHANGES'])=='Y';


    $accessLevel = $_POST['accessLevel'];
    $PRECLAIM = boolToYN($_POST['PRECLAIM']);
    $CLAIM	  =	boolToYN($_POST['CLAIM']);

    if ($accessLevel != 4 and $PRECLAIM == 'Y' and $CLAIM == 'Y') {

        header('Content-type: application/json; charset=utf-8');
        echo json_encode(array('status' => "error"));


    } elseif ($accessLevel == 4 and $PRECLAIM == 'Y' and $CLAIM == 'Y') {

        $strhID = intval($_POST['ID']);
        if ($userCanges) {


            $PROP["FAMILIA"]= encodeTJ2Win1251Ext($_POST["FAMILIA"]);  // mb_convert_encoding($_POST["FAMILIA"], "windows-1251", "utf-8");
            $PROP["IMIA"]=encodeTJ2Win1251Ext($_POST["IMIA"]);
            $PROP["OTCHESTVO"]=encodeTJ2Win1251Ext($_POST["OTCHESTVO"]);  //mb_convert_encoding($_POST["OTCHESTVO"], "windows-1251", "utf-8");
            $PROP["SERIZAGRAN"]=encodeTJ2Win1251Ext($_POST["SERIZAGRAN"]); //mb_convert_encoding($_POST["SERIZAGRAN"], "windows-1251", "utf-8");
            $PROP["NOMERZAGRAN"]=encodeTJ2Win1251Ext($_POST["NOMERZAGRAN"]); //mb_convert_encoding($_POST["NOMERZAGRAN"], "windows-1251", "utf-8");
            $PROP["DATAVIDACHI"]=$_POST["DATAVIDACHI"];
            $PROP["KENVIDAN"]=encodeTJ2Win1251Ext($_POST["KENVIDAN"]); //mb_convert_encoding($_POST["KENVIDAN"], "windows-1251", "utf-8");
            $PROP["GORODREGISTRAZII"]=encodeTJ2Win1251Ext($_POST["GORODREGISTRAZII"]); //mb_convert_encoding($_POST["GORODREGISTRAZII"], "windows-1251", "utf-8");
            $PROP["INDEXREGISTRAZII"]=encodeTJ2Win1251Ext($_POST["INDEXREGISTRAZII"]); //mb_convert_encoding($_POST["INDEXREGISTRAZII"], "windows-1251", "utf-8");
            $PROP["MESTOREGISTRAZII"]=encodeTJ2Win1251Ext($_POST["MESTOREGISTRAZII"]); //mb_convert_encoding($_POST["MESTOREGISTRAZII"], "windows-1251", "utf-8");
            $PROP["GORODPROGIVANIA"]=encodeTJ2Win1251Ext($_POST["GORODPROGIVANIA"]); //mb_convert_encoding($_POST["GORODPROGIVANIA"], "windows-1251", "utf-8");
            $PROP["INDEXPROGIVANIA"]=encodeTJ2Win1251Ext($_POST["INDEXPROGIVANIA"]); //mb_convert_encoding($_POST["INDEXPROGIVANIA"], "windows-1251", "utf-8");
            $PROP["MESTOPROGIVANIA"]=encodeTJ2Win1251Ext($_POST["MESTOPROGIVANIA"]); //mb_convert_encoding($_POST["MESTOPROGIVANIA"], "windows-1251", "utf-8");
            $PROP["DOMTELEFON"]=encodeTJ2Win1251Ext($_POST["DOMTELEFON"]); //mb_convert_encoding($_POST["DOMTELEFON"], "windows-1251", "utf-8");
            $PROP["MOBILNII"]=encodeTJ2Win1251Ext($_POST["MOBILNII"]); //mb_convert_encoding($_POST["MOBILNII"], "windows-1251", "utf-8");
            $PROP["RABOCHII"]=encodeTJ2Win1251Ext($_POST["RABOCHII"]); //mb_convert_encoding($_POST["RABOCHII"], "windows-1251", "utf-8");
            $PROP["EMAIL"]=encodeTJ2Win1251Ext($_POST["EMAIL"]); //mb_convert_encoding($_POST["EMAIL"], "windows-1251", "utf-8");
            $PROP["DATAROGDENIA"]=$_POST["DATAROGDENIA"];


            if ($_POST["POL"] == 11)
                $_POST["POL"] = 19;
            if ($_POST["POL"] == 12)
                $_POST["POL"] = 20;

            $PROP["POL"]=$_POST["POL"];


            $arUserChanges = array(
                "IBLOCK_ID"      => get_iblock_id_by_code("strahovatelext"),
                "IBLOCK_SECTION_ID" => false,          // элемент лежит в корне раздела
                "ACTIVE"         => "Y",            // активен
                'NAME' => $PROP["IMIA"]." ".$PROP["FAMILIA"]." ".$PROP["OTCHESTVO"],
                'MODIFIED_BY' => $User,
                "PROPERTY_VALUES"=> $PROP,
                "CODE" => $DOGID,
            );


            $PRODUCT_ID = $el->Add($arUserChanges);
            if ($PRODUCT_ID > 0 ){
                $userUpdated = true;
            } else {
                $userUpdated = false;
            }

        } else {
            $userUpdated = false;
        }



        $arSelectProperty = Array(
            'TYPE',
            'NUMBER',
            'STATUS',
            'STAHOVATEL',
            'PARTNER',
            'USER',
            'OPLATA',
            'DATA_1',
            'DATA_2',
            'DATA_3',
            'NUMBER',
            'PAYED',
            'PAYDATE',
            'PASSED',
            'PASSDATE',
            'PERIOD',
            'CURRENCY',
            'FRANCHISE',
            'TERRITORY',
            'SHENGEN',
            'SPORT',
            'SUMM',
            'PREMIUM',
            'DATAEND',
            'PERIODDAYS',
            'DFRANCHISE',
            'CLAIM',
            'ESUMM',
            'EPREMIUM',
            'ACLEVEL'
        );

        // it1
        $arSelect = Array(
            'NAME',
            'PREVIEW_TEXT',
        );

        $arFilter = Array("IBLOCK_ID"=>10, "ID"=>$DOGID);
        //die(print_r($arSelect,1));
        $res = $el->GetList(Array('SORT'=>'ASC'), $arFilter, false, false, $arSelect);
        if($ob = $res->GetNextElement()){
            $arData = $ob->GetFields();

            foreach ($arSelectProperty as $sel_item) {
                $ssprop = CIBlockElement::GetProperty(10,$DOGID,array("sort" => "asc"),Array("CODE"=>$sel_item));
                if($ar_props = $ssprop->Fetch()){
                    $arData['PROPERTY_' . $sel_item . '_VALUE'] = $ar_props["VALUE"];
                }
            }
        } else {
            $arData =  array();
            echo $el->LAST_ERROR;
        }



        $arDogovorChanges = array();
        $arChanges = array();
        $arrPropertyChange = Array();
        $dogovorPrintedNumber = $arData['PROPERTY_NUMBER_VALUE'];

        // NAME encodeTJ2Win1251Ext($_POST[""]); //
        $contractName = "TP0 ";
        $contractName .= encodeTJ2Win1251Ext($_POST["FAMILIA"]);
        $contractName .= " ";
        $contractName .= encodeTJ2Win1251Ext($_POST["IMIA"]);
        $contractName .= " ";
        $contractName .= encodeTJ2Win1251Ext($_POST["OTCHESTVO"]);
        if($arData['NAME']!=$contractName)
            $arDogovorChanges['NAME'] = $contractName;

        // PREVIEW_TEXT
        if($arData['PREVIEW_TEXT']!=encodeTJ2Win1251Ext($_POST["COMMENT"]))
            $arDogovorChanges['PREVIEW_TEXT'] = encodeTJ2Win1251Ext($_POST["COMMENT"]);


        // payed
        if ($arData['PROPERTY_PAYED_VALUE']!=$_POST['PAYED']) {
            $arChanges['PAYED'] = $_POST['PAYED'];
            if($arData['PROPERTY_PAYED_VALUE']!=13 && $_POST['PAYED'] == 13 && !empty($_POST['PAYDATE'])) {
                $arChanges['PAYDATE'] = $_POST['PAYDATE'];
            }
        }

        // passed
        if ($arData['PROPERTY_PASSED_VALUE']!=$_POST['PASSED']) {
            $arChanges['PASSED'] = $_POST['PASSED'];
            if($arData['PROPERTY_PASSED_VALUE']!=16 && $_POST['PASSED'] == 16 && !empty($_POST['PASSDATE'])) {
                $arChanges['PASSDATE'] = $_POST['PASSDATE'];
            }
        }

        // PERIOD
        if($arData['PROPERTY_PERIOD_VALUE']!=$_POST['PERIOD'])
            $arChanges['PERIOD'] = $_POST['PERIOD'];

        // OPLATA
        if($arData['PROPERTY_OPLATA_VALUE']!=$_POST['SPOSOBOPLATI'])
            $arChanges['OPLATA'] = $_POST['SPOSOBOPLATI'];

        // DATAVILETA
        if($arData['PROPERTY_DATA_1_VALUE']!=$_POST['DATAVILETA'])
            $arChanges['DATA_1'] = $_POST['DATAVILETA'];

        if($arData['PROPERTY_CURRENCY_VALUE']!=$_POST['CURRENCY'])
            $arChanges['CURRENCY']	=	$_POST['CURRENCY'];

        if($arData['PROPERTY_FRANCHISE_VALUE']!=$_POST['FRANCHISE'])
            $arChanges['FRANCHISE']	=	$_POST['FRANCHISE'];


        //CLAIM
        if($arData['PROPERTY_CLAIM_VALUE']!=boolToYN($_POST['CLAIM']))
            $arChanges['CLAIM']	    =	boolToYN($_POST['CLAIM']);

        if($arData['PROPERTY_TERRITORY_VALUE']!=$_POST['TERRITORY'])
            $arChanges['TERRITORY']	=	$_POST['TERRITORY'];

        if($arData['PROPERTY_SHENGEN_VALUE']!=boolToYN($_POST['SHENGEN']))
            $arChanges['SHENGEN']	=	boolToYN($_POST['SHENGEN']);

        if($arData['PROPERTY_SPORT_VALUE']!=boolToYN($_POST['SPORT']))
            $arChanges['SPORT']	    =	boolToYN($_POST['SPORT']);

        /*if($arData['PROPERTY_SUMM_VALUE']!=$_POST['SUMM'])
            $arChanges['SUMM']	    =	$_POST['SUMM'];

        if($arData['PROPERTY_PREMIUM_VALUE']!=$_POST['PREMIUM'])
            $arChanges['PREMIUM']	=	$_POST['PREMIUM'];*/

        if($arData['PROPERTY_DATAEND_VALUE']!=$_POST['DATAEND'])
            $arChanges['DATAEND']	=	$_POST['DATAEND'];

        if($arData['PROPERTY_PERIODDAYS_VALUE']!=$_POST['PERIODDAYS'])
            $arChanges['PERIODDAYS'] =	$_POST['PERIODDAYS'];

        if($arData['PROPERTY_CLAIM_VALUE']!=$CLAIM)
            $arChanges['CLAIM'] = $CLAIM;

        if ($CLAIM == "Y") {
            $arChanges['ESUMM'] =	$_POST['ESUMM'];
            $arChanges['EPREMIUM'] =	$_POST['EPREMIUM'];
        }

        foreach($arChanges as $key=>$value) {
            $elUpd->SetPropertyValuesEx($DOGID, 10, array($key=>$value));
        }

        $arDogovorChanges['MODIFIED_BY'] = $User;
        $dogovorChanges = false;
        if (
            $elUpd->Update($DOGID , $arDogovorChanges)
            && (($userUpdated && $userCanges ) || ($userUpdated == false && $userCanges == false)))	{
            $dogovorChanges = true;
        }


        header('Content-type: application/json; charset=utf-8');
        if ($dogovorChanges){
            echo json_encode(
                array(
                    'status' => "ok",
                    'ID' => $DOGID,
                    'NUMBER' => $dogovorPrintedNumber
                ));
        } else {
            echo json_encode(
                array(
                    'status' => "bag",
                    'ID' => $DOGID,
                    'NUMBER' => $dogovorPrintedNumber
                ));
        }

    } else {

        // usually changes

        if ($userCanges) {

            //$family = encodeTJ2Win1251($_POST["FAMILIA"]);
            $strhID = intval($_POST['ID']);
            $PROP["FAMILIA"]= encodeTJ2Win1251($_POST["FAMILIA"]);  // mb_convert_encoding($_POST["FAMILIA"], "windows-1251", "utf-8");
            $PROP["IMIA"]=encodeTJ2Win1251($_POST["IMIA"]);
            $PROP["OTCHESTVO"]=encodeTJ2Win1251($_POST["OTCHESTVO"]);  //mb_convert_encoding($_POST["OTCHESTVO"], "windows-1251", "utf-8");
            $PROP["SERIZAGRAN"]=encodeTJ2Win1251($_POST["SERIZAGRAN"]); //mb_convert_encoding($_POST["SERIZAGRAN"], "windows-1251", "utf-8");
            $PROP["NOMERZAGRAN"]=encodeTJ2Win1251($_POST["NOMERZAGRAN"]); //mb_convert_encoding($_POST["NOMERZAGRAN"], "windows-1251", "utf-8");
            $PROP["DATAVIDACHI"]=$_POST["DATAVIDACHI"];
            $PROP["KENVIDAN"]=encodeTJ2Win1251($_POST["KENVIDAN"]); //mb_convert_encoding($_POST["KENVIDAN"], "windows-1251", "utf-8");
            $PROP["GORODREGISTRAZII"]=encodeTJ2Win1251($_POST["GORODREGISTRAZII"]); //mb_convert_encoding($_POST["GORODREGISTRAZII"], "windows-1251", "utf-8");
            $PROP["INDEXREGISTRAZII"]=encodeTJ2Win1251($_POST["INDEXREGISTRAZII"]); //mb_convert_encoding($_POST["INDEXREGISTRAZII"], "windows-1251", "utf-8");
            $PROP["MESTOREGISTRAZII"]=encodeTJ2Win1251($_POST["MESTOREGISTRAZII"]); //mb_convert_encoding($_POST["MESTOREGISTRAZII"], "windows-1251", "utf-8");
            $PROP["GORODPROGIVANIA"]=encodeTJ2Win1251($_POST["GORODPROGIVANIA"]); //mb_convert_encoding($_POST["GORODPROGIVANIA"], "windows-1251", "utf-8");
            $PROP["INDEXPROGIVANIA"]=encodeTJ2Win1251($_POST["INDEXPROGIVANIA"]); //mb_convert_encoding($_POST["INDEXPROGIVANIA"], "windows-1251", "utf-8");
            $PROP["MESTOPROGIVANIA"]=encodeTJ2Win1251($_POST["MESTOPROGIVANIA"]); //mb_convert_encoding($_POST["MESTOPROGIVANIA"], "windows-1251", "utf-8");
            $PROP["DOMTELEFON"]=encodeTJ2Win1251($_POST["DOMTELEFON"]); //mb_convert_encoding($_POST["DOMTELEFON"], "windows-1251", "utf-8");
            $PROP["MOBILNII"]=encodeTJ2Win1251($_POST["MOBILNII"]); //mb_convert_encoding($_POST["MOBILNII"], "windows-1251", "utf-8");
            $PROP["RABOCHII"]=encodeTJ2Win1251($_POST["RABOCHII"]); //mb_convert_encoding($_POST["RABOCHII"], "windows-1251", "utf-8");
            $PROP["EMAIL"]=encodeTJ2Win1251($_POST["EMAIL"]); //mb_convert_encoding($_POST["EMAIL"], "windows-1251", "utf-8");
            $PROP["POL"]=encodeTJ2Win1251($_POST["POL"]); //mb_convert_encoding($_POST["POL"], "windows-1251", "utf-8");
            $PROP["DATAROGDENIA"]=$_POST["DATAROGDENIA"];


            $arUserChanges = array(
                'NAME' => $PROP["IMIA"]." ".$PROP["FAMILIA"]." ".$PROP["OTCHESTVO"],
                "PROPERTY_VALUES"=> $PROP,
                'MODIFIED_BY' => $User,
            );

            if ($elUserUpd->Update($strhID, $arUserChanges)){
                $userUpdated = true;
            }
        } else {
            $userUpdated = false;
        }


        $arSelect = Array(
            'NAME',
            'PROPERTY_NUMBER',
            'PREVIEW_TEXT',
            'PROPERTY_TYPE',
            'PROPERTY_STATUS',
            'PROPERTY_STAHOVATEL',
            'PROPERTY_PARTNER',
            'PROPERTY_USER',
            'PROPERTY_OPLATA',
            'PROPERTY_DATA_1',
            'PROPERTY_DATA_2',
            'PROPERTY_DATA_3',
            'PROPERTY_NUMBER',
            'PROPERTY_PAYED',
            'PROPERTY_PAYDATE',
            'PROPERTY_PASSED',
            'PROPERTY_PASSDATE',
            'PROPERTY_PERIOD',
            'PROPERTY_CURRENCY',
            'PROPERTY_FRANCHISE',
            'PROPERTY_TERRITORY',
            'PROPERTY_SHENGEN',
            'PROPERTY_SPORT',
            'PROPERTY_SUMM',
            'PROPERTY_PREMIUM',
            'PROPERTY_DATAEND',
            'PROPERTY_PERIODDAYS',
            'PROPERTY_CLAIM',
            'PROPERTY_ESUMM',
            'PROPERTY_EPREMIUM',
        );

        $arFilter = Array("IBLOCK_ID"=>10, "ID"=>$DOGID);
        $res = $el->GetList(Array(), $arFilter, false, false, $arSelect);
        if($ob = $res->GetNextElement()){
            $arData = $ob->GetFields();
        } else {
            echo $el->LAST_ERROR;
        }



        $arDogovorChanges = array();
        $arChanges = array();
        $arrPropertyChange = Array();
        $dogovorPrintedNumber = $arData['PROPERTY_NUMBER_VALUE'];

        // NAME encodeTJ2Win1251($_POST[""]); //
        $contractName = "TP0 ";
        $contractName .= encodeTJ2Win1251($_POST["FAMILIA"]);
        $contractName = " ";
        $contractName .= encodeTJ2Win1251($_POST["IMIA"]);
        $contractName = " ";
        $contractName .= encodeTJ2Win1251($_POST["OTCHESTVO"]);
        if($arData['NAME']!=$contractName)
            $arDogovorChanges['NAME'] = $contractName;

        // PREVIEW_TEXT
        if($arData['PREVIEW_TEXT']!=encodeTJ2Win1251($_POST["COMMENT"]))
            $arDogovorChanges['PREVIEW_TEXT'] = encodeTJ2Win1251($_POST["COMMENT"]);


        // payed
        if ($arData['PROPERTY_PAYED_VALUE']!=$_POST['PAYED']) {
            $arChanges['PAYED'] = $_POST['PAYED'];
            if($arData['PROPERTY_PAYED_VALUE']!=13 && $_POST['PAYED'] == 13 && !empty($_POST['PAYDATE'])) {
                $arChanges['PAYDATE'] = $_POST['PAYDATE'];
            }
        }

        // passed
        if ($arData['PROPERTY_PASSED_VALUE']!=$_POST['PASSED']) {
            $arChanges['PASSED'] = $_POST['PASSED'];
            if($arData['PROPERTY_PASSED_VALUE']!=16 && $_POST['PASSED'] == 16 && !empty($_POST['PASSDATE'])) {
                $arChanges['PASSDATE'] = $_POST['PASSDATE'];
            }
        }

        // PERIOD
        if($arData['PROPERTY_PERIOD_VALUE']!=$_POST['PERIOD'])
            $arChanges['PERIOD'] = $_POST['PERIOD'];

        // OPLATA
        if($arData['PROPERTY_OPLATA_VALUE']!=$_POST['SPOSOBOPLATI'])
            $arChanges['OPLATA'] = $_POST['SPOSOBOPLATI'];

        // DATAVILETA
        if($arData['PROPERTY_DATA_1_VALUE']!=$_POST['DATAVILETA'])
            $arChanges['DATA_1'] = $_POST['DATAVILETA'];

        if($arData['PROPERTY_CURRENCY_VALUE']!=$_POST['CURRENCY'])
            $arChanges['CURRENCY']	=	$_POST['CURRENCY'];

        if($arData['PROPERTY_FRANCHISE_VALUE']!=$_POST['FRANCHISE'])
            $arChanges['FRANCHISE']	=	$_POST['FRANCHISE'];


        //CLAIM
        if($arData['PROPERTY_CLAIM_VALUE']!=boolToYN($_POST['CLAIM']))
            $arChanges['CLAIM']	    =	boolToYN($_POST['CLAIM']);

        if($arData['PROPERTY_TERRITORY_VALUE']!=$_POST['TERRITORY'])
            $arChanges['TERRITORY']	=	$_POST['TERRITORY'];

        if($arData['PROPERTY_SHENGEN_VALUE']!=boolToYN($_POST['SHENGEN']))
            $arChanges['SHENGEN']	=	boolToYN($_POST['SHENGEN']);

        if($arData['PROPERTY_SPORT_VALUE']!=boolToYN($_POST['SPORT']))
            $arChanges['SPORT']	    =	boolToYN($_POST['SPORT']);

       /* if($arData['PROPERTY_SUMM_VALUE']!=$_POST['SUMM'])
            $arChanges['SUMM']	    =	$_POST['SUMM'];*/

        /*if($arData['PROPERTY_PREMIUM_VALUE']!=$_POST['PREMIUM'])
            $arChanges['PREMIUM']	=	$_POST['PREMIUM'];*/

        if($arData['PROPERTY_DATAEND_VALUE']!=$_POST['DATAEND'])
            $arChanges['DATAEND']	=	$_POST['DATAEND'];

        if($arData['PROPERTY_PERIODDAYS_VALUE']!=$_POST['PERIODDAYS'])
            $arChanges['PERIODDAYS'] =	$_POST['PERIODDAYS'];

        $CLAIM = boolToYN($_POST['CLAIM']);

        if($arData['PROPERTY_CLAIM_VALUE']!=$CLAIM)
            $arChanges['CLAIM'] = $CLAIM;


        foreach($arChanges as $key=>$value) {
            $elUpd->SetPropertyValuesEx($DOGID, 10, array($key=>$value));
        }


        $arDogovorChanges['MODIFIED_BY'] = $User;
        $dogovorChanges = false;
        if (
            $elUpd->Update($DOGID , $arDogovorChanges)
            && (($userUpdated && $userCanges ) || ($userUpdated == false && $userCanges == false)))	{
            $dogovorChanges = true;

                $premium = getTotalPremiumSum($DOGID, true);
                CIBlockElement::SetPropertyValueCode($DOGID, "PREMIUM", $premium);
        }


        header('Content-type: application/json; charset=utf-8');
        if ($dogovorChanges){
            echo json_encode(
                array(
                    'status' => "ok",
                    'ID' => $DOGID,
                    'NUMBER' => $dogovorPrintedNumber
                ));
        } else {
            echo json_encode(
                array(
                    'status' => "bag",
                    'ID' => $DOGID,
                    'NUMBER' => $dogovorPrintedNumber
                ));
        }


        // \ usually changes
    }


} else {
	header('Content-type: application/json; charset=utf-8');
	echo json_encode(array('status' => "error"));
}?>