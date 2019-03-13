<?php

/**********************
* Classe para manipulação dos endereços do cliente
**********************/

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;

class Address extends Model
{
    const SESSION_ERROR = "AddressError";

	// Pega o endereço relacionado com o CEP
    public static function getCEP($nrcep)
    {
    	// URL do serviço http://viacep.com.br/ws/01001000/json/

    	$nrcep = str_replace("-", "", $nrcep);

    	$ch = curl_init();

    	curl_setopt($ch, CURLOPT_URL, "http://viacep.com.br/ws/$nrcep/json/");
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    	$data = json_decode(curl_EXEC($ch), true);

    	curl_close($ch);

    	return $data;
    }

    // Carrega o endereço
    public function loadFromCEP($nrcep)
    {
    	$data = Address::getCEP($nrcep);

    	if (isset($data['logradouro']) && $data['logradouro'])
    	{
    		$this->setdesaddress($data['logradouro']);
    		$this->setdescomplement($data['complemento']);
    		$this->setdesdistrict($data['bairro']);
    		$this->setdescity($data['localidade']);
    		$this->setdesstate($data['uf']);
    		$this->setdescountry('Brasil');
    		$this->setdeszipcode($data[$nrcep]);
    	}
    }

    // Salva o Endereço
    public function save()
    {
        $sql = new Sql();

        $results = $sql->select("CALL sp_addresses_save2(:idaddress, :idperson, :desaddress, :descomplement, :descity, :desstate, :descountry, :deszipcode, :desdistrict)", [
            ':idaddress'=>$this->getidaddress(),
            ':idperson'=>$this->getidperson(),
            ':desaddress'=>$this->getdesaddress(),
            ':descomplement'=>$this->getdescomplement(),
            ':descity'=>$this->getdescity(),
            ':desstate'=>$this->getdesstate(),
            ':descountry'=>$this->getdescountry(),
            ':deszipcode'=>$this->getdeszipcode(),
            ':desdistrict'=>$this->getdesdistrict()
        ]);
/*
        $results = [
            ':idaddress'=>$this->getidaddress(),
            ':idperson'=>$this->getidperson(),
            ':desaddress'=>$this->getdesaddress(),
            ':descomplement'=>$this->getdescomplement(),
            ':descity'=>$this->getdescity(),
            ':desstate'=>$this->getdesstate(),
            ':descountry'=>$this->getdescountry(),
            ':deszipcode'=>$this->getdeszipcode(),
            ':desdistrict'=>$this->getdesdistrict()
        ];

        var_dump($results);
        exit;
*/
        if (count($results) > 0)
        {
            $this->setData($results[0]);
        }
    }

    // Acerta a mensagem de erro
    public static function setMsgError($msg)
    {
        $_SESSION[Address::SESSION_ERROR] = $msg;
    }

    // Pega a mensagem de erro
    public static function getMsgError()
    {
        $msg = (isset($_SESSION[Address::SESSION_ERROR])) ? $_SESSION[Address::SESSION_ERROR] : "";

        Address::clearMsgError();

        return $msg;
    }

    // Limpa a mensagem de erro
    public static function clearMsgError()
    {
        $_SESSION[Address::SESSION_ERROR] = NULL;
    }


}

?>