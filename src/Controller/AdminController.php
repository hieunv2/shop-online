<?php


class AdminController implements ControllerInterface
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

    public function redirectAction($route = "/")
    {
        header("location: $route");
        exit;
    }

    public function indexAction($request)
    {
        $user = $this->userManager->isLoggedIn();
        if($user) {
            if($user['group_name'] === 'admin') {
                $View = new AdminView();
                $View->renderIndex();
            } else {
                $this->redirectAction();

            }
        } else {
            $this->redirectAction('/login');
        }
    }


}
