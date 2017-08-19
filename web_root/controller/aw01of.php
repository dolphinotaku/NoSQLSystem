<?php

function GetTableStructure(){
	$offerManager = new OfferManager();
    return $offerManager->selectPrimaryKeyList();
}

function FindData($requestData){
	$offerManager = new OfferManager();

	$updateRows = new stdClass();
	$updateRows = $requestData->Data->Header;
    
	foreach ($updateRows as $keyIndex => $rowItem) {
        foreach ($rowItem as $columnName => $value) {
            $offerManager->$columnName = $value;
        }
        $responseArray = $offerManager->Select();
        break;
    }
    
	return $responseArray;
}

function GetData($requestData){
	$offerManager = new OfferManager();
    
	$offsetRecords = 0;
	$offsetRecords = $requestData->Offset;
	$pageNum = $requestData->PageNum;

	$responseArray = $offerManager->selectPage($offsetRecords);
    
	return $responseArray;

}

?>