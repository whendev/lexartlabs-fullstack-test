<?php


namespace Source\Models;


use GuzzleHttp\Client;
use Meli\Api\OAuth20Api;
use Meli\ApiException;
use Requests;
use Source\Core\Model;
use Source\Core\Session;

class MlApi extends Model
{
    public function __construct()
    {
        parent::__construct("ml_api", ["client_id"], ["id"]);
    }

    public function auth(string $code)
    {
        $apiInstance = new OAuth20Api(
            new Client()
        );
        $grant_type = 'authorization_code';
        $client_id = $_ENV["CONF_CLIENT_ID"];
        $client_secret = $_ENV["CONF_SECRET_KEY"];
        $redirect_uri = $_ENV["CONF_REDIRECT_URI"];
        $refresh_token = null;

        try {
            $result = $apiInstance->getToken($grant_type, $client_id, $client_secret, $redirect_uri, $code, $refresh_token);
            return $result;
        } catch (ApiException $e) {
            return false;
        }
    }

    public function setAuth(object $auth): bool
    {
        $clientId = $_ENV["CONF_CLIENT_ID"];
        $ml = (new MlApi())->find("client_id = :id", "id={$clientId}")->fetch();
        if ($ml){
            $ml->access_token = $auth->access_token;
            $ml->refresh_token = $auth->refresh_token;
        } else {
            $ml = (new MlApi());
            $ml->access_token = $auth->access_token;
            $ml->token_type = $auth->token_type;
            $ml->client_id = $clientId;
            $ml->user_id = $auth->user_id;
            $ml->refresh_token = $auth->refresh_token;
        }
        return $ml->save();
    }

    public function getToken()
    {
        $client_id = $_ENV["CONF_CLIENT_ID"];
        $find = $this->find("client_id = :id", "id={$client_id}")->fetch();
        if ($find){
            if (!empty($find->updated_at)){
                $lastUpdate = new \DateTime($find->updated_at);
                $dateNow = new \DateTime();
                $diff = $lastUpdate->diff($dateNow);
                if ($diff->h >= 6){
                    if ($this->refresh_token()){
                        return ($this->find("client_id = :id", "id={$client_id}")->fetch())->access_token;
                    } else {
                        return null;
                    }
                }
                return $find->access_token;
            } else {
                if ($this->refresh_token()){
                    return ($this->find("client_id = :id", "id={$client_id}")->fetch())->access_token;
                } else {
                    return null;
                }
            }
        }
        return null;
    }

    public function refresh_token(): bool
    {
        $clientId = $_ENV["CONF_CLIENT_ID"];
        $ml = (new MlApi())->find("client_id = :id", "id={$clientId}")->fetch();
        if ($ml){
            $secret_key = $_ENV["CONF_SECRET_KEY"];
            Requests::register_autoloader();
            $headers = array();
            $response = Requests::post("https://api.mercadolibre.com/oauth/token?grant_type=refresh_token&client_id={$clientId}&client_secret={$secret_key}&refresh_token={$ml->refresh_token}", $headers);
            $response = json_decode($response->body);
            if ($this->setAuth($response)){
                return true;
            }
        }
        return false;
    }

}