<?php

function GetTableStructure(){
	$courseManager = new CourseManager();
    return $courseManager->selectPrimaryKeyList();
}

function FindData($requestData){
	$courseManager = new CourseManager();

	$updateRows = new stdClass();
	$updateRows = $requestData->Data->Header;
    
	foreach ($updateRows as $keyIndex => $rowItem) {
        foreach ($rowItem as $columnName => $value) {
            $courseManager->$columnName = $value;
        }
        $responseArray = $courseManager->Select();
        break;
    }
    
	return $responseArray;
}

function GetData($requestData){
	$courseManager = new CourseManager();
    
	$offsetRecords = 0;
	$offsetRecords = $requestData->Offset;
	$pageNum = $requestData->PageNum;

	$responseArray = $courseManager->selectPage($offsetRecords);
    
	return $responseArray;

}

?>