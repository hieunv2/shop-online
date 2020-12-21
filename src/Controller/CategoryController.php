<?php

class CategoryController implements ControllerInterface
{
    /**
     * $blogManager model instance
     * @var BlogManager
     */
    private $blogManager;

    private $cartManager;

    /**
     * $blogManager model instance
     * @var UserManager
     */
    private $userManager;

    private $db;


    public function __construct($blogManager, $userModel, $cartModel, $dbConnection)
    {
        $this->blogManager = $blogManager;
        $this->cartManager = $cartModel;
        $this->userManager = $userModel;

        if ($dbConnection instanceof mysqli) {
            $this->db = $dbConnection;
        } else {
            throw new Exception('Connection injected should be of Mysqli object');
        }
    }


    public function indexAction($request)
    {
        $View = new BlogView($this->blogManager);
        $View->indexView();
    }



    public function addCategory($request) {
        $View = new AdminView();
        $View->renderAddCategory();
    }

    public function createCategory($request){
        if (!empty($_POST)) {
            $name = $_POST['name'];
            $status = $_POST['status'];
            $sql = "INSERT INTO category(name,status) VALUES('$name','$status')";
            if ($this->db->query($sql)) {
                header('location:/list_categories');
            }
        }
    }

    public function deleteCategory($request) {
        $id = isset($_GET['id']) ? $_GET['id'] : 0;

        if ($this->db->query("DELETE FROM category WHERE id=$id")) {
            header('location: /list_categories');
        }else{
            echo "Lỗi xoá sản phẩm";
        }
    }


    public function aboutAction()
    {
        $View = new BlogView($this->blogManager);
        $View->renderView();
    }

    public function listCategories($request)
    {
        $cats = $this->db->query("SELECT * FROM category");
        $View = new AdminView();
        $View->renderListCategories($cats);
    }


    public function redirectAction($route="/")
    {
        header("location: $route");
        exit;
    }

}
