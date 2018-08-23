<?php

/**********************
* Classe para manipulação de categorias
**********************/

namespace Hcode\Model;

use \Hcode\Model;
use \Hcode\DB\Sql;

class Category extends Model
{
/*
* Métodos de administração de usuários
*/

	// lista todos as categorias
	public static function listAll()
	{
		$sql = new Sql();

		return $sql->select("SELECT * FROM tb_categories ORDER BY descategory");
	}

    // salva uma categoria
    public function save()
    {
        $sql = new Sql();

        $results = $sql->select("CALL sp_categories_save(:idcategory, :descategory)", array(
            ":idcategory"=>$this->getidcategory(),
            ":descategory"=>$this->getdescategory(),
        ));

        $this->setData($results[0]);
    }

    // pega uma categoria
    public function get($idcategory)
    {
    	$sql = new Sql();

    	$results = $sql->select("SELECT * FROM tb_categories WHERE idcategory = :idcategory", [
    		':idcategory'=>$idcategory
    	]);

    	$this->setData($results[0]);
    }

    public function delete()
    {
    	$sql = new Sql();

    	$sql->query("DELETE FROM tb_categories WHERE idcategory = :idcategory", [
    		':idcategory'=>$this->getidcategory()
    	]);
    }

}

?>