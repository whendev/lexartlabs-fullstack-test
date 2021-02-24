<?php


namespace Source\Support;


use Source\Core\Session;

/**
 * Class Message
 * @package Source\support
 */
class Message
{
    /**
     * @var string
     */
    private string $type = "";

    /**
     * @var string
     */
    private string $text = "";

    /**
     * @var string
     */
    private string $before = "";

    /**
     * @var string
     */
    private string $after = "";


    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->render();
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->before.$this->text.$this->after;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $text
     * @return $this
     */
    public function before(string $text): Message
    {
        $this->before = "<strong>{$text}</strong>";
        return $this;
    }

    /**
     * @param string $text
     * @return $this
     */
    public function after(string $text): Message
    {
        $this->after = $text;
        return $this;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function error(string $message): Message
    {
        $this->type = CONF_MESSAGE_ERROR;
        $this->text = $this->filter($message);
        return $this;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function warning(string $message): Message
    {
        $this->type = CONF_MESSAGE_WARNING;
        $this->text = $this->filter($message);
        return $this;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function info(string $message): Message
    {
        $this->type = CONF_MESSAGE_INFO;
        $this->text = $this->filter($message);
        return $this;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function success(string $message): Message
    {
        $this->type = CONF_MESSAGE_SUCCESS;
        $this->text = $this->filter($message);
        return $this;
    }

    /**
     * @return string
     */
    public function render(): string
    {
        return "<div class='".CONF_MESSAGE_CLASS." {$this->type}'>
                   {$this->text}
                  <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                    <span aria-hidden='true'>&times;</span>
                  </button>
                </div>";
    }

    /**
     * @return false|string
     */
    public function json()
    {
        return json_encode(["error" => $this->getText()]);
    }

    /**
     * SET FLASH MESSAGE
     */
    public function flash(): void
    {
        (new Session())->set("flash", $this);
    }

    /**
     * @param string $message
     * @return mixed
     */
    public function filter(string $message)
    {
        return filter_var($message, FILTER_SANITIZE_STRIPPED);
    }
}