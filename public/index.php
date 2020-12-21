<?php
// Retrieve configuration
$appConfig = require __DIR__ . '/../config/application.config.php';

// the Application initialisation/entry point.
$routeAction = $_SERVER["REQUEST_URI"];
if (isset($_GET['action'])) {
    $routeAction = $_GET['action'];
}

// router
switch ($routeAction) {

    case '/cart':
    case 'cart':
        $controllerName = CartController::class;
        $action = 'cartAction';
        break;
    case '/updateCart':
    case 'updateCart':
    $controllerName = CartController::class;
    $action = 'updateCart';
    break;
    case 'deleteCart':
        $controllerName = CartController::class;
        $action = 'deleteAction';
        break;
    case 'order':
    case '/order':
        $controllerName = OrderController::class;
        $action = 'orderAction';
        break;
    case 'createOrder':
    case '/createOrder':
        $controllerName = OrderController::class;
        $action = 'createOrderAction';
        break;
    case '/about':
    case 'about':
        $controllerName = CategoryController::class;
        $action = 'aboutAction';
        break;

    case 'products':
    case '/products':
        $controllerName = 'BlogController';
        $action = 'getProducts';
        break;

    case 'product':
        $controllerName = 'BlogController';
        $action = 'postAction';
        break;

    case 'addpost':
        $controllerName = 'BlogController';
        $action = 'addAction';
        break;

    case 'addpostsubmitted':
        $controllerName = 'BlogController';
        $action = 'addpostsubmittedAction';
        break;

    case 'addcomment':
        $controllerName = 'BlogController';
        $action = 'addcommentAction';
        break;

    case 'addcommentsubmitted':
        $controllerName = 'BlogController';
        $action = 'addcommentsubmittedAction';
        break;

    case '/login':
    case 'login':
        $controllerName = AuthController::class;
        $action = 'loginAction';
        break;

    case 'register':
    case '/register':
    $controllerName = AuthController::class;
    $action = 'registerAction';
    break;

    case '/loginsubmitted':
    case 'loginsubmitted':
        $controllerName = AuthController::class;
        $action = 'loginsubmittedAction';
        break;

    case '/logout':
    case 'logout':
        $controllerName = AuthController::class;
        $action = 'logoutAction';
        break;


    case 'admin':
    case '/admin':
        $controllerName = AdminController::class;
        $action = 'indexAction';
        break;


    case '/add_product':
        $controllerName = BlogController::class;
        $action = 'addProduct';
        break;

    case 'editProduct':
    case '/editProduct':
        $controllerName = BlogController::class;
        $action = 'editProduct';
        break;

    case 'updateProduct':
    case '/updateProduct':
        $controllerName = BlogController::class;
        $action = 'updateProduct';
        break;

    case 'deleteProduct':
    case '/deleteProduct':
        $controllerName = BlogController::class;
        $action = 'deleteProduct';
        break;

    case 'createProduct':
        $controllerName = BlogController::class;
        $action = 'createProduct';
        break;

    case '/add_user':
        $controllerName = AuthController::class;
        $action = 'addUser';
        break;

    case 'createUser':
        $controllerName = AuthController::class;
        $action = 'createUser';
        break;

    case '/list_users':
        $controllerName = AuthController::class;
        $action = 'listUsers';
        break;

    case '/list_customers':
        $controllerName = AuthController::class;
        $action = 'listCustomers';
        break;



    case '/list_categories':
        $controllerName = CategoryController::class;
        $action = 'listCategories';
        break;


    case '/list_orders':
        $controllerName = OrderController::class;
        $action = 'listOrders';
        break;

    case 'list':
    case '/':
        $controllerName = 'BlogController';
        $action = 'indexAction';
        break;

    case '/list_products':
    default:
        $controllerName = BlogController::class;
        $action = 'listProducts';
        break;


}

require '../src/Controller/ControllerInterface.php';
require '../src/Controller/' . $controllerName . '.php';
require '../src/Model/DbConnectionManager.php';
require '../src/Model/BlogManager.php';
require '../src/Model/UserManager.php';
require '../src/Model/CartManager.php';
require '../src/View/BlogView.php';
require '../src/View/CartView.php';
require '../src/View/LoginView.php';
require '../src/View/AdminView.php';


$db = new DbConnectionManager($appConfig);
$dbConnection = null;
if ($db) {
    $dbConnection = $db->getConnection();
}
$blogManager = new BlogManager($dbConnection);
$userManager = new UserManager($dbConnection);
$cartManager = new CartManager($dbConnection);


$controller = new $controllerName($blogManager, $userManager, $cartManager, $dbConnection);


$controller->{$action}($_REQUEST);


