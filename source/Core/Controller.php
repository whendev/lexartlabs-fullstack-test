<?php


namespace Source\Core;



use Source\Support\Message;

/**
 * Class Controller
 * @package Source\Core
 */
class Controller
{

    /**
     * @var View
     */
    protected View $view;
    /**
     * @var Message
     */
    protected Message $message;

    /**
     * Controller constructor.
     * @param string|null $pathToViews
     */
    public function __construct(string $pathToViews = null)
    {
        $this->message = new Message();
        $this->view = new View($pathToViews);
    }

}