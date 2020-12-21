<?php


class OrderController implements ControllerInterface
{
    /**
     * $blogManager model instance
     * @var CartManager
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


    public function orderAction($request) {
        $View = new CartView($this->cartManager);
        $Login = new LoginView();
        if(isset($_SESSION['login'])) {
            $View->renderOrder();
        } else {
            header("location: /login");
        }
    }

    public function createOrderAction($request)
    {
        if (isset($_SESSION['login']))  {
            $u = $_SESSION['login'];
            $ghs=isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
            if (isset($_POST['btn_pay'])) {
                $user_id=$u['id'];
                $name=$_POST['fullname'];
                $email=$_POST['email'];
                $phone=$_POST['phone'];
                $address=$_POST['address'];
                $pay=$_POST['payment'];
                //1. lưu vào order để lấy order id
                $queryOd= $this->db->query("INSERT INTO orders(user_id) VALUES ($user_id)");
                if ($queryOd) {
                    $order_id=$this->db->insert_id;
                    $this->db->query("INSERT INTO receiver(user_id,order_id,name,email,phone,address,payment_method) VALUES($user_id,$order_id,'$name','$email','$phone','$address','$pay')");
                    //2.duyệt giỏ hàng vào order_detail
                    foreach ($ghs as $gh) {
                        $product_id=$gh['id'];
                        $quantity=$gh['quantity'];
                        $price=$gh['price'];
                        $this->db->query("INSERT INTO order_detail VALUES ($order_id,$product_id,$quantity,$price)");
                    }
                    // xóa gió hàng
                    unset($_SESSION['cart']);

                }

            }
            $this->redirectAction();

        } else {
            header("location: /login");
        }
    }


    public function listOrders($request){
        $sql="SELECT o.id,o.created_at,o.status,u.name,SUM(dt.price*dt.quantity) AS 'total' FROM orders o JOIN order_detail dt ON o.id=dt.order_id JOIN users u ON o.user_id=u.id  GROUP BY o.id,o.created_at,o.status,u.name ";
        $orders=$this->db->query($sql);

        $View = new AdminView();
        $View->renderListOrders($orders);
    }

    public function redirectAction($route = "/")
    {
        header("location: $route");
        exit;
    }

    public function indexAction($request)
    {
        // TODO: Implement indexAction() method.
    }
}
