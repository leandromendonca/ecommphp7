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

        Category::updateFile();
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

        Category::updateFile();
    }

    public static function updateFile()
    {
        $categories = Category::listAll();

        $html = [];

        foreach ($categories as $row)
        {
            array_push($html, '<li><a href="/category/'.$row['idcategory'].'">'.$row['descategory'].'</a></li>');
        }

        file_put_contents($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR."views".DIRECTORY_SEPARATOR."categories-menu.html", implode('', $html));
    }

}

?>