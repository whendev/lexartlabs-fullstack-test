<?php


namespace Source\Controllers;


use CoffeeCode\Router\Router;
use Source\Core\Connect;
use Source\Core\Controller as Controller;
use Source\Models\Admin\Auth;
use Source\Models\MlApi;
use Source\Support\Message;

class Admin extends Controller
{
    protected ?\Source\Models\Admin\Admin $admin;

    public function __construct()
    {
        parent::__construct(__DIR__."/../../resources/views/". CONF_VIEW_ADMIN ."/");

        if (!Auth::admin()){
            (new Message())->warning("Efetue o login para acessar esta area")->flash();
            redirect("/login");
        }
        $this->admin = Auth::admin();
    }

    public function home()
    {
        echo $this->view->render("home", [
            "link" => "https://auth.mercadolivre.com.br/authorization?response_type=code&client_id={$_ENV['CONF_CLIENT_ID']}&redirect_uri={$_ENV['CONF_REDIRECT_URI']}"
        ]);
    }

    public function auth()
    {
        if (!empty($_GET["code"])){
            $code = filter_var($_GET["code"], FILTER_SANITIZE_STRIPPED);
            $meli = new MlApi();
            $auth = $meli->auth($code);
            if ($auth){
                $response = json_decode($auth[0]);
                if ($meli->setAuth($response)){
                    $this->message->success("Sua aplicação foi autorizada com sucesso")->flash();
                    redirect("/admin");
                    return;
                }
                $this->message->error("Não foi possivel autorizar sua aplicaçõa, verirque os dados e tente novamente!")->flash();
                redirect("/admin");
                return;
            } else{
                $this->message->error("Não foi possivel autorizar sua aplicaçõa, verirque os dados e tente novamente!")->flash();
                redirect("/admin");
                return;
            }
        }
        redirect("/admin");
    }
}