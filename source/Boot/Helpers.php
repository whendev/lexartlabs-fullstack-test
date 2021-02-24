<?php


// REQUEST
use Source\Core\Session;

/**
 * @return string
 */
function csrf_input(): string
{
    $session = new Session();
    $session->csrf();
    return "<input type='hidden' name='csrf' value='" . ($session->csrf_token ?? "") . "'/>";
}

/**
 * @param $request
 * @return bool
 */
function csrf_verify($request): bool
{
    $session = new Session();
    if (empty($session->csrf_token) || empty($request['csrf']) || $request['csrf'] != $session->csrf_token) {
        return false;
    }
    return true;
}


/**
 * @return string|null
 */
function flash(): ?string
{
    $session = new Session();
    if ($flash = $session->flash()){
        echo $flash;
    }
    return null;
}


// URL

function search_url(string $category, string $search, int $offset = 0, int $limit = 5): ?string
{
    $categoryOptions = [
        "geladeira" => "1576",
        "celular" => "1051",
        "tv" => "1002"
    ];
    $category = (!empty($category) && array_key_exists($category, $categoryOptions) ? $categoryOptions[$category] : null);
    $search = (!empty($search) ? "q={$search}&" : '');
    if ($category){
        return "https://api.mercadolibre.com/sites/MLB/search?category={$category}&{$search}offset={$offset}&limit={$limit}";
    }
    return null;
}

/**
 * @param ?string $path
 * @return string
 */
function url(string $path = null): string
{
    $url = $_SERVER['HTTP_HOST'];
    $find = 'localhost';
    $pos = strpos($url, $find);
    if ($pos === false){
        if ($path){
            return CONF_URL_BASE . "/" . ($path[0] == "/" ? mb_substr($path, 1) : $path);
        }
        return CONF_URL_BASE;

    } else {
        if ($path){
            return CONF_URL_TEST. "/" . ($path[0] == "/" ? mb_substr($path, 1) : $path);
        }
        return CONF_URL_TEST;
    }
}


/**
 * @param string $url
 */
function redirect(string $url): void
{
    header("HTTP/1.1 302 Redirect");
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        header("Location: {$url}");
        exit;
    }

    if (filter_input(INPUT_GET, "route", FILTER_DEFAULT) != $url){
        $location = url($url);
        header("Location: {$location}");
        exit;
    }
}

// ASSETS


/**
 * @param string|null $path
 * @param string $theme
 * @return string
 */
function theme(string $path = null, string $theme = CONF_VIEW_APP): string
{
    $url = $_SERVER['HTTP_HOST'];
    $find = 'localhost';
    $pos = strpos($url, $find);

    if ($pos === false){
        if ($path){
            return CONF_URL_BASE. "/resources/views/{$theme}/".($path[0] == "/" ? mb_substr($path, 1) : $path);
        }
        return CONF_URL_BASE."/resources/views/{$theme}";

    }
    if ($path){
        return CONF_URL_TEST. "/resources/views/{$theme}/".($path[0] == "/" ? mb_substr($path, 1) : $path);
    }

    return CONF_URL_TEST."/resources/views/{$theme}";
}


// VALIDATIONS

function is_email(string $email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * @param string $password
 * @return bool
 */
function is_passwd(string $password): bool
{
    if (password_get_info($password)['algo'] || mb_strlen($password) >= CONF_PASSWD_MIN_LEN && mb_strlen($password) <= CONF_PASSWD_MAX_LEN){
        return true;
    }
    return false;
}

/**
 * @param string $password
 * @return string
 */
function passwd(string $password): string
{
    if (!empty(password_get_info($password)['algo'])){
        return $password;
    }

    return password_hash($password, CONF_PASSWD_ALGO, CONF_PASSWD_OPTIONS);
}

/**
 * @param string $password
 * @param string $hash
 * @return bool
 */
function passwd_verify(string $password, string $hash): bool
{
    return password_verify($password, $hash);
}

/**
 * @param string $hash
 * @return bool
 */
function passwd_rehash(string $hash): bool
{
    return password_needs_rehash($hash, CONF_PASSWD_ALGO, CONF_PASSWD_OPTIONS);
}
