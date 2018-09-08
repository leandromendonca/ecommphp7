<?php

/**********************
* Classe para manipulação de usuários
**********************/

namespace Hcode\Model;

use \Hcode\Model;
use \Hcode\DB\Sql;
use \Hcode\Mailer;

class User extends Model
{
    const SESSION = "User";
    const SECRET = "Recovery_Hcode_Sample";
    const METHOD = 'AES-256-CBC';

    /*
    * Métodos de login
    */

    // Executa o login
    public static function login($login, $password)
    {
        $sql = new Sql();

        $results = $sql->select("SELECT * FROM tb_users WHERE deslogin = :LOGIN", array(
            ":LOGIN"=>$login
        ));

        if (count($results) === 0)
        {
            throw new \Exception("Usuário inexistente ou senha inválida.");
        }

        $data = $results[0];

        if (password_verify($password, $data["despassword"]))
        {
            $user = new User();

            $user->setData($data);

            $_SESSION[User::SESSION] = $user->getValues();

            return $user;
        }
        else
        {
            throw new \Exception("Usuário inexistente ou senha inválida.");
        }
    }

    // Verifica se o usuário não está logado e manda para o login no admin
    public static function verifyLogin($inadmin = true)
    {
        if (User::checkLogin($inadmin))
        {
            header("Location: /admin/login");
            exit;
        }
    }

    // Verifica o login no frontend
    public static function checkLogin($inadmin = true)
    {
        if (
            !isset($_SESSION[User::SESSION])
            ||
            !$_SESSION[User::SESSION]
            ||
            !(int)$_SESSION[User::SESSION]["iduser"] > 0
        ) {
            // não está logado
            return false;
        }
        else
        {
            if (($inadmin === true) && (bool)$_SESSION[User::SESSION]['idadmin'] === true)
            {
                return true;
            }
            else if ($inadmin === false)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
    }

    // Executa o logout
    public static function logout()
    {
        $_SESSION[User::SESSION] = NULL;
    }

    /*
    * Métodos de administração de usuários
    */

    // lista todos os usuários
    public static function listAll()
    {
        $sql = new Sql();

        return $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) ORDER BY b.desperson");
    }

    // salva um usuário
    public function save()
    {
        $sql = new Sql();

        /*
        pdesperson VARCHAR(64), 
        pdeslogin VARCHAR(64), 
        pdespassword VARCHAR(256), 
        pdesemail VARCHAR(128), 
        pnrphone BIGINT, 
        pinadmin TINYINT
        */

        $results = $sql->select("CALL sp_users_save(:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
            ":desperson"=>$this->getdesperson(),
            ":deslogin"=>$this->getdeslogin(),
            ":despassword"=>$this->getdespassword(),
            ":desemail"=>$this->getdesemail(),
            ":nrphone"=>$this->getnrphone(),
            ":inadmin"=>$this->getinadmin()
        ));

        $this->setData($results[0]);
    }

    // pega os dados de um usuário
    public function get($iduser)
    {
        $sql = new Sql();

        $results = $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) WHERE a.iduser = :iduser", array(
            ":iduser"=>$iduser
        ));

        $this->setData($results[0]);
    }

    // atualiza os dados de um usuário
    public function update()
    {
        $sql = new Sql();

        $results = $sql->select("CALL sp_usersupdate_save(:iduser, :desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
            ":iduser"=>$this->getiduser(),
            ":desperson"=>$this->getdesperson(),
            ":deslogin"=>$this->getdeslogin(),
            ":despassword"=>$this->getdespassword(),
            ":desemail"=>$this->getdesemail(),
            ":nrphone"=>$this->getnrphone(),
            ":inadmin"=>$this->getinadmin()
        ));

        $this->setData($results[0]);
    }

    // apaga um usuário
    public function delete()
    {
        $sql = new Sql();

        $sql->query("CALL sp_users_delete(:iduser)", array(
            ":iduser"=>$this->getiduser()
        ));
    }

    // Pega token de recuperação de senha associado ao endereço de e-mail
    public static function getForgot($email)
    {
        $sql = new Sql;

        $results = $sql->select("
            SELECT *
            FROM tb_persons a
            INNER JOIN tb_users b USING(idperson)
            WHERE a.desemail = :email;
        ", array(
            ":email"=>$email
        ));

        if (count($results) === 0)
        {
            throw new \Exception("Não foi possível recuperar a senha 1.");
        }
        else
        {
            $data = $results[0];

            $recovery = $sql->select("CALL sp_userspasswordsrecoveries_create(:iduser, :desip)", array(
                ":iduser"=>$data["iduser"],
                ":desip"=>$_SERVER["REMOTE_ADDR"]
            ));

            if (count($recovery) === 0)
            {
                throw new \Exception("Não foi possível recuperar a senha 2.");
            }
            else
            {
                $dataRecovery = $recovery[0];

                $code = parent::encrypt($dataRecovery["idrecovery"], hash('sha256', User::SECRET), User::METHOD);

                $link = (isset($_SERVER["HTTPS"]) ? "https://" : "http://") . $_SERVER["SERVER_NAME"] . "/admin/forgot/reset?code=$code";

                $mailer = new Mailer($data["desemail"], $data["desperson"], "Redefinir a senha da Hcode Store.", "forgot", array(
                    "name"=>$data["desperson"],
                    "link"=>$link
                ));

                $mailer->send();

                return $data;
            }
        }
    }

    // Valida o token de recuperação de senha
    public static function validForgotDecrypt($code)
    {
        $idrecovery = parent::decrypt($code, hash('sha256', User::SECRET), User::METHOD);

        $sql = new sql();

        $results = $sql->select("
            SELECT *
            FROM tb_userspasswordsrecoveries a
            INNER JOIN tb_users b USING(iduser)
            INNER JOIN tb_persons c USING(idperson)
            WHERE
                a.idrecovery = :idrecovery
                AND
                a.dtrecovery IS NULL
                AND
                DATE_ADD(a.dtregister, INTERVAL 1 HOUR) >= NOW();
        ", array(
            ':idrecovery'=>$idrecovery
        ));

        if (count($results) === 0)
        {
            throw new \Exception("Não foi possível recuperar a senha.");
        }
        else
        {
            return $results[0];
        }
    }

    // Marca o token como usado
    public static function setForgotUsed($idrecovery)
    {
        $sql = new Sql();

        $sql->query("UPDATE tb_userspasswordsrecories SET dtrecovery = NOW() WHERE idrecovery = :idrecovery", array(
            ":idrecovery"=>$idrecovery
        ));
    }

    // Grava a nova senha do usuário
    public function setPassword($password)
    {
        $sql = new Sql();

        $sql->query("UPDATE tb_users SET despassword = :password WHERE iduser = :iduser", array(
            ":password"=>$password,
            ":iduser"=>$this->getiduser()
        ));
    }

    // Pega o ID do usuário a partir da sessão ativa
    public static function getFromSession()
    {
        $user = new User();

        if (isset($_SESSION[User::SESSION]) && (int)$_SESSION[User::SESSION]['iduser'] > 0)
        {
            $user->setData($_SESSION[User::SESSION]);
        }

        return $user;
    }

}

?>