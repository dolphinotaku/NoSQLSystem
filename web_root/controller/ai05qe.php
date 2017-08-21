<?php

function ProcessData($requestData){
    $responseArray = Core::CreateResponseArray();
    $processMessageList = [];
    $courseManager = new CourseManager();
    $offerManager = new OfferManager();
    $enrolledManager = new EnrolledManager();

    // prepare select
    $mongoFilter = array();

    $selectOption = array('multi' => false, 'upsert' => false);

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

    // $mongoFilter = [
    //     "Year" => $year
    // ];

    $pipeline = [
        [
          '$lookup' =>
          [
            'from' => "Students",
            'localField' => "StudentID",
            'foreignField' => "StudentID",
            'as' => "student_docs",
          ]
        ],
        ['$unwind' => '$student_docs'],
        [
          '$lookup' =>
          [
            'from' => "Course",
            'localField' => "CourseID",
            'foreignField' => "CourseID",
            'as' => "offer_docs",
          ]
        ],
        ['$unwind' => '$offer_docs'],
        [
          '$lookup' =>
          [
            'from' => "Course",
            'localField' => "CourseID",
            'foreignField' => "CourseID",
            'as' => "courses_docs",
          ]
        ],
        ['$unwind' => '$courses_docs'],
        [
          '$match'=>[
            "offer_docs.DeptID" => "CS",
            "Year" => 2016,
            "student_docs.StuName" => "Chan Tai Man"         ]
        ]
    ];

    $enrollResponseArray = $enrolledManager->ExecuteCommand($pipeline);

    // print_r($enrollResponseArray);

    // if offer records found
    // if(!$enrollResponseArray['affected_rows'] > 0){
    //     array_push($processMessageList, "No records match.");
    //     $responseArray['processed_message'] = $processMessageList;
    //     return $responseArray;
    // }
    //
    // $courseRecordsList = array();
    // foreach ($enrollResponseArray['data'] as $index => $dataRow) {
    //     $courseManager->CourseID = $dataRow->_id;
    //     $courseResponseArray = $courseManager->select();
    //     if(!$courseResponseArray['affected_rows'] > 0){
    //         continue;
    //     }
    //     $courseRecordsList = array_merge($courseRecordsList, $courseResponseArray['data']);
    // }

    $responseArray['data'] = $enrollResponseArray['data'];
    $responseArray['queryResultDataList'] = $enrollResponseArray['data'];
    // $responseArray['mostCourseEnrollResultDataList'] = $enrollResponseArray;
//
//    $responseArray['processed_message'] = $processMessageList;
//    $responseArray['access_status'] = Core::$access_status['OK'];

    return $responseArray;
}

?>
