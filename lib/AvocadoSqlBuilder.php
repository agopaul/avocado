<?php

/**
 * Builds the SQL statements given 
 * a field or a table
 *
 * @package default
 * @author paul
 **/
class AvocadoSqlBuilder{

	const CREATE = 1;
	const ALTER = 2;
	const DELETE = 3;
	const INTO_TABLE = 4;

	protected $Diff, $Statements;
	
	public function __construct(AvocadoSchemaDiff $Diff){
		$this->Diff = $Diff;
		$this->Statements = array();
	}

	public function build(){
		$Diffs = $this->Diff->getAll();
		foreach($Diffs["add_tables"] as $Table){
			$this->Statements[] = $this->tableToSql($Table, self::CREATE);
		}
		foreach($Diffs["add_fields"] as $Field){
			$this->Statements[] = $this->fieldToSql($Field, self::CREATE);
		}
		foreach($Diffs["delete_tables"] as $Table){
			$this->Statements[] = $this->tableToSql($Table, self::DELETE);
		}
		foreach($Diffs["delete_fields"] as $Field){
			$this->Statements[] = $this->fieldToSql($Field, self::DELETE);
		}

		if($this->Statements)
			return implode(";\n", $this->Statements).";";
	}

	/**
	 * Return the table SQL
	 *
	 * @return string
	 * @author paul
	 **/
	protected function tableToSql(AvocadoTable $Table, $Action=self::CREATE){
		$Sep = "\n\t";
		$Sql = "CREATE TABLE {$Table->getName()}(";
		foreach($Table->getFields() as $Field){
			$Sql .= $Sep . $this->fieldToSql($Field, self::INTO_TABLE);
			$Sep = ",\n\t";
		}
		return $Sql . "\n)";
	}

	/**
	 * Return sql to create/modify field
	 *
	 * @return string
	 * @author paul
	 **/
	protected function fieldToSql(AvocadoField $Field, $Action=self::CREATE){

		switch($Action){
			case self::CREATE:
					return sprintf("ALTER TABLE %s ADD %s %s(%s) %s", 
										$Field->getTable()->getName(),
										$Field->getName(),
										$Field->getType(),
										$Field->getLength(),
										$Field->getNullable() ? 'NULL' : 'NOT NULL'
									);
				break;
				
			case self::ALTER:
					return sprintf("ALTER TABLE %s MODIFY %s %s(%s) %s", 
										$Field->getTable()->getName(),
										$Field->getName(),
										$Field->getType(),
										$Field->getLength(),
										$Field->getNullable() ? 'NULL' : 'NOT NULL'
									);
				break;
			
			case self::DELETE:
					return sprintf("ALTER TABLE %s DROP %s", 
										$Field->getTable()->getName(),
										$Field->getName(),
										$Field->getType(),
										$Field->getLength(),
										$Field->getNullable() ? 'NULL' : 'NOT NULL'
									);
				break;

			case self::INTO_TABLE:
					return sprintf("'%s' %s%s %s",
										$Field->getName(),
										$Field->getType(),
										$Field->getLength() ? "(".$Field->getLength().")" : null,
										$Field->getNullable() ? 'NULL' : 'NOT NULL'
									);
				break;
		}
	}
}
