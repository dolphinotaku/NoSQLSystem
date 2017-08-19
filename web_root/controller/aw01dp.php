<?php

function GetTableStructure(){
	$departmentManager = new DepartmentManager();
    return $departmentManager->selectPrimaryKeyList();
}

function FindData($requestData){
	$departmentManager = new DepartmentManager();

	$updateRows = new stdClass();
	$updateRows = $requestData->Data->Header;
    
	foreach ($updateRows as $keyIndex => $rowItem) {
        foreach ($rowItem as $columnName => $value) {
            $departmentManager->$columnName = $value;
        }
        $responseArray = $departmentManager->Select();
        break;
    }
    
	return $responseArray;
}

function GetData($requestData){
	$departmentManager = new DepartmentManager();
    
	$offsetRecords = 0;
	$offsetRecords = $requestData->Offset;
	$pageNum = $requestData->PageNum;

	$responseArray = $departmentManager->selectPage($offsetRecords);
    
	return $responseArray;

}

?>