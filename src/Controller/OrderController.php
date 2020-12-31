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
        $this->sendMail();
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
                $pay='a';
                $total=0;
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
                        $total += (int) $gh['quantity'] * (int) $gh['price'];
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

    public function editOrder($request) {
        $id = isset($_GET['id']) ? $_GET['id'] : 0;
        $pro=$this->db->query("SELECT* FROM orders WHERE id=$id")->fetch_assoc();
        $View = new AdminView();
        $View->renderEditOrder($pro);
    }

    public function updateOrder($request) {
        $id = isset($_GET['id']) ? $_GET['id'] : 0;
        $status = isset($_GET['status']) ? $_GET['status']: 0;
        if($status == 1) {
            $this->sendMail();
        }
        $this->db->query("UPDATE orders SET status=$status WHERE id=$id");
        header('location:/?action=editOrder&id='.$id);
    }

    public function sendMail() {

        $email = new \SendGrid\Mail\Mail();

// Set the email parameters
        $email->setFrom("vanhieu13546@gmail.com", "Dan Shop");
        $email->setSubject("Dan shop");

        $email->addTo("buithanhtoan377@gmail.com", "Marcus Battle");
        $email->addContent("text/html", "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional //EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">
<head>
<!--[if gte mso 9]>
<xml>
  <o:OfficeDocumentSettings>
    <o:AllowPNG/>
    <o:PixelsPerInch>96</o:PixelsPerInch>
  </o:OfficeDocumentSettings>
</xml>
<![endif]-->
  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">
  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
  <meta name=\"x-apple-disable-message-reformatting\">
  <!--[if !mso]><!--><meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\"><!--<![endif]-->
  <title></title>
  
    <style type=\"text/css\">
      a { color: #0000ee; text-decoration: underline; } @media (max-width: 480px) { #u_content_menu_1 .v-padding { padding: 5px 40px !important; } #u_content_menu_2 .v-padding { padding: 5px 35px 5px 20px !important; } #u_content_menu_4 .v-padding { padding: 5px 45px 5px 40px !important; } #u_content_menu_3 .v-padding { padding: 5px 45px 5px 50px !important; } }
[owa] .u-row .u-col {
  display: table-cell;
  float: none !important;
  vertical-align: top;
}

.ie-container .u-row,
[owa] .u-row {
  width: 640px !important;
}

.ie-container .u-col-100,
[owa] .u-col-100 {
  width: 640px !important;
}


@media only screen and (min-width: 660px) {
  .u-row {
    width: 640px !important;
  }
  .u-row .u-col {
    vertical-align: top;
  }

  .u-row .u-col-100 {
    width: 640px !important;
  }

}

@media (max-width: 660px) {
  .u-row-container {
    max-width: 100% !important;
    padding-left: 0px !important;
    padding-right: 0px !important;
  }
  .u-row .u-col {
    min-width: 320px !important;
    max-width: 100% !important;
    display: block !important;
  }
  .u-row {
    width: calc(100% - 40px) !important;
  }
  .u-col {
    width: 100% !important;
  }
  .u-col > div {
    margin: 0 auto;
  }
  .no-stack .u-col {
    min-width: 0 !important;
    display: table-cell !important;
  }

  .no-stack .u-col-100 {
    width: 100% !important;
  }

}
body {
  margin: 0;
  padding: 0;
}

table,
tr,
td {
  vertical-align: top;
  border-collapse: collapse;
}

p {
  margin: 0;
}

.ie-container table,
.mso-container table {
  table-layout: fixed;
}

* {
  line-height: inherit;
}

a[x-apple-data-detectors='true'] {
  color: inherit !important;
  text-decoration: none !important;
}

.ExternalClass,
.ExternalClass p,
.ExternalClass span,
.ExternalClass font,
.ExternalClass td,
.ExternalClass div {
  line-height: 100%;
}

@media (max-width: 480px) {
  .hide-mobile {
    display: none !important;
    max-height: 0px;
    overflow: hidden;
  }
}

@media (min-width: 481px) {
  .hide-desktop {
    display: none !important;
    max-height: none !important;
  }
}
    </style>
  
  

</head>

<body class=\"clean-body\" style=\"margin: 0;padding: 0;-webkit-text-size-adjust: 100%;background-color: #e7e7e7\">
  <!--[if IE]><div class=\"ie-container\"><![endif]-->
  <!--[if mso]><div class=\"mso-container\"><![endif]-->
  <table class=\"nl-container\" style=\"border-collapse: collapse;table-layout: fixed;border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;vertical-align: top;min-width: 320px;Margin: 0 auto;background-color: #e7e7e7;width:100%\" cellpadding=\"0\" cellspacing=\"0\">
  <tbody>
  <tr style=\"vertical-align: top\">
    <td style=\"word-break: break-word;border-collapse: collapse !important;vertical-align: top\">
    <!--[if (mso)|(IE)]><table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tr><td align=\"center\" style=\"background-color: #e7e7e7;\"><![endif]-->
    

<div class=\"u-row-container\" style=\"padding: 0px;background-color: transparent\">
  <div style=\"Margin: 0 auto;min-width: 320px;max-width: 640px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: #ffffff;\" class=\"u-row\">
    <div style=\"border-collapse: collapse;display: table;width: 100%;background-color: #ffffff;\">
      <!--[if (mso)|(IE)]><table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tr><td style=\"padding: 0px;background-color: transparent;\" align=\"center\"><table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" style=\"width:640px;\"><tr style=\"background-color: #ffffff;\"><![endif]-->
      
<!--[if (mso)|(IE)]><td align=\"center\" width=\"640\" style=\"width: 640px;padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;\" valign=\"top\"><![endif]-->
<div class=\"u-col u-col-100\" style=\"max-width: 320px;min-width: 640px;display: table-cell;vertical-align: top;\">
  <div style=\"width: 100% !important;\">
  <!--[if (!mso)&(!IE)]><!--><div style=\"padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;\"><!--<![endif]-->
  
<table id=\"u_content_image_1\" class=\"u_content_image\" style=\"font-family:arial,helvetica,sans-serif;\" role=\"presentation\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" border=\"0\">
  <tbody>
    <tr>
      <td style=\"overflow-wrap:break-word;word-break:break-word;padding:15px 10px 10px;font-family:arial,helvetica,sans-serif;\" align=\"left\">
        
<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
  <tr>
    <td style=\"padding-right: 0px;padding-left: 0px;\" align=\"center\">
      
      <img align=\"center\" border=\"0\" src=\"https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQsF_4pltiCgMZP_rdPDlj_K-HnY_1F0HMBAw&usqp=CAU\" alt=\"Image\" title=\"Image\" style=\"outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block !important;border: none;height: auto;float: none;width: 100%;max-width: 174px;\" width=\"174\" class=\"v-src-width v-src-max-width\"/>
      
    </td>
  </tr>
</table>

      </td>
    </tr>
  </tbody>
</table>

  <!--[if (!mso)&(!IE)]><!--></div><!--<![endif]-->
  </div>
</div>
<!--[if (mso)|(IE)]></td><![endif]-->
      <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
    </div>
  </div>
</div>



<div class=\"u-row-container\" style=\"padding: 0px;background-color: transparent\">
  <div style=\"Margin: 0 auto;min-width: 320px;max-width: 640px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: #ffffff;\" class=\"u-row\">
    <div style=\"border-collapse: collapse;display: table;width: 100%;background-color: #ffffff;\">
      <!--[if (mso)|(IE)]><table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tr><td style=\"padding: 0px;background-color: transparent;\" align=\"center\"><table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" style=\"width:640px;\"><tr style=\"background-color: #ffffff;\"><![endif]-->
      
<!--[if (mso)|(IE)]><td align=\"center\" width=\"640\" style=\"width: 640px;padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;\" valign=\"top\"><![endif]-->
<div class=\"u-col u-col-100\" style=\"max-width: 320px;min-width: 640px;display: table-cell;vertical-align: top;\">
  <div style=\"width: 100% !important;\">
  <!--[if (!mso)&(!IE)]><!--><div style=\"padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;\"><!--<![endif]-->
  
<table id=\"u_content_divider_1\" class=\"u_content_divider\" style=\"font-family:arial,helvetica,sans-serif;\" role=\"presentation\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" border=\"0\">
  <tbody>
    <tr>
      <td style=\"overflow-wrap:break-word;word-break:break-word;padding:10px 10px 20px;font-family:arial,helvetica,sans-serif;\" align=\"left\">
        
  <table height=\"0px\" align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"border-collapse: collapse;table-layout: fixed;border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;vertical-align: top;border-top: 1px solid #444444;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%\">
    <tbody>
      <tr style=\"vertical-align: top\">
        <td style=\"word-break: break-word;border-collapse: collapse !important;vertical-align: top;font-size: 0px;line-height: 0px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%\">
          <span>&#160;</span>
        </td>
      </tr>
    </tbody>
  </table>

      </td>
    </tr>
  </tbody>
</table>

  <!--[if (!mso)&(!IE)]><!--></div><!--<![endif]-->
  </div>
</div>
<!--[if (mso)|(IE)]></td><![endif]-->
      <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
    </div>
  </div>
</div>



<div class=\"u-row-container\" style=\"padding: 0px;background-color: transparent\">
  <div style=\"Margin: 0 auto;min-width: 320px;max-width: 640px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: #ffffff;\" class=\"u-row\">
    <div style=\"border-collapse: collapse;display: table;width: 100%;background-color: #ffffff;\">
      <!--[if (mso)|(IE)]><table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tr><td style=\"padding: 0px;background-color: transparent;\" align=\"center\"><table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" style=\"width:640px;\"><tr style=\"background-color: #ffffff;\"><![endif]-->
      
<!--[if (mso)|(IE)]><td align=\"center\" width=\"640\" style=\"width: 640px;padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;\" valign=\"top\"><![endif]-->
<div class=\"u-col u-col-100\" style=\"max-width: 320px;min-width: 640px;display: table-cell;vertical-align: top;\">
  <div style=\"width: 100% !important;\">
  <!--[if (!mso)&(!IE)]><!--><div style=\"padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;\"><!--<![endif]-->
  
<table id=\"u_content_image_2\" class=\"u_content_image\" style=\"font-family:arial,helvetica,sans-serif;\" role=\"presentation\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" border=\"0\">
  <tbody>
    <tr>
      <td style=\"overflow-wrap:break-word;word-break:break-word;padding:20px 10px 15px;font-family:arial,helvetica,sans-serif;\" align=\"left\">
        
<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
  <tr>
    <td style=\"padding-right: 0px;padding-left: 0px;\" align=\"center\">
      
      <img align=\"center\" border=\"0\" src=\"https://img.bayengage.com/assets/1602239729297-tick.jpg\" alt=\"Image\" title=\"Image\" style=\"outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block !important;border: none;height: auto;float: none;width: 100%;max-width: 106px;\" width=\"106\" class=\"v-src-width v-src-max-width\"/>
      
    </td>
  </tr>
</table>

      </td>
    </tr>
  </tbody>
</table>

  <!--[if (!mso)&(!IE)]><!--></div><!--<![endif]-->
  </div>
</div>
<!--[if (mso)|(IE)]></td><![endif]-->
      <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
    </div>
  </div>
</div>



<div class=\"u-row-container\" style=\"padding: 0px;background-color: transparent\">
  <div style=\"Margin: 0 auto;min-width: 320px;max-width: 640px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: #ffffff;\" class=\"u-row\">
    <div style=\"border-collapse: collapse;display: table;width: 100%;background-color: #ffffff;\">
      <!--[if (mso)|(IE)]><table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tr><td style=\"padding: 0px;background-color: transparent;\" align=\"center\"><table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" style=\"width:640px;\"><tr style=\"background-color: #ffffff;\"><![endif]-->
      
<!--[if (mso)|(IE)]><td align=\"center\" width=\"640\" style=\"width: 640px;padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;\" valign=\"top\"><![endif]-->
<div class=\"u-col u-col-100\" style=\"max-width: 320px;min-width: 640px;display: table-cell;vertical-align: top;\">
  <div style=\"width: 100% !important;\">
  <!--[if (!mso)&(!IE)]><!--><div style=\"padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;\"><!--<![endif]-->
  
<table id=\"u_content_text_1\" class=\"u_content_text\" style=\"font-family:arial,helvetica,sans-serif;\" role=\"presentation\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" border=\"0\">
  <tbody>
    <tr>
      <td style=\"overflow-wrap:break-word;word-break:break-word;padding:15px 10px;font-family:arial,helvetica,sans-serif;\" align=\"left\">
        
  <div class=\"v-text-align\" style=\"color: #000000; line-height: 140%; text-align: left; word-wrap: break-word;\">
    <p style=\"font-size: 14px; line-height: 140%; text-align: center;\"><strong><span style=\"font-size: 24px; line-height: 33.6px;\">Cảm ơn bạn đã mua hàng</span></strong></p>
<p style=\"font-size: 14px; line-height: 140%; text-align: center;\"><strong><span style=\"font-size: 24px; line-height: 33.6px;\">tại Dan Shop!</span></strong></p>
  </div>

      </td>
    </tr>
  </tbody>
</table>

  <!--[if (!mso)&(!IE)]><!--></div><!--<![endif]-->
  </div>
</div>
<!--[if (mso)|(IE)]></td><![endif]-->
      <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
    </div>
  </div>
</div>



<div class=\"u-row-container\" style=\"padding: 0px;background-color: transparent\">
  <div style=\"Margin: 0 auto;min-width: 320px;max-width: 640px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: #ffffff;\" class=\"u-row\">
    <div style=\"border-collapse: collapse;display: table;width: 100%;background-color: #ffffff;\">
      <!--[if (mso)|(IE)]><table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tr><td style=\"padding: 0px;background-color: transparent;\" align=\"center\"><table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" style=\"width:640px;\"><tr style=\"background-color: #ffffff;\"><![endif]-->
      
<!--[if (mso)|(IE)]><td align=\"center\" width=\"640\" style=\"width: 640px;padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;\" valign=\"top\"><![endif]-->
<div class=\"u-col u-col-100\" style=\"max-width: 320px;min-width: 640px;display: table-cell;vertical-align: top;\">
  <div style=\"width: 100% !important;\">
  <!--[if (!mso)&(!IE)]><!--><div style=\"padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;\"><!--<![endif]-->
  
<table id=\"u_content_text_2\" class=\"u_content_text\" style=\"font-family:arial,helvetica,sans-serif;\" role=\"presentation\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" border=\"0\">
  <tbody>
    <tr>
      <td style=\"overflow-wrap:break-word;word-break:break-word;padding:15px 10px;font-family:arial,helvetica,sans-serif;\" align=\"left\">
        

      </td>
    </tr>
  </tbody>
</table>

  <!--[if (!mso)&(!IE)]><!--></div><!--<![endif]-->
  </div>
</div>
<!--[if (mso)|(IE)]></td><![endif]-->
      <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
    </div>
  </div>
</div>

<div class=\"u-row-container\" style=\"padding: 0px;background-color: transparent\">
  <div style=\"Margin: 0 auto;min-width: 320px;max-width: 640px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: #ffffff;\" class=\"u-row\">
    <div style=\"border-collapse: collapse;display: table;width: 100%;background-color: #ffffff;\">
      <!--[if (mso)|(IE)]><table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tr><td style=\"padding: 0px;background-color: transparent;\" align=\"center\"><table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" style=\"width:640px;\"><tr style=\"background-color: #ffffff;\"><![endif]-->
      
<!--[if (mso)|(IE)]><td align=\"center\" width=\"640\" style=\"width: 640px;padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;\" valign=\"top\"><![endif]-->
<div class=\"u-col u-col-100\" style=\"max-width: 320px;min-width: 640px;display: table-cell;vertical-align: top;\">
  <div style=\"width: 100% !important;\">
  <!--[if (!mso)&(!IE)]><!--><div style=\"padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;\"><!--<![endif]-->
  
<table id=\"u_content_divider_3\" class=\"u_content_divider\" style=\"font-family:arial,helvetica,sans-serif;\" role=\"presentation\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" border=\"0\">
  <tbody>
    <tr>
      <td style=\"overflow-wrap:break-word;word-break:break-word;padding:10px 10px 15px;font-family:arial,helvetica,sans-serif;\" align=\"left\">
        
  <table height=\"0px\" align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"border-collapse: collapse;table-layout: fixed;border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;vertical-align: top;border-top: 1px solid #BBBBBB;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%\">
    <tbody>
      <tr style=\"vertical-align: top\">
        <td style=\"word-break: break-word;border-collapse: collapse !important;vertical-align: top;font-size: 0px;line-height: 0px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%\">
          <span>&#160;</span>
        </td>
      </tr>
    </tbody>
  </table>

      </td>
    </tr>
  </tbody>
</table>

  <!--[if (!mso)&(!IE)]><!--></div><!--<![endif]-->
  </div>
</div>
<!--[if (mso)|(IE)]></td><![endif]-->
      <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
    </div>
  </div>
</div>



<div class=\"u-row-container\" style=\"padding: 0px;background-color: transparent\">
  <div style=\"Margin: 0 auto;min-width: 320px;max-width: 640px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: #ffffff;\" class=\"u-row\">
    <div style=\"border-collapse: collapse;display: table;width: 100%;background-color: #ffffff;\">
      <!--[if (mso)|(IE)]><table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tr><td style=\"padding: 0px;background-color: transparent;\" align=\"center\"><table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" style=\"width:640px;\"><tr style=\"background-color: #ffffff;\"><![endif]-->
      
<!--[if (mso)|(IE)]><td align=\"center\" width=\"640\" style=\"width: 640px;padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;\" valign=\"top\"><![endif]-->
<div class=\"u-col u-col-100\" style=\"max-width: 320px;min-width: 640px;display: table-cell;vertical-align: top;\">
  <div style=\"width: 100% !important;\">
  <!--[if (!mso)&(!IE)]><!--><div style=\"padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;\"><!--<![endif]-->
  
<table id=\"u_content_social_1\" class=\"u_content_social\" style=\"font-family:arial,helvetica,sans-serif;\" role=\"presentation\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" border=\"0\">
  <tbody>
    <tr>
      <td style=\"overflow-wrap:break-word;word-break:break-word;padding:15px 10px;font-family:arial,helvetica,sans-serif;\" align=\"left\">
        
<div align=\"center\">
  <div style=\"display: table; max-width:125px;\">
  <!--[if (mso)|(IE)]><table width=\"125\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tr><td style=\"border-collapse:collapse;\" align=\"center\"><table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" style=\"border-collapse:collapse; mso-table-lspace: 0pt;mso-table-rspace: 0pt; width:125px;\"><tr><![endif]-->
  
    
    <!--[if (mso)|(IE)]><td width=\"32\" style=\"width:32px; padding-right: 10px;\" valign=\"top\"><![endif]-->
    <table align=\"left\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"32\" height=\"32\" style=\"border-collapse: collapse;table-layout: fixed;border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;vertical-align: top;Margin-right: 10px\">
      <tbody><tr style=\"vertical-align: top\"><td align=\"left\" valign=\"middle\" style=\"word-break: break-word;border-collapse: collapse !important;vertical-align: top\">
        <a href=\"https://facebook.com/\" title=\"Facebook\" target=\"_blank\">
          <img src=\"https://cdn.tools.unlayer.com/social/icons/circle-black/facebook.png\" alt=\"Facebook\" title=\"Facebook\" width=\"32\" style=\"outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block !important;border: none;height: auto;float: none;max-width: 32px !important\">
        </a>
      </td></tr>
    </tbody></table>
    <!--[if (mso)|(IE)]></td><![endif]-->
    
    <!--[if (mso)|(IE)]><td width=\"32\" style=\"width:32px; padding-right: 10px;\" valign=\"top\"><![endif]-->
    <table align=\"left\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"32\" height=\"32\" style=\"border-collapse: collapse;table-layout: fixed;border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;vertical-align: top;Margin-right: 10px\">
      <tbody><tr style=\"vertical-align: top\"><td align=\"left\" valign=\"middle\" style=\"word-break: break-word;border-collapse: collapse !important;vertical-align: top\">
        <a href=\"https://twitter.com/\" title=\"Twitter\" target=\"_blank\">
          <img src=\"https://cdn.tools.unlayer.com/social/icons/circle-black/twitter.png\" alt=\"Twitter\" title=\"Twitter\" width=\"32\" style=\"outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block !important;border: none;height: auto;float: none;max-width: 32px !important\">
        </a>
      </td></tr>
    </tbody></table>
    <!--[if (mso)|(IE)]></td><![endif]-->
    
    <!--[if (mso)|(IE)]><td width=\"32\" style=\"width:32px; padding-right: 0px;\" valign=\"top\"><![endif]-->
    <table align=\"left\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"32\" height=\"32\" style=\"border-collapse: collapse;table-layout: fixed;border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;vertical-align: top;Margin-right: 0px\">
      <tbody><tr style=\"vertical-align: top\"><td align=\"left\" valign=\"middle\" style=\"word-break: break-word;border-collapse: collapse !important;vertical-align: top\">
        <a href=\"https://instagram.com/\" title=\"Instagram\" target=\"_blank\">
          <img src=\"https://cdn.tools.unlayer.com/social/icons/circle-black/instagram.png\" alt=\"Instagram\" title=\"Instagram\" width=\"32\" style=\"outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;clear: both;display: block !important;border: none;height: auto;float: none;max-width: 32px !important\">
        </a>
      </td></tr>
    </tbody></table>
    <!--[if (mso)|(IE)]></td><![endif]-->
    
    
    <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
  </div>
</div>

      </td>
    </tr>
  </tbody>
</table>

  <!--[if (!mso)&(!IE)]><!--></div><!--<![endif]-->
  </div>
</div>
<!--[if (mso)|(IE)]></td><![endif]-->
      <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
    </div>
  </div>
</div>



<div class=\"u-row-container\" style=\"padding: 0px;background-color: transparent\">
  <div style=\"Margin: 0 auto;min-width: 320px;max-width: 640px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: #ffffff;\" class=\"u-row\">
    <div style=\"border-collapse: collapse;display: table;width: 100%;background-color: #ffffff;\">
      <!--[if (mso)|(IE)]><table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tr><td style=\"padding: 0px;background-color: transparent;\" align=\"center\"><table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" style=\"width:640px;\"><tr style=\"background-color: #ffffff;\"><![endif]-->
      
<!--[if (mso)|(IE)]><td align=\"center\" width=\"640\" style=\"width: 640px;padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;\" valign=\"top\"><![endif]-->
<div class=\"u-col u-col-100\" style=\"max-width: 320px;min-width: 640px;display: table-cell;vertical-align: top;\">
  <div style=\"width: 100% !important;\">
  <!--[if (!mso)&(!IE)]><!--><div style=\"padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;\"><!--<![endif]-->
  
<table id=\"u_content_text_3\" class=\"u_content_text\" style=\"font-family:arial,helvetica,sans-serif;\" role=\"presentation\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" border=\"0\">
  <tbody>
    <tr>
      <td style=\"overflow-wrap:break-word;word-break:break-word;padding:15px 10px 30px;font-family:arial,helvetica,sans-serif;\" align=\"left\">

      </td>
    </tr>
  </tbody>
</table>

  <!--[if (!mso)&(!IE)]><!--></div><!--<![endif]-->
  </div>
</div>
<!--[if (mso)|(IE)]></td><![endif]-->
      <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
    </div>
  </div>
</div>


    <!--[if (mso)|(IE)]></td></tr></table><![endif]-->
    </td>
  </tr>
  </tbody>
  </table>
  <!--[if (mso)|(IE)]></div><![endif]-->
</body>

</html>");

        $sendgrid = new \SendGrid('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');

// Send the email
        try {
            $response = $sendgrid->send($email);
        } catch (Exception $e) {
            echo 'Caught exception: '. $e->getMessage() ."\n";
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
