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

    // apaga uma categoria
    public function delete()
    {
    	$sql = new Sql();

    	$sql->query("DELETE FROM tb_categories WHERE idcategory = :idcategory", [
    		':idcategory'=>$this->getidcategory()
    	]);

        Category::updateFile();
    }

    // atualiza arquivo com a lista de categorias para o frontend
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

    // consulta produtos relacionados ou não com a categoria
    public function getProducts($related = true)
    {
        $sql = new Sql();

        if ($related === true)
        {
            return $sql->select("
                SELECT * FROM tb_products WHERE idproduct IN(
                    SELECT a.idproduct
                    FROM tb_products a
                    INNER JOIN tb_productscategories b ON a.idproduct = b.idproduct
                    WHERE b.idcategory = :idcategory
                )
            ", [
                ':idcategory'=>$this->getidcategory()
            ]);
        }
        else
        {
            return $sql->select("
                SELECT * FROM tb_products WHERE idproduct NOT IN(
                    SELECT a.idproduct
                    FROM tb_products a
                    INNER JOIN tb_productscategories b ON a.idproduct = b.idproduct
                    WHERE b.idcategory = :idcategory
                )
            ", [
                ':idcategory'=>$this->getidcategory()
            ]);
        }
    }

    // consulta paginada de produtos relacionados ou não com a categoria
    public function getProductsPage($page = 1, $itemsPerPage = 8)
    {
        $sql = new Sql();

        $start = ($page - 1) * $itemsPerPage;

        $results = $sql->select("
            SELECT SQL_CALC_FOUND_ROWS *
            FROM tb_products a
            INNER JOIN tb_productscategories b ON a.idproduct = b.idproduct
            INNER JOIN tb_categories c ON c.idcategory = b.idcategory
            WHERE c.idcategory = :idcategory
            LIMIT $start, $itemsPerPage;
        ", [
            ':idcategory'=>$this->getidcategory()
        ]);

        $resultsTotal = $sql->select("SELECT FOUND_ROWS() AS nrtotal;");

        return [
            'data'=>Product::checklist($results),
            'total'=>(int)$resultsTotal[0]["nrtotal"],
            'pages'=>ceil($resultsTotal[0]["nrtotal"] / $itemsPerPage)
        ];
    }

    // adciona o produto na categoria
    public function addProduct(Product $product)
    {
        $sql = new Sql();

        $sql->query("INSERT INTO tb_productscategories (idcategory, idproduct) VALUES (:idcategory, :idproduct)", [
            ':idcategory'=>$this->getidcategory(),
            ':idproduct'=>$product->getidproduct()
        ]);
    }

    // remove o produto da categoria
    public function removeProduct(Product $product)
    {
        $sql = new Sql();

        $sql->query("DELETE FROM tb_productscategories WHERE idcategory = :idcategory AND idproduct = :idproduct", [
            ':idcategory'=>$this->getidcategory(),
            ':idproduct'=>$product->getidproduct()
        ]);
    }

}

?>