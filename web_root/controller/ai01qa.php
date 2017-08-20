<?php

function ProcessData($requestData){
    $responseArray = Core::CreateResponseArray();
    $processMessageList = [];
    $offerManager = new OfferManager();
    $courseManager = new CourseManager();
    
    // get DeptID Criteria
    if(isset($requestData->Data->deptCriteria->DeptID)){
        $deptID = $requestData->Data->deptCriteria->DeptID;
    }else{
        array_push($processMessageList, "Department ID required.");
        $responseArray['processed_message'] = $processMessageList;
        return $responseArray;
    }
    
    // get Year Criteria
    if(isset($requestData->Data->Year)){
        $year = $requestData->Data->Year;
    }else{
        array_push($processMessageList, "Year required.");
        $responseArray['processed_message'] = $processMessageList;
        return $responseArray;
    }
    
    $offerManager->DeptID = $deptID;
    $offerManager->Year = $year;
    $offerResponseArray = $offerManager->select();
    
    // if offer records found
    if(!$offerResponseArray['affected_rows'] > 0){
        array_push($processMessageList, "No records match.");
        $responseArray['processed_message'] = $processMessageList;
        return $responseArray;
    }
    
    $courseRecordsList = array();
    foreach ($offerResponseArray['data'] as $index => $dataRow) {
        $courseManager->CourseID = $dataRow->CourseID;
        $courseResponseArray = $courseManager->select();
        if(!$courseResponseArray['affected_rows'] > 0){
            continue;
        }
        $courseRecordsList = array_merge($courseRecordsList, $courseResponseArray['data']);
    }
    
    $responseArray['data'] = $courseRecordsList;
    $responseArray['queryResultDataList'] = $courseRecordsList;
    
    $responseArray['processed_message'] = $processMessageList;
    $responseArray['access_status'] = Core::$access_status['OK'];

    return $responseArray;
}

?>