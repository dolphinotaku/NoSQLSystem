<?php

class OfferManager extends MongoDBManager {
    public $_ = array(
    );
	
	protected $table = "Offer";
    
    function __construct() {
		parent::__construct();
    }

    public function DescribeTableStructure(){
        $responseArray = array();
    	$responseArray = $this->CreateResponseArray();
        
        $responseArray["data"] = array(
        	array(
        		"Field" => "DeptID",
        		"Type" => "varchar(10)",
        		"Null" => "NO",
        		"Key" => "PRI",
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
        		"Field" => "Year",
        		"Type" => "int(4)",
        		"Null" => "NO",
        		"Key" => null,
        		"Default" => null,
        		"Extra" => null),
        	array(
        		"Field" => "ClassSize",
        		"Type" => "int(3)",
        		"Null" => "NO",
        		"Key" => null,
        		"Default" => null,
        		"Extra" => null),
        	array(
        		"Field" => "AvailablePlaces",
        		"Type" => "int(3)",
        		"Null" => "NO",
        		"Key" => null,
        		"Default" => null,
        		"Extra" => null)
        );
        
        $responseArray["KeyColumns"] = array("DeptID", "CourseID");

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
                "DeptID",
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