<?php


namespace Source\Controllers;


use CoffeeCode\Paginator\Paginator;
use GuzzleHttp\Client;
use Requests;
use Source\Core\Controller;
use Source\Models\Admin\Auth;
use Source\Models\MlApi;

class App extends Controller
{

    public function __construct()
    {
        parent::__construct(__DIR__."/../../resources/views/". CONF_VIEW_APP."/");

    }

    public function home()
    {
        echo $this->view->render("home", []);
    }

    public function login(?array $data)
    {
        if (Auth::admin()){
            redirect("/admin/");
            return;
        }

        if(!empty($data["csrf"])){
            if (!csrf_verify($data)){
                $json["message"] = $this->message->warning("Por favor, use o formulario!")->render();
                echo json_encode($json);
                return;
            }
            $auth = (new Auth());
            $admin = $auth->login($data["email"], $data["password"]);
            if($admin){
                $json["redirect"] = url("/admin/");
                echo json_encode($json);
                return;
            } else {
                $json["message"] = $auth->message()->render();
                echo json_encode($json);
                return;
            }
        }

        $auth = (new Auth());
        if (!$auth->exist_admin()){
            $auth->register();
            echo $this->view->render("login", []);
            return;
        }

        echo $this->view->render("login", []);
    }

    public function search(array $data)
    {
        $data = filter_var_array($data);
        if(!empty($data['s'])){
            if (empty($data['category'])){
                $this->message->warning("selecione uma categoria e pesquise por algum produto!")->flash();
                redirect("/");
                return;
            }
            $search = urlencode($data['s']);
            redirect("/search/{$data['web']}/{$data['category']}/$search/p/1");
            return;
        }

        if($data['web'] == 'ml'){

            $search = filter_var($data['search'], FILTER_SANITIZE_STRIPPED);
            $page = (filter_var($data['page'], FILTER_VALIDATE_INT) >= 1 ? $data['page'] : 1);
            $category = (filter_var($data['category'], FILTER_SANITIZE_STRIPPED));
            $pager = new Paginator(url("/search/ml/{$category}/p/"));
            $search = urlencode($search);
            if ($search){
                $pager = new Paginator(url("/search/ml/{$category}/{$search}/p/"));
            }

            $url = search_url($category, $search, ($page == 1 ? $page : (5 * ($page - 1))), 5);
            if ($url){
                $token = (new MlApi())->getToken();
                if ($token){
                    Requests::register_autoloader();
                    $headers = array(
                        'Authorization' => "Bearer {$token}"
                    );
                    $response = Requests::get($url, $headers);
                    $response = json_decode($response->body);
                    $products = $response->results;
                    $pager->pager(100, 5, $page, 1);

                    echo $this->view->render("home", [
                        "products" => $products,
                        "paginator" => $pager->render("Page"),
                        "category" => $category,
                        "search" => urldecode($search)
                    ]);
                    return;
                } else {
                    $this->message->warning("Voce precisa configurar a aplicação!")->flash();
                    redirect("/login");
                    return;
                }
            } else {
                $this->message->warning("selecione uma categoria e pesquise por algum produto!")->flash();
                redirect("/");
            }
        } elseif ($data['web'] == 'bp'){
            $categories = ['celular', 'tv', 'geladeira'];

            if (in_array($data['category'], $categories)){
                $search = filter_var($data['search'], FILTER_SANITIZE_STRIPPED);
                $page = (filter_var($data['page'], FILTER_VALIDATE_INT) >= 1 ? $data['page'] : 1);
                $category = (filter_var($data['category'], FILTER_SANITIZE_STRIPPED));
                $pager = new Paginator(url("/search/bp/{$category}/p/"));
                $search = urlencode($search);
                if ($search){
                    $pager = new Paginator(url("/search/bp/{$category}/{$search}/p/"));
                }

                $url = "https://www.buscape.com.br/".(!empty($search) ? "search?q={$category}%20{$search}&page={$page}" : "{$category}?page={$page}");

                $httpClient = new Client();
                $response = $httpClient->get($url);
                $htmlString = (string) $response->getBody();
                libxml_use_internal_errors(true);

                $doc = new \DOMDocument();
                $doc->loadHTML($htmlString);
                $xpath = new \DOMXPath($doc);
                $links = $xpath->evaluate('//div[@class="SearchPage_SearchList__HL7RI col"][1]//div[@class="card card--prod"]//div[@class="cardBody"]//a[@class="price"]//@href');
                $titles = $xpath->evaluate('//div[@class="SearchPage_SearchList__HL7RI col"][1]//div[@class="card card--prod"]//div[@class="cardBody"]//a[@class="name"]//@title');
                $prices = $xpath->evaluate('//div[@class="SearchPage_SearchList__HL7RI col"][1]//div[@class="card card--prod"]//div[@class="cardBody"]//span[@class="mainValue"]');
                $images = $xpath->evaluate('//div[@class="SearchPage_SearchList__HL7RI col"][1]//div[@class="card card--prod"]//img[@class="image"]/@src');
                $arrayLength = $images->length;
                $pagination = $xpath->evaluate('//div[@class="SearchPage_Pagination__3TZvP col"][1]//ul[@class="ais-Pagination-list ais-Pagination"]//li');
                $pagination = (!empty($pagination) || $pagination > 3 ? $pagination->length - 3 : null);


                $products = [];
                for ($i = 0; $i < $arrayLength; $i++){
                    $product = new \stdClass();
                    $product->thumbnail = $images[$i]->value;
                    $product->title = $titles[$i]->value;
                    $product->price = (float)preg_replace("/\D/","", $prices[$i]->textContent);
                    $product->permalink = "https://www.buscape.com.br/{$category}{$links[$i]->value}";
                    $products[] = $product;
                }
                if (count($products) <= $pagination){
                    $pager->pager(1, 1, $page);
                } else {
                    $pager->pager($pagination, 1, $page, 1);
                }

                echo $this->view->render("home", [
                    "products" => $products,
                    "paginator" => $pager->render("Page"),
                    "category" => $category,
                    "search" => urldecode($search),
                    "web" => $data['web']
                ]);
            } else {
                $this->message->warning("selecione uma categoria e pesquise por algum produto!")->flash();
                redirect("/");
            }
        }
    }

    public function error(?array $error)
    {
        $errCode = filter_var($error['errcode']);
        echo $this->view->render("error", [
           "errCode" => $errCode
        ]);
    }

}