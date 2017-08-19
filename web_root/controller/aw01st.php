<?php

function GetTableStructure(){
	$studentManager = new StudentManager();
    return $studentManager->selectPrimaryKeyList();
}

function FindData($requestData){
	$studentManager = new StudentManager();

	$updateRows = new stdClass();
	$updateRows = $requestData->Data->Header;
    
	foreach ($updateRows as $keyIndex => $rowItem) {
        foreach ($rowItem as $columnName => $value) {
            $studentManager->$columnName = $value;
        }
        $responseArray = $studentManager->Select();
        break;
    }
    
	return $responseArray;
}

function GetData($requestData){
	$studentManager = new StudentManager();
    
	$offsetRecords = 0;
	$offsetRecords = $requestData->Offset;
	$pageNum = $requestData->PageNum;

	$responseArray = $studentManager->selectPage($offsetRecords);
    
	return $responseArray;

}

?>