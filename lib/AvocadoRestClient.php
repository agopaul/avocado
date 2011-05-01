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
	
	public function compareToLink($ApiUrl){
		// curl richiesta, se 200 OK allora json_decode
		
		$LocalSchema = AvocadoSchema::getInstanceFromDb($this->Db);
		
		$Curl = curl_init();
		curl_setopt($Curl, CURLOPT_URL, $ApiUrl);
		curl_setopt($Curl, CURLOPT_HEADER, 0);
		curl_setopt($Curl, CURLOPT_RETURNTRANSFER, true);
		if($Result = curl_exec($Curl)){

			if(!$RemoteTables = @json_decode($Result, true))
				throw new AvocadoException("Bad or empty response from server");
			
			$RemoteSchema = AvocadoSchema::getInstanceFromArray($RemoteTables);

			$this->compareSchemas($LocalSchema, $RemoteSchema);
			/*
			var_dump($LocalSchema);
			var_dump($RemoteSchema);
			*/
		}
		else throw new AvocadoException("Error opening URL: ".curl_error($Curl));
		curl_close($Curl);
	}

	public function compareToArray(array $Arr){
		// curl richiesta, se 200 OK allora json_decode
		
		$LocalSchema = AvocadoSchema::getInstanceFromDb($this->Db);
		$RemoteSchema = AvocadoSchema::getInstanceFromArray($Arr);

		$this->compareSchemas($LocalSchema, $RemoteSchema);
		/*
		var_dump($LocalSchema);
		var_dump($RemoteSchema);
		*/

	}
	
	public function compareSchemas(AvocadoSchema $Schema1, AvocadoSchema $Schema2){
		$Diff = new AvocadoSchemaDiff();
		$Tables = array_merge($Schema1->getTables(), $Schema2->getTables());

		foreach($Tables as $Table){
			if(isset($Schema1[$Table->getName()]) && !isset($Schema2[$Table->getName()])){
				$Diff->addTable($Table);
			}
			elseif(isset($Schema2[$Table->getName()]) && !isset($Schema1[$Table->getName()])){
				$Diff->deleteTable($Table);
			}
			else{

				// Compare fields
				$T1 = $Schema1[$Table->getName()]->getFields();
				$T2 = $Schema2[$Table->getName()]->getFields();

				foreach(array_merge($T1, $T2) as $Field){
					/// TODO :: Check the field properties too
					
					//foreach($T1 as $CurrentField)
					//	$CurrentField->getName()

					if(isset($T1[$Field->getName()]) && !isset($T2[$Field->getName()])){
						$Diff->addField($Field);
					}
					elseif(isset($T2[$Field->getName()]) && !isset($T1[$Field->getName()])){
						$Diff->deleteField($Field);
					}
				}

			}
		}
		return $Diff;
	}
	
}

		?>