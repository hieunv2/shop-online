<?php
$carts=isset($_SESSION['cart'])?$_SESSION['cart']:[];
function tong_so_luong(){

    $t=0;
    global $carts;
    $carts=isset($_SESSION['cart'])?$_SESSION['cart']:[];
    foreach ($carts as $cart) {
        $t= $t+$cart['quantity'];

    }


    return $t;
}
function tong_tien(){

    $t=0;
    global $carts;
    $carts=isset($_SESSION['cart'])?$_SESSION['cart']:[];
    foreach ($carts as $cart) {
        $t= $t+$cart['quantity']*$cart['price'];

    }


    return $t;
}

?>
