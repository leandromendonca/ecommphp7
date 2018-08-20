<?php

/**********************
* Classe básica para objetos
**********************/

namespace Hcode;

class Model
{
    private $values = [];

    // Método mágico, executa setters e getters dinamicamente
    public function __call($name, $args)
    {
        $method = substr($name, 0, 3);
        $fieldName = substr($name, 3, strlen($name));

        switch ($method)
        {
            case "get":
                return $this->values[$fieldName];
            break;
            
            case "set":
                $this->values[$fieldName] = $args[0];
            break;
        }
    }

    // Executa todos os setters para um objeto
    public function setData($data = array())
    {
        foreach ($data as $key => $value) {
            $this->{"set".$key}($value);
        }
    }

    // Pega todos os valores de um objeto
    public function getValues()
    {
        return $this->values;
    }

    // Função de encriptar usando openssl
    public static function encrypt(string $data, string $key, string $method): string
    {
        $ivSize = openssl_cipher_iv_length($method);
        $iv = openssl_random_pseudo_bytes($ivSize);

        $encrypted = openssl_encrypt($data, $method, $key, OPENSSL_RAW_DATA, $iv);
        
        // For storage/transmission, we simply concatenate the IV and cipher text
        $encrypted = base64_encode($iv . $encrypted);

        return $encrypted;
    }

    // Função de decriptar usando openssl
    public static function decrypt(string $data, string $key, string $method): string
    {
        $data = base64_decode($data);
        $ivSize = openssl_cipher_iv_length($method);
        $iv = substr($data, 0, $ivSize);
        $data = openssl_decrypt(substr($data, $ivSize), $method, $key, OPENSSL_RAW_DATA, $iv);

        return $data;
    }

}

?>