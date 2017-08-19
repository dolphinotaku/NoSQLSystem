<?php

function GetTableStructure(){
	$studentManager = new StudentManager();
    return $studentManager->selectPrimaryKeyList();
}

function CreateData($requestData){
	$studentManager = new StudentManager();
    $responseArray = array();
	$responseArray = $studentManager->CreateResponseArray();
    
	$createRows = new stdClass();
	$createRows = $requestData->Data->Header;
	foreach ($createRows as $keyIndex => $rowItem) {
		// $studentManager->Initialize();

		foreach ($rowItem as $columnName => $value) {
			$studentManager->$columnName = $value;
		}

		$responseArray = $studentManager->Insert();
	}
	return $responseArray;
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

function UpdateData($requestData){
	$studentManager = new StudentManager();

	$updateRows = new stdClass();
	$updateRows = $requestData->Data->Header;
	foreach ($updateRows as $keyIndex => $rowItem) {
		foreach ($rowItem as $columnName => $value) {
			$studentManager->$columnName = $value;
		}
        
		$responseArray = $studentManager->Update();

	}
	return $responseArray;
}

function DeleteData($requestData){
	$studentManager = new StudentManager();

	$deleteRows = new stdClass();
	$deleteRows = $requestData->Data->Header;
	foreach ($deleteRows as $keyIndex => $rowItem) {
		foreach ($rowItem as $columnName => $value) {
			$studentManager->$columnName = $value;
		}
		$responseArray = $studentManager->Delete();

	}
	return $responseArray;
}


?>