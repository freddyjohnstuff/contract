<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("iblock");
header('Content-type: application/json; charset=utf-8');
if(array($_POST))
{
    global $USER;
    $el = new CIBlockElement;
    $User=$USER->GetID();

    if(isset($_POST["hID"])){
        $ID = intval(base64_decode($_POST["hID"]));
        $arLoadProductArray = Array(
            "MODIFIED_BY"	 => $User
        );

        if ($el->Update($ID, $arLoadProductArray))	{
            $el->SetPropertyValuesEx($ID, 10, array('STATUS'=>'7'));


            echo json_encode(
                array(
                    'status' => "ok",
                    'ID' => $ID
                ));
        } else {
            echo json_encode(
                array(
                    'status' => "error",
                    'message' => $el->LAST_ERROR
                ));
        }

    }else{
        echo json_encode(array('status' => "error",'message' => "Bad Request"));
    }

    //echo '<pre>'; print_r($PROP); echo '</pre>';
} else {
    echo json_encode(array('status' => "error",'message' => "Bad Request"));
}?>