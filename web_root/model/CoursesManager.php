<?php

class CourseManager extends MongoDBManager {
    public $_ = array(
    );
	
	protected $table = "Courses";
    
    function __construct() {
		parent::__construct();
    }

    public function DescribeTableStructure(){
        $responseArray = array();
    	$responseArray = $this->CreateResponseArray();
        
        $responseArray["data"] = array(
        	array(
        		"Field" => "CourseID",
        		"Type" => "varchar(10)",
        		"Null" => "NO",
        		"Key" => "PRI",
        		"Default" => null,
        		"Extra" => null),
        	array(
        		"Field" => "Title",
        		"Type" => "varchar(100)",
        		"Null" => "NO",
        		"Key" => null,
        		"Default" => null,
        		"Extra" => null),
        	array(
        		"Field" => "Level",
        		"Type" => "varchar(50)",
        		"Null" => "NO",
        		"Key" => null,
        		"Default" => null,
        		"Extra" => null)
        );
        
        $responseArray["KeyColumns"] = array("CourseID");

        $responseArray["num_rows"] = 1;
        $responseArray["insert_id"] = 0;
        $responseArray["access_status"] = "OK";
        $responseArray["affected_rows"] = 1;

        return $responseArray;
    }

    public function getPrimaryKeyName(){
        $responseArray = array();
        
        $responseArray["data"] = array(
        	"Field"=>array(
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