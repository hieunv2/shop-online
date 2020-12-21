<?php

class AuthController implements ControllerInterface
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
        $this->loginAction();
    }


    public function loginAction()
    {
        if ($this->userManager->isLoggedIn()) {
            $this->redirectAction();
        }

        $View = new LoginView();
        $View->renderLogin();
    }

    public function redirectAction($route = "/")
    {
        header("location: $route");
        exit;
    }

    public function registerAction($request)
    {
        if(isset($_POST['username'])){
            $name = $_POST['name'];
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $phone = $_POST['phone'];
            $address = $_POST['address'];
            $sql="INSERT INTO users(username,name,email,phone,address,birthday,password, group_name, gender, image) VALUES ('$username','$name','$email','$phone','$address','1999-01-26','$password','customer', 1, 'image') ";
            if($this->db->query($sql)) {
                $this->userManager->login($email, $password);
                $this->redirectAction();
            } else {
                echo "loi";
            }

        }

    }

    public function loginsubmittedAction($request)
    {
        $res = $this->userManager->login($request['email'], $request['password']);
        if ($res) {
            $this->redirectAction();
        } else {
            $this->redirectAction("/login");
        }
    }


    public function listUsers($request)
    {
        $users = $this->db->query("SELECT * FROM users WHERE group_name='admin'");
        $View = new AdminView();
        $View->renderListUsers($users);
    }

    public function listCustomers($request)
    {
        $users = $this->db->query("SELECT * FROM users WHERE group_name='customer'");
        $View = new AdminView();
        $View->renderListCustomers($users);
    }

    public function addUser($request) {
        $View = new AdminView();
        $View->renderAddUser();
    }

    public function createUser($request){
        $image = '';

        if (!empty($_FILES['image']['name'])) {
            $f = $_FILES['image'];
            $f_name = time().'-'.$f['name'];

            if (move_uploaded_file($f['tmp_name'], 'public/images/'.$f_name)) {
                $image = $f_name;
            }
        }

        if (!empty($_POST)) {
            $name=$_POST['name'];
            $username=$_POST['username'];
            $email=$_POST['email'];
            $phone=$_POST['phone'];
            $birthday=$_POST['birthday'];
            $address=$_POST['address'];
            $password=$_POST['password'];
            $confirm_password=$_POST['confirm_password'];
            $sql="INSERT INTO users(name,username,email,phone,birthday,address,password,group_name,image) VALUES('$name','$username','$email','$phone','$birthday','$address','$password','admin','$image')";
            if ($this->db->query($sql)) {
                header('location:/list_users');
            }
            }
    }

    public function logoutAction()
    {
        $this->userManager->logout();
        $this->redirectAction();
    }

}
