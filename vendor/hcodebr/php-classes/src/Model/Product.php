<?php

/**********************
* Classe para manipulação de produtos
**********************/

namespace Hcode\Model;

use \Hcode\Model;
use \Hcode\DB\Sql;

class Product extends Model
{
	// lista todos os produtos
	public static function listAll()
	{
		$sql = new Sql();

		return $sql->select("SELECT * FROM tb_products ORDER BY desproduct");
	}

    // verifica a lista de produtos (adiciona a foto no getValues)
    public static function checkList($list)
    {
        foreach ($list as &$row)
        {
            $p = new Product();
            $p->setData($row);
            $row = $p->getValues();
        }

        return $list;
    }

    // salva um produto
    public function save()
    {
        $sql = new Sql();

        $results = $sql->select("CALL sp_products_save(:idproduct, :desproduct, :vlprice, :vlwidth, :vlheight, :vllength, :vlweight, :desurl)", array(
            ":idproduct"=>$this->getidproduct(),
            ":desproduct"=>$this->getdesproduct(),
            ":vlprice"=>$this->getvlprice(),
            ":vlwidth"=>$this->getvlwidth(),
            ":vlheight"=>$this->getvlheight(),
            ":vllength"=>$this->getvllength(),
            ":vlweight"=>$this->getvlweight(),
            ":desurl"=>$this->getdesurl()
        ));

        $this->setData($results[0]);
    }

    // pega um produto
    public function get($idproduct)
    {
    	$sql = new Sql();

    	$results = $sql->select("SELECT * FROM tb_products WHERE idproduct = :idproduct", [
    		':idproduct'=>$idproduct
    	]);

    	$this->setData($results[0]);
    }

    // apaga um produto
    public function delete()
    {
    	$sql = new Sql();

    	$sql->query("DELETE FROM tb_products WHERE idproduct = :idproduct", [
    		':idproduct'=>$this->getidproduct()
    	]);
    }

    // verifica a foto do produto
    public function checkPhoto()
    {
        if (file_exists(
            $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR .
            "res" . DIRECTORY_SEPARATOR .
            "site" . DIRECTORY_SEPARATOR .
            "img" . DIRECTORY_SEPARATOR .
            "products" . DIRECTORY_SEPARATOR .
            $this->getidproduct() . ".jpg"
        ))
        {
            $url = "/res/site/img/products/" . $this->getidproduct() . ".jpg";
        }
        else
        {
            $url = "/res/site/img/product.jpg";
        }

        return $this->setdesphoto($url);
    }

    // getValues especial para o objeto de produto
    public function getValues()
    {
        $this->checkPhoto();

        $values = parent::getValues();

        return $values;
    }

    // upload da foto do arquivo
    public function setPhoto($file)
    {
        $extention = explode('.', $file['name']);
        $extention = end($extention);

        switch ($extention)
        {
            case 'jpg':
            case 'jpeg':
                $image = imagecreatefromjpeg($file['tmp_name']);
                break;
            
            case 'gif':
                $image = imagecreatefromgif($file['tmp_name']);
                break;

            case 'png':
                $png = imagecreatefrompng($file['tmp_name']);

                $width = imagesx($png);
                $height = imagesy($png);

                $image = imagecreatetruecolor($width,$height);
                $white = imagecolorallocate($image, 255, 255, 255);

                imagefill($image,0,0,$white);

                imagecopyresampled($image,$png,0,0,0,0,$width,$height,$width,$height);

                imagedestroy($png);

                break;
        }

        if (isset($image))
        {
            $dist = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR .
                "res" . DIRECTORY_SEPARATOR .
                "site" . DIRECTORY_SEPARATOR .
                "img" . DIRECTORY_SEPARATOR .
                "products" . DIRECTORY_SEPARATOR .
                $this->getidproduct() . ".jpg";

            imagejpeg($image, $dist);

            imagedestroy($image);
        }

        $this->checkPhoto();
    }

    // Pega um produto a partir da URL
    public function getFromURL($desurl)
    {
        $sql = new Sql();

        $rows = $sql->select("SELECT * FROM tb_products WHERE desurl = :desurl LIMIT 1", [
            ':desurl'=>$desurl
        ]);

        $this->setData($rows[0]);
    }

    // Pega as categorias de um produto
    public function getCategories()
    {
        $sql = new Sql();

        return $sql->select("
            SELECT * FROM tb_categories a
            INNER JOIN tb_productscategories b
            ON a.idcategory = b.idcategory
            WHERE b.idproduct = :idproduct
        ", [
            ':idproduct'=>$this->getidproduct()
        ]);
    }

}

?>