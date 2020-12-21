<?php


class CartController implements ControllerInterface
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


    public function __construct($blogManager, $userModel, $cartModel)
    {
        $this->blogManager = $blogManager;
        $this->cartManager = $cartModel;
        $this->userManager = $userModel;
    }

    public function updateCart($request) {
        if (isset($_POST['id'])) {
            $id1=isset($_POST['id']) ? $_POST['id'] : [];
            $quantity1=isset($_POST['quantity']) ? $_POST['quantity'] : [];
            for ($i = 0; $i < count($id1); $i++) {
                if (isset($_SESSION['cart'][$id1[$i]])) {
                    $_SESSION['cart'][$id1[$i]]['quantity'] = $quantity1[$i];
                }
            }
        }
        $View = new CartView($this->cartManager);
        $View->renderCart();
    }


    public function cartAction($request)
    {

        $id=isset($request['id']) ? $request['id'] : 0;
      if($id) {
          $quantity=isset($request['quantity']) ? $request['quantity'] : 1;
          $pr = $this->blogManager->findOnePostById($id);

          if($pr) {
              if (isset($_SESSION['cart'][$id])) {
                  $_SESSION['cart'][$id]['quantity']+=$quantity;
              } else {
                  $_SESSION['cart'][$id]=[
                      'id' => $pr['id'],
                      'name' => $pr['name'],
                      'image' => $pr['image'],
                      'price' => $pr['sale_price'] ? $pr['sale_price'] : $pr['price'],
                      'quantity' => $quantity,
                  ];
              }
          }
          $action=isset($_GET['action']) ? $_GET['action'] : 'null';
      }
        $View = new CartView($this->cartManager);
        $View->renderView($request);
    }

    public function deleteAction($request)
    {
        $id=isset($request['id']) ? $request['id'] : 0;
        if($id) {
            if (isset($_SESSION['cart'][$id])) {
                unset($_SESSION['cart'][$id]);
            }
        }
        $View = new CartView($this->cartManager);
        $View->renderCart();
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
