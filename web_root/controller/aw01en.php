<?php

function GetTableStructure(){
	$enrolledManager = new EnrolledManager();
    return $enrolledManager->selectPrimaryKeyList();
}

function FindData($requestData){
	$enrolledManager = new EnrolledManager();

	$updateRows = new stdClass();
	$updateRows = $requestData->Data->Header;
    
	foreach ($updateRows as $keyIndex => $rowItem) {
        foreach ($rowItem as $columnName => $value) {
            $enrolledManager->$columnName = $value;
        }
        $responseArray = $enrolledManager->Select();
        break;
    }
    
	return $responseArray;
}

function GetData($requestData){
	$enrolledManager = new EnrolledManager();
    
	$offsetRecords = 0;
	$offsetRecords = $requestData->Offset;
	$pageNum = $requestData->PageNum;

	$responseArray = $enrolledManager->selectPage($offsetRecords);
    
	return $responseArray;

}

?>