<?

if(!function_exists("curl_setopt")) die("Curl module missing");

/**
 * Sends requests to the REST Server
 *
 * @package Avocado
 * @author Paolo Agostinetto <paul.ago@gmail.com>
 **/
class AvocadoRestClient{
	
	protected $Configuration;
	
	function __construct(PDO $Db, array $Configuration){
		$this->Db = $Db;
		$this->Configuration = $Configuration;
	}
	
	public static function getInstance(PDO $Db, array $Configuration){
		return new self($Db, $Configuration);
	}
	
	public function compareTo($AptiRoot){
		// curl richiesta, se 200 OK allora json_decode
		
		$AvoDb = AvocadoSchema::getInstance($this->Db);
		
		$Curl = curl_init();
		curl_setopt($Curl, CURLOPT_URL, $AptiRoot);
		curl_setopt($Curl, CURLOPT_HEADER, 0);
		curl_setopt($Curl, CURLOPT_RETURNTRANSFER, true);
		if($Result = curl_exec($Curl)){
			
			$RemoteTables = json_decode($Result, true);
			$LocalTables = $AvoDb->toArray();
			
			if(!$RemoteTables) throw new AvocadoException("Bad or empty response from REST server");
				
			$this->compareSchemas($LocalTables, $RemoteTables);
			$this->compareSchemas($RemoteTables, $LocalTables);
			
			var_dump($LocalTables);
			var_dump($RemoteTables);
			
		}
		else throw new AvocadoException("Error opening URL: ".curl_error($Curl));
		curl_close($Curl);
	}
	
	protected function compareSchemas(array $Schema1, array $Schema2){
		foreach($Schema1 as $TableName=>$Table){
			if($Schema2[$TableName]){
				foreach($Table as $FieldName=>$Field){
					if($Field2 = $Schema2[$TableName][$FieldName]){
						if($Field['name']!=$Field2['name']){
							
						}
						if($Field['type']!=$Field2['type']){
							
						}
						if($Field['nullable']!=$Field2['nullable']){
							
						}
					}
					else{
						echo "il campo $FieldName non esiste in schema2";
					}
				}
			}
			else{
				echo "la tabella $TableName non c'è in schema2";
			}
		}
	}
	
}

?>