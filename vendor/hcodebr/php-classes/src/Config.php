<?php

/**********************
* Classe de configuração do sistema
**********************/

namespace Hcode;

use \Hcode\Model;
use \Hcode\DB\Sql;

class Config extends Model
{
    // Constrói a configução de um módulo passado por parâmetro
    public function __construct($idconfig = '')
    {
        if (isset($idconfig) && $idconfig != '')
        {
            $this->get($idconfig);
        }
    }

    // Pega no banco as variáveis de configuração do módulo
    public function get($idconfig)
    {
        $sql = new Sql();

        $results = $sql->select("SELECT option_name, option_value FROM tb_config WHERE option_name LIKE :option", array(
            ":option"=>'%'.$idconfig.'_%'
        ));

        foreach ($results as $key) {
            $this->setData(array($key['option_name']=>$key['option_value']));
        }
    }

}

?>