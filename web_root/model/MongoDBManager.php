<?php

require_once 'Core.php';

class MongoDBManager {
    protected $hostname_fyp;
    protected $database_fyp;

	protected $dataSchema;
	protected $dataSchemaCSharp;

    protected $mongoDBManager;
    
    function __construct() {
    	$this->ConnectMongoDB();
		$this->Initialize();
    }

    function ConnectMongoDB(){
		$this->hostname_fyp = _MONGO_DB_HOST;
		$this->database_fyp = _MONGO_DB_NAME;
    	$this->mongoDBManager = new MongoDB\Driver\Manager("mongodb://".$this->hostname_fyp);
    }

    function GetMongoDBConnection(){
    	return $this->mongoDBManager;
    }

    function Initialize(){
    	$this->_ = array();
		$this->dataSchema = array();
		$this->dataSchemaCSharp = array();

		$this->setDataSchemaForSet();
		$this->setArrayIndex();
    }
    
    
	
	public function Insert(){
		// prepare insert
		$bulk = $this->GetBulkWrite();

		// $document1 = [
		// 	'DeptID' => 'CS',
		// 	'DeptName' => 'Computer Science',
		// 	'Location' => 'Green Zone'
		// ];

		$document1 = array();
		foreach ($this->_ as $key => $value) {
			$document1[$key] = $value;
		}

		$_id1 = $bulk->insert($document1);

		// var_dump($document1);
		// var_dump($_id1);

		$result = $this->BulkWrite($bulk);

		// print_r($result);

		return $result;
	}
	
	public function Update(){
		// prepare update
		$bulk = $this->GetBulkWrite();
		$mongoFilter = array();
		$recordArray = $this->_;

		$primaryKeySchema = $this->getPrimaryKeyName();

		$document1 = array();
		$updateOption = array('multi' => false, 'upsert' => false);

		// prepare the $mongoFilter, same as WHERE clause(condition) in SQL
		foreach ($primaryKeySchema['data']['Field'] as $index => $columnName){
			$value = $recordArray[$columnName];
			if($this->IsNullOrEmptyString($value)){
			}else{
				$mongoFilter[$columnName] = $value;
			}
		}

		// prepare the $document1, same as SET keyword in update SQL statement
		// foreach ($recordArray as $key => $value) {
		// 	$document1[$key] = $value;
		// }
		$isAllColumnNullOrEmpty = true;
		$nullOrEmptyColumn = "";
		foreach ($recordArray as $columnName => $value) {
			if($this->IsSystemField($columnName)){
				continue;
			}
            
            // 20170221, keithpoon, fixed: if the fk was null, don't assign empty
            if(!isset($value))
                continue;
            
			$isColumnAPK = array_search($columnName, $primaryKeySchema['data']['Field']);
			// array_search return key index if found, false otherwise
			if($isColumnAPK === false){

				$isAllColumnNullOrEmpty = $isAllColumnNullOrEmpty && $this->IsNullOrEmptyString($value);

				if(!$this->IsNullOrEmptyString($value)){
					$document1[$columnName] = $this->GetSQLValueString($columnName);
				}else{
                    // 20170111, keithpoon, also allowed to assign empty, if the user want to update the record from text to empty
					$document1[$columnName] = "";
				}
			}
		}

		// $bulk->insert(['_id' => 1, 'x' => 1]);
		// $bulk->insert(['_id' => 2, 'x' => 2]);
		// $bulk->update(['x' => 2], ['$set' => ['x' => 1]]);
		// $bulk->insert(['_id' => 3, 'x' => 3]);
		// $bulk->delete(['x' => 1]);

		$_id1 = $bulk->update(
		    $mongoFilter,
		    ['$set' => $document1],
		    $updateOption
		);

		$result = $this->BulkWrite($bulk);

		// print_r($result);

		return $result;
	}
	
	public function Delete(){
		// prepare delete
		$bulk = $this->GetBulkWrite();
		$mongoFilter = array();
		$recordArray = $this->_;

		$primaryKeySchema = $this->getPrimaryKeyName();

		$deleteOption = array('multi' => false, 'upsert' => false);

		// prepare the $mongoFilter, same as WHERE clause(condition) in SQL
		foreach ($primaryKeySchema['data']['Field'] as $index => $columnName){
			$value = $recordArray[$columnName];
			if($this->IsNullOrEmptyString($value)){
			}else{
				$mongoFilter[$columnName] = $value;
			}
		}

		$_id1 = $bulk->delete(
		    $mongoFilter,
		    $deleteOption
		);

		$result = $this->BulkWrite($bulk);

		return $result;
	}

	public function Select(){
		// prepare select
		$bulk = $this->GetBulkWrite();
		$mongoFilter = array();
		$recordArray = $this->_;
		$dataSchema = $this->dataSchema;

		$primaryKeySchema = $this->getPrimaryKeyName();

		$selectOption = array('multi' => false, 'upsert' => false);

		// prepare the $mongoFilter, same as WHERE clause(condition) in SQL
		foreach ($recordArray as $columnName => $value) {
            if(!isset($value))
                continue;
            

//			if(isset($this->SearchDataType($dataSchema['data'], 'Field', $index)[0]['Default']))
//				if ($value == $this->SearchDataType($dataSchema['data'], 'Field', $index)[0]['Default'])
//					continue;

            $mongoFilter[$columnName] = $value;
		}

		$result = $this->ExecuteQuery($mongoFilter, $selectOption);

		return $result;
	}
    
    public function Find(){
        return $this->SelectPage(0, 1);
    }

	public function SelectPage($tempOffset = 1, $tempLimit = 10){
		// prepare select
		$bulk = $this->GetBulkWrite();
		$mongoFilter = array();
		$recordArray = $this->_;
		$dataSchema = $this->dataSchema;
        
		$primaryKeySchema = $this->getPrimaryKeyName();

		$selectOption = array(
			'multi' => false,
			'upsert' => false,
			'skip' => $tempOffset,
			'limit' => $tempLimit);
        $selectOption = array();

		// prepare the $mongoFilter, same as WHERE clause(condition) in SQL
		foreach ($recordArray as $columnName => $value) {
            if(!isset($value))
                continue;
            

//			if(isset($this->SearchDataType($dataSchema['data'], 'Field', $index)[0]['Default']))
//				if ($value == $this->SearchDataType($dataSchema['data'], 'Field', $index)[0]['Default'])
//					continue;
            
            // search for documents where 5 < x < 20
            // column "x" = array( '$gt' => 5, '$lt' => 20 )
//			$mongoFilter[$columnName] = array('$gt' => $value);
            $mongoFilter[$columnName] = $value;
		}
        
//        var_dump($mongoFilter);
//        var_dump($selectOption);

		$result = $this->ExecuteQuery($mongoFilter, $selectOption);

		$mongoDBResult = $result["mongoDBResult"];
		$mongoDBResultDataArray = $mongoDBResult->toArray();
        
//        print_r($mongoDBResult);
//        print_r($mongoDBResultDataArray);
        
		$result["data"] = $mongoDBResultDataArray;
		$result["affected_rows"] = count($mongoDBResultDataArray);
		$result["num_rows"] = count($mongoDBResultDataArray);

		return $result;
	}
    
    protected function GetDescribeTableStructureResult(){
        $responseArray = $this->DescribeTableStructure();
        
        $rowsCount = count($responseArray["data"]);

        $responseArray["num_rows"] = $rowsCount;
        $responseArray["insert_id"] = 0;
        $responseArray["access_status"] = "OK";
        $responseArray["affected_rows"] = $rowsCount;
        
        return $responseArray;
    }
    
    
    function setDataSchemaForSet($isClearResponseArrayDataIndex = false){
		$this->dataSchema = $this->GetDescribeTableStructureResult();

		// extract data schema to increase readability
		$colDetailsIndex = array(
			"type", 
			"length", 
			"decimalPoint", 
			"null", 
			"key", 
			"default",
			"extra"
		);

		// Start - build the high readability dataSchema
		$this->dataSchemaCSharp = array();
		if(isset($this->dataSchema) && !empty($this->dataSchema["data"]))
		foreach($this->dataSchema["data"] as $arrayIndex => $columnDetails){
			$columnName = $columnDetails['Field'];
			$this->dataSchemaCSharp[$columnName] = array();
			
			$colType = $columnDetails['Type'];
				
			$tempColType = explode("(", $colType);
			
			if(isset($tempColType[0]))
				$colType = $tempColType[0];
			if(isset($tempColType[1]))
				$colLength = substr($tempColType[1], 0, strlen($tempColType[1])-1);
				
			$tempColLength = explode(",", $colLength);
			
			if(isset($tempColLength[0]))
				$colLength = $tempColLength[0];
			if(isset($tempColLength[1]))
				$colDecimalPoint = $tempColLength[1];
			else
				$colDecimalPoint = Null;
			
			foreach ($colDetailsIndex as $newArrayIndex){
				$this->dataSchemaCSharp[$columnName][$newArrayIndex] = null;
				
				switch ($newArrayIndex){
					case "type":
						$this->dataSchemaCSharp[$columnName][$newArrayIndex] = $colType;
						break;
					case "length":
						$this->dataSchemaCSharp[$columnName][$newArrayIndex] = $colLength;
						break;
					case "decimalPoint":
						$this->dataSchemaCSharp[$columnName][$newArrayIndex] = $colDecimalPoint;
						break;
					case "null":
						$this->dataSchemaCSharp[$columnName][$newArrayIndex] = $columnDetails['Null'];
						break;
					case "key":
						$this->dataSchemaCSharp[$columnName][$newArrayIndex] = $columnDetails['Key'];
						break;
					case "default":
						$this->dataSchemaCSharp[$columnName][$newArrayIndex] = $columnDetails['Default'];
						break;
					case "extra":
						$this->dataSchemaCSharp[$columnName][$newArrayIndex] = $columnDetails['Extra'];
						break;
				}
			}
		}
		// End - high readability dataSchema builded

		if($isClearResponseArrayDataIndex)
			$this->responseArray["data"] = array();
    }
    
    function setArrayIndex(){
		$dataSchema = $this->dataSchema['data'];
		if(!empty($dataSchema)){
			foreach($dataSchema as $index=>$value){
				$this->_[$value['Field']] = NULL;
			}
		}
    }

    public function CreateResponseArray(){
    	return Core::CreateResponseArray();
    }

    public function getPrimaryKeyName(){
    }

    public function selectPrimaryKeyList(){
        $responseArray = array();
    	$responseArray = $this->CreateResponseArray();

        $responseArray = $this->getPrimaryKeyName();
        
        $responseArray["DataColumns"] = $this->dataSchemaCSharp;
        
        $responseArray["num_rows"] = count($responseArray["DataColumns"]);
        $responseArray["insert_id"] = 0;
        $responseArray["access_status"] = "OK";
        $responseArray["affected_rows"] = count($responseArray["DataColumns"]);
        
        $responseArray["KeyColumns"] = $responseArray["data"]["Field"];

        return $responseArray;
    }

    function GetBulkWrite(){
		$bulk = new MongoDB\Driver\BulkWrite(['ordered' => true]);;
		return $bulk;
    }
	
	public function BulkWrite($bulk){
		$result;
		$dbName = $this->database_fyp;
		$tableName = $this->table;
		$manager = $this->mongoDBManager;
		$writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);

		try {
		    $result = $manager->executeBulkWrite($dbName.'.'.$tableName, $bulk, $writeConcern);
		} catch (MongoDB\Driver\Exception\BulkWriteException $e) {
		    $result = $e->getWriteResult();

		    // Check if the write concern could not be fulfilled
		    if ($writeConcernError = $result->getWriteConcernError()) {
		        printf("%s (%d): %s\n",
		            $writeConcernError->getMessage(),
		            $writeConcernError->getCode(),
		            var_export($writeConcernError->getInfo(), true)
		        );
		    }

		    // Check if any write operations did not complete at all
		    foreach ($result->getWriteErrors() as $writeError) {
		        printf("Operation#%d: %s (%d)\n",
		            $writeError->getIndex(),
		            $writeError->getMessage(),
		            $writeError->getCode()
		        );
		    }
		} catch (MongoDB\Driver\Exception\Exception $e) {
		    printf("Other error: %s\n", $e->getMessage());
		    exit;
		}

		return $this->BulkWriteResult($result);

		// $result->insertedIds; // array
	}

	function BulkWriteResult($result){
    	$responseArray = $this->CreateResponseArray();

        $responseArray["num_rows"] = $result->getInsertedCount();
        $responseArray["insert_id"] = 0;
        $responseArray["access_status"] = "OK";
        $responseArray["affected_rows"] = $result->getModifiedCount();

        $responseArray["mongoDBResult"] = $result;

        return $responseArray;
	}

	public function ExecuteQuery($filter, $options){

		$dbName = $this->database_fyp;
		$tableName = $this->table;
		$manager = $this->mongoDBManager;

		$query = new MongoDB\Driver\Query($filter, $options);

		$result = $manager->executeQuery($dbName.'.'.$tableName, $query);

		// printf("Updated %d document(s)\n", $result->getModifiedCount());
		// printf("Matched %d document(s)\n", $result->getMatchedCount());
		// printf("Upserted documents: %d\n", $result->getUpsertedCount());

		return $this->ExecuteQueryResult($result);
	}

	function ExecuteQueryResult($result){
    	$responseArray = $this->CreateResponseArray();

        // $responseArray["num_rows"] = $result->getInsertedCount();
        $responseArray["insert_id"] = 0;
        $responseArray["access_status"] = "OK";
        // $responseArray["affected_rows"] = $result->getModifiedCount();

        $responseArray["mongoDBResult"] = $result;

        $responseArray["table_schema"] = $this->GetDescribeTableStructureResult()["data"];

        return $responseArray;
	}

    /**
     * Magic Methods: __set(), __set() is run when writing data to inaccessible properties.
     * so it is suitable for any initialization that the object may need before it is used.
	 * 
	 * @param string $name, The $name argument is the name of the property being interacted with.
	 * @param string $value, The __set() method's $value argument specifies the value the $name'ed property should be set to.
     */
    function __set($name, $value) {
        $method = 'Set' . ucfirst($name);
			if (method_exists($this, $method)) {
				// Top Priority - if TableNameManager have setName method
				$this->$method($value);
			}else if (array_key_exists($name, $this->_)) {//(isset($this->_[$name])) {
				// Second Priority - if TableNameManager have column name as $name
				// $this->_[$name] = $value;
				$this->SetSQLValueString($name, $value);
			}else if (isset($this->$name)) {
				// Last Priority - if DatabaseManager have variable name as $name
				$this->$name = $value;
			}else {
				throw new Exception('Manager cannot found and set table column or Parent variable!');
			}
    }
    
	/**
	 * Magic Methods: __get(), __get() is utilized for reading data from inaccessible properties.
	 * 
	 * @param string $name, The $name argument is the name of the property being interacted with.
	 * //may be controller need not to get
	 */
    function __get($name) {
        $method = 'get' . ucfirst($name);
		//if($this->issetDefaultValue){
        if (method_exists($this, $method)){
            return $this->$method();
        }else if (isset($this->_[$name])){
            // return $this->_[$name];
            return $this->GetSQLValueString($name);
        }else if (isset($this->$name)){
			return $this->$name;
        }
        //else
            //throw new Exception('Manager cannot found and get table column or Parent variable!');
		//}
    }
	
    public function __call($k, $args) {
        if (preg_match_all('/(set|get)([A-Z][a-z0-9]+)+/', $k, $words)) {
            $firstWord = $words[1][0]; // set or get
            $methodName = strtolower(array_shift($words[2]));
            //first word of property name

            foreach ($words[2] as $word) {
                $methodName .= ucfirst($word);
            }
            if (method_exists($this, $methodName)) {
                $methodObj = array(&$this, $methodName);
                if ($firstWord == 'set') {
                    call_user_func_array($methodObj, $args);
                    return;
                }
                else {
                    return call_user_func_array($methodObj, NULL);
                }
            }
        }
        throw new Exception('tableObject call undefined function() or property!'.$k);
    }


	/**
	 * assign $value to the TableManger.Object column
	 * 
	 * @param string $name columnName
	 * @param string $setValue a value you would like to assign to the TableManager.Object
	 * @return nothing, review is the value setted to the TableManager.Object, use print_r($this->_) to see TableManager.Object
	 */
	function SetSQLValueString($setColumn, $setValue)
	{
		$dataSchema = $this->dataSchema['data'];
		if(empty($dataSchema)){
			return;
		}
		if (PHP_VERSION < 6) {
			$setValue = get_magic_quotes_gpc() ? stripslashes($setValue) : $setValue;
		}
		
		//$setValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($setValue) : mysql_escape_string($setValue);
		// you may use mysqli->real_escape_string() to replace mysql_real_escape_string()
		
		$structure = $this->SearchDataType($dataSchema, 'Field', $setColumn);

		// $column structure
		//Array
		//(
		//	[0] => Array
		//		(
		//			[Field] => LoginID
		//			[Type] => varchar(255)
		//			[Null] => NO
		//			[Key] => UNI
		//			[Default] => 
		//			[Extra] => 
		//		)
		//)
		$type = $structure[0]['Type'];

		// for debug and checking,
		// i don't kown why it must coding as $type===, otherwise $type='datetime' will cased as float/double
		$typeCaseAs = "";

		//$hkTimeZone = new DateTimeZone("Asia/Hong_Kong");
		$defaultTimeZoneString = date_default_timezone_get();
		$hkTimeZone = new DateTimeZone($defaultTimeZoneString);

		//echo "I am a $type type.";
		switch (true) {
			case strpos($type, "char") !== FALSE:
			case strpos($type, "varchar") !== FALSE:
			case strpos($type, "text") !== FALSE:
				$typeCaseAs = "text";
				if(strpos($setValue, "'")==0 && strrpos($setValue, "'")==strlen($setValue)-1 && strlen($setValue)!=1){
					break;
				}
				// $setValue = ($setValue != "") ? "'" . $setValue . "'" : NULL;
				// $setValue = $this->dbc->real_escape_string($setValue);

				// $setValue = ($setValue != "") ? "'" . $setValue . "'" : NULL;
				$setValue = ($setValue != "") ? $setValue : NULL;;
				break;
			//http://dev.mysql.com/doc/refman/5.0/en/integer-types.html
			case strpos($type, "tinyint") !== FALSE: // -128 to 127, 0 to 255
			case strpos($type, "smallint") !== FALSE: // -32768 to 32767, 0 to 65535
			case strpos($type, "mediumint") !== FALSE: // -8388608 to 8388607, 0 to 16777215
			case strpos($type, "int") !== FALSE: // -2147483648 to 2147483647, 0 to 4294967295
			case strpos($type, "bigint") !== FALSE: // -9223372036854775808 to 9223372036854775807, 0 to 18446744073709551615
				$setValue = ($setValue != "") ? intval($setValue) : NULL;
				$typeCaseAs = "integer";
				break;
			//http://dev.mysql.com/doc/refman/5.0/en/fixed-point-types.html
			//http://dev.mysql.com/doc/refman/5.0/en/floating-point-types.html
			case strpos($type, "float") !== FALSE:
			case strpos($type, "double") !== FALSE:
			case strpos($type, "decimal") !== FALSE:
				$setValue = ($setValue != "") ? doubleval($setValue) : NULL;
				$typeCaseAs = "decimal";
				break;

			case $type==="date":
					$tmpDate = date_parse($setValue);
					if($tmpDate["error_count"] > 0)
						$setValue = date("Y-m-d"); // if convert with error, use the current date
					else
						$setValue = new DateTime($setValue);

				if(is_object($setValue)){
					$setValue->setTimezone($hkTimeZone);
					$setValue = $setValue->format("Y-m-d");
				}
				$setValue = "'" . $setValue . "'";
				$typeCaseAs = "date";
				break;

			case $type==="datetime":
			case $type==="timestamp":
				// convert string to date
				$tmpDate = date_parse($setValue);
				//print_r($tmpDate);
				if($tmpDate["error_count"] > 0){
					//$setValue = date("Y-m-d\TH:i:s+"); // if convert with error, use the current date
					$setValue = NULL;
					break;
				}else{
					$setValue = new DateTime($setValue);
				}

				// if(!is_null($setValue))
					if(is_object($setValue) && $setValue instanceof DateTime){
						$setValue->setTimezone($hkTimeZone);
						$setValue = $setValue->format("Y-m-d H:i:s");
					}
				$typeCaseAs = "datetime";
				break;
			case $type==="time":
					$tmpDate = date_parse($setValue);
					if($tmpDate["error_count"] > 0)
						$setValue = date("H:i:s"); // if convert with error, use the current date
					else
						$setValue = new DateTime($setValue);
				
				$setValue = $setValue->format("H:i:s");
				$setValue = "'" . $setValue . "'";
				$typeCaseAs = "time";
				break;
		}
		
		// if(strpos($setValue, '@')!==false)
		// echo "value in:$setValue, type:$type, entryType:$typeCaseAs"."<br>";

		$this->_[$setColumn] = $setValue;
	}
	
	/**
	 * return a specifiy column value
	 * 
	 * @param string $getColumn, column name
	 * @return string
	 */
	function GetSQLValueString($getColumn)
	{
		//return;
		$dataSchema = $this->dataSchema['data'];
		$structure = $this->SearchDataType($dataSchema, 'Field', $getColumn);
		$type = $structure[0]['Type'];
		$valueIn = $this->_[$getColumn];
		$valueOut = "NULL";
		
		// for debug and checking,
		// i don't kown why it must coding as $type===, otherwise $type='datetime' will cased as float/double
		$typeCaseAs = "";
		
		switch (true) {
			case strpos($type, "char") !== FALSE:
			case strpos($type, "varchar") !== FALSE:
			case strpos($type, "text") !== FALSE:
				if(strpos($valueIn, "'")==0 && strrpos($valueIn, "'")==strlen($valueIn)-1 && strlen($valueIn) != 1){
					$valueOut = $valueIn;
				}else{
					$valueOut = ($valueIn != "") ? "'" . $valueIn . "'" : NULL;
				}

				// $valueOut = $this->dbc->real_escape_string($valueIn);
				$valueOut = $valueIn;

				$typeCaseAs = "text";

				break;
				//http://dev.mysql.com/doc/refman/5.0/en/integer-types.html
			case $type === "tinyint": // -128 to 127, 0 to 255
			case $type === "smallint": // -32768 to 32767, 0 to 65535
			case $type === "mediumint": // -8388608 to 8388607, 0 to 16777215
			case strpos($type, "int") !== FALSE: // -2147483648 to 2147483647, 0 to 4294967295
			case $type === "bigint": // -9223372036854775808 to 9223372036854775807, 0 to 18446744073709551615
                // both are cannot identify the $valueIn is NULL, always convert the NULL to 0 and return
//                $valueOut = ($valueIn != "" && $valueIn != null) ? intval($valueIn) : "NULL";
//				$valueOut = (is_null($valueIn) || $valueIn == "" || $valueIn == null) ? echo "NULL" : intval($valueIn);
                $valueOut = is_int($valueIn) || gettype($valueIn)=="string" ? intval($valueIn) : "NULL";
                
				$typeCaseAs = "integer";
				break;
				//http://dev.mysql.com/doc/refman/5.0/en/fixed-point-types.html
				//http://dev.mysql.com/doc/refman/5.0/en/floating-point-types.html
			case strpos($type, "float") !== FALSE:
			case strpos($type, "double") !== FALSE:
			case strpos($type, "decimal") !== FALSE:
//				$valueOut = ($valueIn != "") ? doubleval($valueIn) : NULL;
                $valueOut = is_float($valueIn) ? doubleval($valueIn) : "NULL";
				$typeCaseAs = "decimal";
				break;
			case $type==="date":
				if($this->IsNullOrEmptyString($valueIn)){
					//$valueOut = date("Y-m-d");
					$valueOut = NULL;
					return $valueOut;
					break;
				}else{
					$valueIn = trim($valueIn, "'");
					// convert string to date
					$tmpDate = date_parse($valueIn);
					if(count($tmpDate["errors"]) > 0)
						$valueOut = date("Y-m-d"); // if convert with error, use the current date
					else
						//$valueOut = $tmpDate->format("Y-m-d H:i:s");
						// mktime(hour,minute,second,month,day,year)
						$valueOut = date("Y-m-d", mktime(
							0, 
							0, 
							0, 
							$tmpDate["month"], 
							$tmpDate["day"], 
							$tmpDate["year"])
					);
				}
				$valueOut = "'" . $valueOut . "'";
				/*
				if(is_string($valueIn)){
					$valueOut = $valueIn;
				}else{
					$valueOut = ($valueIn != "") ? "'" . $valueIn . "'" : "NULL";
				}
				*/
				$typeCaseAs = "date";
				break;
			case $type==="datetime":
			case $type==="timestamp":
				if($this->IsNullOrEmptyString($valueIn)){
					//$valueOut = date("Y-m-d H:i:s");
					$valueOut = NULL;
					return $valueOut;
					break;
				}else{
					$valueIn = trim($valueIn, "'");
					// convert string to date
					$tmpDate = date_parse($valueIn);
					if(count($tmpDate["errors"]) > 0)
						$valueOut = date("Y-m-d H:i:s"); // if convert with error, use the current date
					else
						//$valueOut = $tmpDate->format("Y-m-d H:i:s");
						// mktime(hour,minute,second,month,day,year)
						$valueOut = date("Y-m-d H:i:s", mktime(
							$tmpDate["hour"], 
							$tmpDate["minute"], 
							$tmpDate["second"], 
							$tmpDate["month"], 
							$tmpDate["day"], 
							$tmpDate["year"])
					);
				}
				if(!is_null($valueOut))
					$valueOut = "'" . $valueOut . "'";

				$typeCaseAs = "datetime";

				break;
			case $type==="time":
				if($this->IsNullOrEmptyString($valueIn)){
					$valueOut = date("H:i:s");
				}else{
					$valueIn = trim($valueIn, "'");
					// convert string to date
					$tmpDate = date_parse($valueIn);
					if(count($tmpDate["errors"]) > 0)
						$valueOut = date("H:i:s"); // if convert with error, use the current date
					else
						//$valueOut = $tmpDate->format("Y-m-d H:i:s");
						// mktime(hour,minute,second,month,day,year)
						$valueOut = date("H:i:s", mktime(
							$tmpDate["hour"], 
							$tmpDate["minute"], 
							$tmpDate["second"])
					);
				}
				$valueOut = "'" . $valueOut . "'";
				$typeCaseAs = "time";
				break;
		}
		
//		echo "value in:$valueIn, type:$type, entryType:$typeCaseAs, value out:$valueOut";
		return $valueOut;
	}
	
	/**
	 * 
	 * @param string $question input a sting
	 * @return boolean, true means that the string is null or empty otherwise false
	 */
	static function IsNullOrEmptyString($question){
		return (!isset($question) || trim($question)==='');
	}
	
	function IsSystemField($fields){
		$isSystemField = false;
		
		$isSystemField = array_search($fields, Core::$reserved_fields);
		return $isSystemField;
	}

	
	/**
	 *
	 * @param array $array a array or a nested array
	 * @param string $key search is the $key index exists in the array
	 * @param string $value find a array contain key index with $value value
	 * @return array, a array contains one or more array(s) which match $key as index and $value as value 
	 */
	function SearchDataType($array, $key, $value) {
		$results = array();
	
		if (is_array($array)) {
			if (isset($array[$key]) && $array[$key] == $value) {
				$results[] = $array;
			}
	
			foreach ($array as $subarray) {
				$results = array_merge($results, $this->SearchDataType($subarray, $key, $value));
			}
		}
	
		return $results;
	}
}

?>