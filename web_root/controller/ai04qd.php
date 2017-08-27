<?php

/* MongoDB query
db.Enrolled.aggregate([
    {
        $group: {
            _id : { CourseID: "$CourseID", Year: "$Year"},
           count: { $sum: 1 }
        }
    },
    {
        $lookup: {
               from: "Offer",
               localField: "_id.CourseID",
               foreignField: "CourseID",
               as: "offer_docs"
             }
    },
    {$unwind: "$offer_docs"},
    {
        $match: {
            "offer_docs.DeptID": "CS",
            "_id.Year": 2016,
            "offer_docs.Year": 2016
        }
    },
    {
        $project: {
            "DeptID": "$offer_docs.DeptID",
            "CourseID": "$offer_docs.CourseID",
            "Year": "$offer_docs.Year",
            "ClassSize": "$offer_docs.ClassSize",
            "AvailablePlaces": "$offer_docs.AvailablePlaces",
            "count": "$count"
        }
    },
    {
        $sort: {
            "count": -1
        }
    }
])
*/
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
        [ '$group' =>
         [
             '_id' => ["CourseID" => '$CourseID', "Year" => '$Year'],
             "count" => ['$sum' => 1]
         ]
        ],
        [
          '$lookup' =>
          [
            'from' => "Offer",
            'localField' => "_id.CourseID",
            'foreignField' => "CourseID",
            'as' => "offer_docs",
          ]
        ],
        ['$unwind' => '$offer_docs'],
        [
          '$match' => [
            "offer_docs.DeptID" => $deptID,
            "_id.Year" => $year,
            "offer_docs.Year" => $year
          ]
        ],
        [
          '$project' => [
            "DeptID" => '$offer_docs.DeptID',
            "ClassSize" => '$offer_docs.ClassSize',
            "Year" => '$offer_docs.Year',
            "ClassSize" => '$offer_docs.ClassSize',
            "AvailablePlaces" => '$offer_docs.AvailablePlaces',
            "count" => '$count'
          ]
        ],
        [
            '$sort' => ["count" => -1]
        ],
    ];

    $enrollResponseArray = $enrolledManager->ExecuteCommand($pipeline);

//     print_r($enrollResponseArray);

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

    $responseArray['affected_rows'] = $enrollResponseArray['affected_rows'];
    $responseArray['num_rows'] = $enrollResponseArray['num_rows'];
    $responseArray['data'] = $enrollResponseArray['data'];
    $responseArray['queryResultDataList'] = $enrollResponseArray['data'];
    // $responseArray['mostCourseEnrollResultDataList'] = $enrollResponseArray;
//
//    $responseArray['processed_message'] = $processMessageList;
    $responseArray['access_status'] = Core::$access_status['OK'];

    return $responseArray;
}

?>
