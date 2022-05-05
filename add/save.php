<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("iblock");
global $USER;
if(!$USER->IsAuthorized()) {
    echo json_encode(
        array(
            'status' => "error",
            'message' => "Unauthorized"
        ));
    exit;
}
if(is_array($_POST) && isset($_POST['DOGID']))
{
	$el = new CIBlockElement;
	$elpolzovatel = new CIBlockElement;
	$PROP = array();
	$PROP["TYPE"]=get_element_id_by_codes("TP0","dogtype");



    $PROP["USER"]=$USER->GetID();
    if ($_POST["accessLevel"]==ACCESS_USER) {
        $PROP["PARTNER"] = getPartnerIdByUserId($PROP["USER"]);
    } elseif ($_POST["accessLevel"]==ACCESS_PARTNER) {
        $PROP["PARTNER"] = $PROP["USER"];
    }

	$PROP["STATUS"]=5;
	$PROP["ACLEVEL"]=$_POST["accessLevel"];
	$PROP["OPLATA"]=$_POST["SPOSOBOPLATI"];
    $PROP["DATA_1"]=$_POST["DATAVILETA"];
    $PROP["PERIOD"]=$_POST["PERIOD"];


    $PROP["CURRENCY"]=$_POST["CURRENCY"];
    $PROP["FRANCHISE"]=$_POST["FRANCHISE"];
    $PROP["CLAIM"]=boolToYN($_POST["CLAIM"]);
    $PROP["TERRITORY"]=$_POST["TERRITORY"];
    $PROP["SHENGEN"]=boolToYN($_POST["SHENGEN"]);
    $PROP["SPORT"]=boolToYN($_POST["SPORT"]);

    $PROP["DATAEND"]=$_POST["DATAEND"];
    $PROP["PERIODDAYS"]=$_POST["PERIODDAYS"];

    //echo '<pre>'; print_r($PROP); echo '</pre>';

    // NAME encodeTJ2Win1251($_POST[""]); //
    $contractName = "TP0 ";
    $contractName .= encodeTJ2Win1251($_POST["FAMILIA"]);
    $contractName = " ";
    $contractName .= encodeTJ2Win1251($_POST["IMIA"]);
    $contractName = " ";
    $contractName .= encodeTJ2Win1251($_POST["OTCHESTVO"]);


    if($_POST["ID"]>0){
        $PROP["STAHOVATEL"] = $_POST["ID"];
        $arLoadProductArray = array(
            'NAME' => $contractName,
            "PROPERTY_VALUES"=> $PROP,
            'MODIFIED_BY' => $USER->GetID(),
            "ACTIVE"      => "Y",
            "PREVIEW_TEXT" => encodeTJ2Win1251($_POST['COMMENT'])
        );

        if ($el -> Update(intval($_POST['DOGID']), $arLoadProductArray)){
            $ID = $_POST['DOGID'];

                $premium = getTotalPremiumSum($DOGID, true);
                CIBlockElement::SetPropertyValueCode($DOGID, "PREMIUM", $premium);
        }

    } else{

        $PROP["FAMILIA"]=encodeTJ2Win1251($_POST["FAMILIA"]);
        $PROP["IMIA"]=encodeTJ2Win1251($_POST["IMIA"]);
        $PROP["OTCHESTVO"]=encodeTJ2Win1251($_POST["OTCHESTVO"]);
        $PROP["SERIZAGRAN"]=encodeTJ2Win1251($_POST["SERIZAGRAN"]);
        $PROP["NOMERZAGRAN"]=encodeTJ2Win1251($_POST["NOMERZAGRAN"]);
        $PROP["DATAVIDACHI"]=$_POST["DATAVIDACHI"];
        $PROP["KENVIDAN"]=encodeTJ2Win1251($_POST["KENVIDAN"]);
        $PROP["GORODREGISTRAZII"]=encodeTJ2Win1251($_POST["GORODREGISTRAZII"]);
        $PROP["INDEXREGISTRAZII"]=encodeTJ2Win1251($_POST["INDEXREGISTRAZII"]);
        $PROP["MESTOREGISTRAZII"]=encodeTJ2Win1251($_POST["MESTOREGISTRAZII"]);
        $PROP["GORODPROGIVANIA"]=encodeTJ2Win1251($_POST["GORODPROGIVANIA"]);
        $PROP["INDEXPROGIVANIA"]=encodeTJ2Win1251($_POST["INDEXPROGIVANIA"]);
        $PROP["MESTOPROGIVANIA"]=encodeTJ2Win1251($_POST["MESTOPROGIVANIA"]);
        $PROP["DOMTELEFON"]=encodeTJ2Win1251($_POST["DOMTELEFON"]);
        $PROP["MOBILNII"]=encodeTJ2Win1251($_POST["MOBILNII"]);
        $PROP["RABOCHII"]=encodeTJ2Win1251($_POST["RABOCHII"]);
        $PROP["EMAIL"]=encodeTJ2Win1251($_POST["EMAIL"]);
        $PROP["POL"]=encodeTJ2Win1251($_POST["POL"]);
        $PROP["DATAROGDENIA"]=$_POST["DATAROGDENIA"];

        $personName = encodeTJ2Win1251($_POST["FAMILIA"]);
        $personName .= " ";
        $personName .= encodeTJ2Win1251($_POST["IMIA"]);
        $personName .= " ";
        $personName .= encodeTJ2Win1251($_POST["OTCHESTVO"]);

        $arLoadProductArray = Array(
			"IBLOCK_SECTION" => false,
			"IBLOCK_ID"      => 11,
			"PROPERTY_VALUES"=> $PROP,
			"NAME"           => $personName,
			"ACTIVE"         => "Y",
		);	
		$IDstrahovatel = $el -> Add($arLoadProductArray);	
		
		if($IDstrahovatel>0){
			$PROP["STAHOVATEL"]=$IDstrahovatel;
            $arLoadProductArray = array(
                'NAME' => $PROP["IMIA"]." ".$PROP["FAMILIA"]." ".$PROP["OTCHESTVO"],
                "ACTIVE"         => "Y",
                "PROPERTY_VALUES"=> $PROP,
                'MODIFIED_BY' => $USER->GetID(),
                "PREVIEW_TEXT"    => encodeTJ2Win1251($_POST['COMMENT'])
            );

			if ($el -> Update(intval($_POST['DOGID']), $arLoadProductArray)) {
                $ID = intval($_POST['DOGID']);
                    $premium = getTotalPremiumSum($DOGID, true);
                    CIBlockElement::SetPropertyValueCode($DOGID, "PREMIUM", $premium);
            }

		}
		else{
			$er=$elpolzovatel -> LAST_ERROR;
		}

		//echo '<pre>'; print_r($er); echo '</pre>';
		//echo '<pre>'; print_r($ProUser["DATAVIDACHI"]); echo '</pre>';
		//echo '<pre>'; print_r($ProUser["DATAROGDENIA"]); echo '</pre>';
	}


	header('Content-type: application/json; charset=utf-8');
	if ($ID>0 && $PROP["USER"] && $PROP["PARTNER"])	{

            //$uid = $USER->GetID();
            //$hash = md5($uid . date('Y-m-d'));
            //$strSql = sprintf("UPDATE `risk_cache` SET `processed` = 1  WHERE `contract_id` = '%d';", $ID);
            //die($strSql);
            //if($DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__)){
                //echo json_encode(array('status' => "ok", "id" => $_POST['id']));
            //} else {
                //echo json_encode(array('status' => "error", 'message'=> $DB->GetErrorMessage()));
            //}


        echo json_encode(
			array(
				'status' => "ok",
				'ID' => $ID
		));
	}
	else{
		echo json_encode(
			array(
				'status' => "bag",
				'ID' => $ID
		));	
	}
	//echo '<pre>'; print_r($PROP); echo '</pre>';
}	
else{
	header('Content-type: application/json; charset=utf-8');
		echo json_encode(
			array(
				'status' => "error",
			));
}?>