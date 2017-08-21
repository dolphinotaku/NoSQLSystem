<?php

function ProcessData($requestData){
    $responseArray = Core::CreateResponseArray();
    $processMessageList = [];
    $courseManager = new CourseManager();
    $enrolledManager = new EnrolledManager();


    // prepare select
    $mongoFilter = array();

    $selectOption = array('multi' => false, 'upsert' => false);

    $pipeline = [
        [ '$match' => (object)$mongoFilter ],
//            [
//                '$limit' => 1
//            ],
        [
            '$sort' => ["count" => -1]
        ],
        [ '$group' =>
         [
             "_id" => '$CourseID',
             "count" => ['$sum' => 1]
         ]
        ]
    ];

    $enrollResponseArray = $enrolledManager->ExecuteCommand($pipeline);

    // if offer records found
    if(!$enrollResponseArray['affected_rows'] > 0){
        array_push($processMessageList, "No records match.");
        $responseArray['processed_message'] = $processMessageList;
        return $responseArray;
    }

    $courseRecordsList = array();
    foreach ($enrollResponseArray['data'] as $index => $dataRow) {
        $courseManager->CourseID = $dataRow->_id;
        $courseResponseArray = $courseManager->select();
        if(!$courseResponseArray['affected_rows'] > 0){
            continue;
        }
        $courseRecordsList = array_merge($courseRecordsList, $courseResponseArray['data']);
    }
//
    $responseArray['data'] = $courseRecordsList;
    $responseArray['queryResultDataList'] = $courseRecordsList;
    $responseArray['mostCourseEnrollResultDataList'] = $enrollResponseArray;
//
//    $responseArray['processed_message'] = $processMessageList;
//    $responseArray['access_status'] = Core::$access_status['OK'];

    return $responseArray;
}

?>
