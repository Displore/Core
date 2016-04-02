<?php


class MenusTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->builder = app('Displore\Core\Menu\Builder');
    }

    public function test_can_add_and_get_menu()
    {
        $mainMenu = [
            '<a href="#">Home</a>',
            '<a href="#">About</a>',
            '<a href="#">Contact</a>',
        ];
        $this->builder->add('mainMenu', $mainMenu);
        $menu = $this->builder->get('mainMenu');

        $this->assertInternalType('array', $menu);
    }

    public function test_can_build_menu()
    {
        $mainMenu = [
            '<a href="#">Home</a>',
            '<a href="#">About</a>',
            '<a href="#">Contact</a>',
        ];
        $this->builder->add('mainMenu', $mainMenu);
        $menu = $this->builder->build('mainMenu');

        $this->assertInternalType('string', $menu);
    }

    public function test_populating_from_config()
    {
        $config = [
            'sidemenu' => [
                '<a href="#">Home</a>',
                '<a href="#">About</a>',
                '<a href="#">Contact</a>',
            ],
        ];
        $build = $this->builder->from($config)->build('sidemenu');
        $get = $this->builder->get('sidemenu');

        $this->assertInternalType('array', $get);
        $this->assertInternalType('string', $build);
    }
}
