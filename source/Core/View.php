<?php


namespace Source\Core;


use League\Plates\Engine;

/**
 * Class View
 * @package Source\Core
 */
class View
{
    /**
     * @var Engine
     */
    protected Engine $engine;

    /**
     * View constructor.
     * @param string $baseDir
     * @param string $ext
     */
    public function __construct(string $baseDir = CONF_VIEW_PATCH, string $ext = CONF_VIEW_EXT)
    {
        $this->engine = Engine::create($baseDir, $ext);
    }

    /**
     * @param string $name
     * @param string $patch
     * @return $this
     */
    public function patch(string $name, string $patch): View
    {
        $this->engine->addFolder($name, $patch);
        return $this;
    }

    /**
     * @param string $templateName
     * @param array $data
     * @return string
     */
    public function render(string $templateName, array $data): string
    {
        return $this->engine->render($templateName, $data);
    }

    /**
     * @return Engine
     */
    public function engine(): Engine
    {
        return $this->engine;
    }
}