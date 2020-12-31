<?php

class BlogController implements ControllerInterface
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
        return $this->listAction($request);
    }

    public function blogAction($request) {
        $View = new BlogView($this->blogManager);
        $View->renderBlogs();
    }

    public function blogViewAction($request) {
        $View = new BlogView($this->blogManager);
        $View->renderBlog();
    }

    public function listAction($request)
    {
        $limit=20;
        $total=$this->db->query("SELECT id FROM product")->num_rows;
        $pages=ceil($total/$limit);
        $page=isset($_GET['page']) ? $_GET['page'] : 1;
        $start=($page-1)*$limit;
        $products = $this->blogManager->findAllProducts($start);
        $View = new BlogView($this->blogManager);
        $View->renderView($products, $pages);
    }

    public function listProducts($request)
    {
        $limit=20;
        $total=$this->db->query("SELECT id FROM product")->num_rows;
        $pages=ceil($total/$limit);
        $page=isset($_GET['page']) ? $_GET['page'] : 1;
        $start=($page-1)*$limit;
        $sql  = "SELECT p.*,c.name as 'cat_name' FROM product p JOIN category c ON p.category_id = c.id  ORDER BY id ASC LIMIT $start,$limit";
        $products = $this->db->query($sql);

        $View = new AdminView();
        $View->renderListProducts($products, $pages);
    }

    public function addProduct($request) {
        $View = new AdminView();
        $View->renderAddProduct();
    }

    public function createProduct($request) {
        $image = '';

        if (!empty($_FILES['image']['name'])) {
            $f = $_FILES['image'];
            $f_name = time().'-'.$f['name'];

            if (move_uploaded_file($f['tmp_name'], '../../../public/image'.$f_name)) {
                $image = $f['name'];
            }
            // die;
        }

        if (isset($_POST['name'])) {
            $name = $_POST['name'];
            $category_id = $_POST['category_id'];
            $price = $_POST['price'];
            $sale_price = $_POST['sale_price'];
            $description = $_POST['description'];
            $note = $_POST['note'];
            $status = $_POST['status'];

            $sql = "INSERT INTO product(name,category_id,price,sale_price,description,note,image,status) VALUES('$name','$category_id','$price','$sale_price','$description','$note','$image','$status')";

            if ($this->db->query($sql)) {
                $product_id=$this->db->insert_id;
                if (!empty($_FILES['img']['name'])&&count($_FILES['img']['name'])>0) {
                    $n=count($_FILES['img']['name']);
                    $f = $_FILES['img'];
                    for ($i = 0; $i <$n ; $i++) {
                        $f_name = time().'-'.$f['name'][$i];

                        if (move_uploaded_file($f['tmp_name'][$i], '../../../public/image/'.$f_name)) {
                            $this->db->query("INSERT INTO product_image(link_img,product_id) VALUES('$f_name',$product_id)");
                        }
                    }
                    // die;
                }
                header('location: /list_products');
            }else{
                echo "Có lỗi thêm mới";
            }
        }

    }

    public function editProduct($request) {
        $id = isset($_GET['id']) ? $_GET['id'] : 0;
        $pro=$this->db->query("SELECT* FROM product WHERE id=$id")->fetch_assoc();
        $View = new AdminView();
        $View->renderEditProduct($pro);
    }

    public function updateProduct($request) {
        $id = isset($_GET['id']) ? $_GET['id'] : 0;
        $pro=$this->db->query("SELECT* FROM product WHERE id=$id")->fetch_assoc();
        $image = $pro['image'];

        if (!empty($_FILES['image']['name'])) {
            $f = $_FILES['image'];
            $f_name = time().'-'.$f['name'];

            if (move_uploaded_file($f['tmp_name'], '../../image/'.$f_name)) {
                $image = $f_name;
            }
            // die;
        }

        if (isset($_POST['name'])) {
            $name = $_POST['name'];
            $cat_name=$_POST['cat_name'];
            $status = $_POST['status'];

            $sql = "UPDATE product SET image='$image' ,name='$name',category_id='$cat_name',status='$status',note='$note' WHERE id=$id";

            if ($this->db->query($sql)) {
                header('location: /list_products');
            }else{
                echo "Có lỗi sửa";
            }
        }

    }

    public function deleteProduct($request) {
        $id = isset($_GET['id']) ? $_GET['id'] : 0;

        if ($this->db->query("DELETE FROM product WHERE id=$id")) {
            header('location: /list_products');
        }else{
            echo "Lỗi xoá sản phẩm";
        }
    }

    public function getProducts($request)
    {
        $name = isset($request['name'])? $request['name'] : '';
        $id = isset($request['id']) ? $request['id'] : 0;
        $products = $this->blogManager->findAllProductsByCategory($id, $name);
        $View = new BlogView($this->blogManager);
        $View->renderProducts($products);
    }



    public function viewAction($request)
    {
        $post = $this->blogManager->findOnePostById($request['id']);
        $View = new BlogView($this->blogManager);
        $View->renderView($post);
    }

    public function postAction($request)
    {
        $post = $this->blogManager->findOnePostById($request['id']);
        $View = new BlogView($this->blogManager);
        $View->renderView($post);
    }


    public function addAction($request)
    {
        $View = new BlogView($this->blogManager);
        $View->renderView($request);
    }


    public function addpostsubmittedAction($request)
    {
        $res = null;
        $userid = $this->userManager->isLoggedIn();
        if ($userid) {
            $res = $this->blogManager->addPost($request['title'], $request['content'], $userid);
        }
        if ($res) {
            $this->redirectAction();
        }
        else {
            $this->redirectAction("/?action=add&error=error");
        }
    }


    public function addcommentsubmittedAction($request)
    {
        $res = $this->blogManager->addComment(
            $request['name'],
            $request['content'],
            $request['post_id'],
            $request['email'],
            $request['url']
        );
        if ($res) $this->redirectAction("/?action=post&id=".$request['post_id']);
        else $this->redirectAction("/?action=post&id={$request['post_id']}&error=error");
    }


    public function redirectAction($route="/")
    {
        header("location: $route");
        exit;
    }
}
