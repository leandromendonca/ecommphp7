<?php 

/**********************
* Classe básica para manipulação do banco
**********************/

namespace Hcode\DB;

class Sql {

	const HOSTNAME = "localhost";
	const USERNAME = "bmend546_ecphp7";
	const PASSWORD = "clatjgll4rc8";
	const DBNAME = "bmend546_ecphp7";

	private $conn;

	// Método construtor, abre a conexão com o banco
	public function __construct()
	{
		$this->conn = new \PDO(
			"mysql:dbname=".Sql::DBNAME.";host=".Sql::HOSTNAME.";charset=UTF8", 
			Sql::USERNAME,
			Sql::PASSWORD
		);
	}

	// Faz bind de um parâmetro para um statement SQL
	private function setParam($statement, $key, $value)
	{
		$statement->bindParam($key, $value);
	}

	// Faz bind de todos os parâmentros para um statment SQL
	private function setParams($statement, $parameters = array())
	{
		foreach ($parameters as $key => $value) {
			
			$this->setParam($statement, $key, $value);

		}
	}

	// Executa uma query SQL
	public function query($rawQuery, $params = array())
	{
		$stmt = $this->conn->prepare($rawQuery);

		$this->setParams($stmt, $params);

		$stmt->execute();

		return $stmt;
	}

	// Executa um select SQL
	public function select($rawQuery, $params = array()):array
	{
		$stmt = $this->query($rawQuery, $params);

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}

}

 ?>