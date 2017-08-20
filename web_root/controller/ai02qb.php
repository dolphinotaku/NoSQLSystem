<?php

function ProcessData($requestData){
    $responseArray = Core::CreateResponseArray();
    $processMessageList = [];
    $offerManager = new OfferManager();
    $courseManager = new CourseManager();
    
    
    // prepare select
    $mongoFilter = array();

    $selectOption = array('multi' => false, 'upsert' => false);
    
    $mongoFilter = [
        "DeptID" => ['$in'=>["CS", "IS"]],
        "Year" => 2016
    ];

    $offerResponseArray = $offerManager->ExecuteQuery($mongoFilter, $selectOption);

    
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