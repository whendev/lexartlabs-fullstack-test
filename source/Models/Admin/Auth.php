<?php


namespace Source\Models\Admin;


use Source\Core\Model;
use Source\Core\Session;
use function Composer\Autoload\includeFile;

class Auth extends Model
{
    public function __construct()
    {
        parent::__construct("admin", ["email", "password"], ["id"]);
    }

    public static function admin(): ?Admin
    {
        $session = new Session();
        if (!$session->has("authAdmin")){
            return null;
        }

        return (new Admin())->findById($session->authAdmin);
    }

    public function login(string $email, string $password): bool
    {
        if (!is_email($email)){
            $this->message->warning("O email informado não é valido!");
            return false;
        }

        if (!is_passwd($password)){
            $this->message->warning("A senha informada não é valida!");
            return false;
        }

        $admin = (new Admin())->find("email = :email", "email={$email}")->fetch();
        if (!$admin){
            $this->message->error("O email informado não pertence a um administrador!");
            return false;
        }

        if (!passwd_verify($password, $admin->password)){
            $this->message->error("A senha informada não confere");
            return false;
        }

        if (passwd_rehash($admin->password)){
            $admin->password = $password;
            $admin->save();
        }

        // LOGIN
        (new Session())->set("authAdmin", $admin->id);
        $this->message->success("Login efetuado com sucesso")->flash();
        return true;
    }

    public function register()
    {
        $admin = (new Admin());
        $admin->email = $_ENV["CONF_ADMIN_EMAIL"];
        $admin->password = passwd($_ENV["CONF_ADMIN_PASS"]);
        if ($admin->save()){
            return true;
        } else {
            return $admin->fail();
        }

    }

    public function exist_admin(): bool
    {
        $email = $_ENV["CONF_ADMIN_EMAIL"];
        $admin = (new Admin())->find("email = :email", "email={$email}")->fetch();
        if ($admin){
            return true;
        } else {
            return false;
        }
    }
}