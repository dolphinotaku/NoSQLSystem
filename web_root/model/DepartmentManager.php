<?php

class DepartmentManager extends MongoDBManager {
    public $_ = array(
		// this Array structure By Initialize()
        // 'columnName1' => "value",
        // 'columnName2' => "value",
    );
	
	protected $table = "Departments";
    
    function __construct() {
		parent::__construct();
    }
    // function setArrayIndex(){
    // 	foreach ($this->_ as $key => $value) {
    // 		$this->$key = null;
    // 	}
    // 	print_r($this);
    // }

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
        		"Field" => "DeptName",
        		"Type" => "varchar(50)",
        		"Null" => "NO",
        		"Key" => null,
        		"Default" => null,
        		"Extra" => null),
        	array(
        		"Field" => "Location",
        		"Type" => "varchar(50)",
        		"Null" => "NO",
        		"Key" => null,
        		"Default" => null,
        		"Extra" => null)
        );
        
        $responseArray["KeyColumns"] = array("DeptID");

        return $responseArray;
    }

    public function getPrimaryKeyName(){
        $responseArray = array();
        
        $responseArray["data"] = array(
        	"Field"=>array(
        		"DeptID"
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