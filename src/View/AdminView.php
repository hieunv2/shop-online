<?php


class AdminView
{

    private $content2; // content of page output


    public function __construct()
    {

        $tmp = \debug_backtrace();
        $this->controller = \str_replace("controller", "", \strtolower($tmp[1]['class']));
        $this->action = \str_replace("action", "", \strtolower($tmp[1]['function']));
    }


    public function __destruct()
    {
        include '../src/View/Layout/authLayout.phtml';
    }


    public function renderView($variables = null)
    {
        \ob_start();
        require "../src/View/{$this->controller}/{$this->action}.phtml";
        $this->content2 = \ob_get_clean();
    }


    public function renderIndex()
    {
        \ob_start();
        require "../src/View/admin/index.phtml";
        $this->content2 = \ob_get_clean();
    }

    public function renderListProducts($variables = null, $pages=1)
    {
        \ob_start();
        require "../src/View/admin/listProducts.phtml";
        $this->content2 = \ob_get_clean();
    }

    public function renderListUsers($variables = null)
    {
        \ob_start();
        require "../src/View/admin/listUsers.phtml";
        $this->content2 = \ob_get_clean();
    }

    public function renderListCustomers($variables = null)
    {
        \ob_start();
        require "../src/View/admin/listCustomers.phtml";
        $this->content2 = \ob_get_clean();
    }

    public function renderListCategories($variables = null){
        \ob_start();
        require "../src/View/admin/listCategories.phtml";
        $this->content2 = \ob_get_clean();
    }

    public function renderListOrders($variables = null)
    {
        \ob_start();
        require "../src/View/admin/listOrders.phtml";
        $this->content2 = \ob_get_clean();
    }



    public function renderAddUser(){
        \ob_start();
        require "../src/View/admin/addUser.phtml";
        $this->content2 = \ob_get_clean();
    }

    public function renderAddProduct(){
        \ob_start();
        require "../src/View/admin/addProduct.phtml";
        $this->content2 = \ob_get_clean();
    }

    public function renderEditProduct($variables = null) {
        \ob_start();
        require "../src/View/admin/editProduct.phtml";
        $this->content2 = \ob_get_clean();
    }

    public function indexView()
    {
        $this->content2 = "Blog sample.
        Click <a href ='/about'>here</a> to see";
    }


}
