<?php

class StudentManager extends MongoDBManager {
    public $_ = array(
    );
	
	protected $table = "Students";
    
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
        		"Field" => "StuName",
        		"Type" => "varchar(10)",
        		"Null" => "NO",
        		"Key" => "PRI",
        		"Default" => null,
        		"Extra" => null),
        	array(
        		"Field" => "DOB",
        		"Type" => "date",
        		"Null" => "NO",
        		"Key" => null,
        		"Default" => null,
        		"Extra" => null)
        );
        
        $responseArray["KeyColumns"] = array("StudentID");

        return $responseArray;
    }

    public function getPrimaryKeyName(){
        $responseArray = array();
        
        $responseArray["data"] = array(
        	"Field"=>array(
                "StudentID"
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