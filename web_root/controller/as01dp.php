<?php

function GetTableStructure(){
	$departmentManager = new DepartmentManager();
    return $departmentManager->selectPrimaryKeyList();
}

function CreateData($requestData){
	$departmentManager = new DepartmentManager();
    $responseArray = array();
	$responseArray = $departmentManager->CreateResponseArray();
    
	$createRows = new stdClass();
	$createRows = $requestData->Data->Header;
	foreach ($createRows as $keyIndex => $rowItem) {
		// $departmentManager->Initialize();

		foreach ($rowItem as $columnName => $value) {
			$departmentManager->$columnName = $value;
		}

		$responseArray = $departmentManager->Insert();
	}
	return $responseArray;
}

function FindData($requestData){
	$departmentManager = new DepartmentManager();

	$updateRows = new stdClass();
	$updateRows = $requestData->Data->Header;
    
	foreach ($updateRows as $keyIndex => $rowItem) {
        foreach ($rowItem as $columnName => $value) {
            $departmentManager->$columnName = $value;
        }
        $responseArray = $departmentManager->Find();
        break;
    }
    
	return $responseArray;
}

function GetData($requestData){
	$departmentManager = new DepartmentManager();
    
	$offsetRecords = 0;
	$offsetRecords = $requestData->Offset;
	$pageNum = $requestData->PageNum;

	$responseArray = $departmentManager->SelectPage($offsetRecords);
    
	return $responseArray;

}

function UpdateData($requestData){
	$departmentManager = new DepartmentManager();

	$updateRows = new stdClass();
	$updateRows = $requestData->Data->Header;
	foreach ($updateRows as $keyIndex => $rowItem) {
		foreach ($rowItem as $columnName => $value) {
			$departmentManager->$columnName = $value;
		}
        
		$responseArray = $departmentManager->Update();

	}
	return $responseArray;
}

function DeleteData($requestData){
	$departmentManager = new DepartmentManager();

	$deleteRows = new stdClass();
	$deleteRows = $requestData->Data->Header;
	foreach ($deleteRows as $keyIndex => $rowItem) {
		foreach ($rowItem as $columnName => $value) {
			$departmentManager->$columnName = $value;
		}
		$responseArray = $departmentManager->Delete();

	}
	return $responseArray;
}


?>