<?php

namespace Displore\Core\Contracts;

interface MenuBuilder
{
    /**
     * Get a menu based on its name.
     * 
     * @param  string $name
     * @return mixed
     */
    public function get($name);

    /**
     * Add a menu to the stack.
     * 
     * @param  string $name
     * @param  mixed  $menu
     * @return $this
     */
    public function add($name, $menu);

    /**
     * Turn a menu into HTML.
     * @param  string $name
     * @return string
     */
    public function build($name);

    /**
     * Populate the stack from an array of menus.
     * @param  array $config
     * @return $this
     */
    public function from(array $config);
}
