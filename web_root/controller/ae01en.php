<?php

function GetTableStructure(){
	$enrolledManager = new EnrolledManager();
    return $enrolledManager->selectPrimaryKeyList();
}

function CreateData($requestData){
	$enrolledManager = new EnrolledManager();
    $responseArray = array();
	$responseArray = $enrolledManager->CreateResponseArray();
    
	$createRows = new stdClass();
	$createRows = $requestData->Data->Header;
	foreach ($createRows as $keyIndex => $rowItem) {
		// $enrolledManager->Initialize();

		foreach ($rowItem as $columnName => $value) {
			$enrolledManager->$columnName = $value;
		}

		$responseArray = $enrolledManager->Insert();
	}
	return $responseArray;
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

function UpdateData($requestData){
	$enrolledManager = new EnrolledManager();

	$updateRows = new stdClass();
	$updateRows = $requestData->Data->Header;
	foreach ($updateRows as $keyIndex => $rowItem) {
		foreach ($rowItem as $columnName => $value) {
			$enrolledManager->$columnName = $value;
		}
        
		$responseArray = $enrolledManager->Update();

	}
	return $responseArray;
}

function DeleteData($requestData){
	$enrolledManager = new EnrolledManager();

	$deleteRows = new stdClass();
	$deleteRows = $requestData->Data->Header;
	foreach ($deleteRows as $keyIndex => $rowItem) {
		foreach ($rowItem as $columnName => $value) {
			$enrolledManager->$columnName = $value;
		}
		$responseArray = $enrolledManager->Delete();

	}
	return $responseArray;
}


?>