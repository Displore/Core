<?php

namespace Displore\Core\Menu;

use Displore\Core\Contracts\MenuBuilder;

class Builder implements MenuBuilder
{
    /**
     * All of the menus.
     * @var array
     */
    protected $stack;

    /**
     * {@inheritdoc}
     */
    public function get($name)
    {
        if (isset($this->stack[$name])) {
            return $this->stack[$name];
        }
        throw new \Exception("Menu {$name} not found");
    }

    /**
     * {@inheritdoc}
     */
    public function add($name, $menu)
    {
        $this->stack[$name] = $menu;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function build($name)
    {
        if (isset($this->stack[$name])) {
            return implode(' ', $this->stack[$name]);
        }
        throw new \Exception("Menu {$name} not found");
    }

    /**
     * {@inheritdoc}
     */
    public function from(array $config)
    {
        foreach ($config as $name => $menu) {
            $this->stack[$name] = $menu;
        }

        return $this;
    }
}
