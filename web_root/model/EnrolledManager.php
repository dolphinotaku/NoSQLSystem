<?php

class EnrolledManager extends MongoDBManager {
    public $_ = array(
    );
	
	protected $table = "Enrolled";
    
    function __construct() {
		parent::__construct();
    }

    public function DescribeTableStructure(){
        $responseArray = array();
    	$responseArray = $this->CreateResponseArray();
        
        $responseArray["data"] = array(
        	array(
        		"Field" => "StudentID",
        		"Type" => "varchar(10)",
        		"Null" => "NO",
        		"Key" => "PRI",
        		"Default" => null,
        		"Extra" => null),
        	array(
        		"Field" => "Year",
        		"Type" => "int(4)",
        		"Null" => "NO",
        		"Key" => "null",
        		"Default" => null,
        		"Extra" => null),
        	array(
        		"Field" => "CourseID",
        		"Type" => "varchar(10)",
        		"Null" => "NO",
        		"Key" => "PRI",
        		"Default" => null,
        		"Extra" => null),
        	array(
        		"Field" => "EnrolDate",
        		"Type" => "date",
        		"Null" => "NO",
        		"Key" => null,
        		"Default" => null,
        		"Extra" => null)
        );
        
        $responseArray["KeyColumns"] = array("StudentID", "CourseID");

        return $responseArray;
    }

    public function getPrimaryKeyName(){
        $responseArray = array();
        
        $responseArray["data"] = array(
        	"Field"=>array(
                "StudentID",
                "CourseID"
        	)
        );

        return $responseArray;
    }
    
	function SetDefaultValue(){
		parent::setDefaultValue();
	}
    
    function __isset($name) {
        return isset($this->_[$name]);
    }
}

?>