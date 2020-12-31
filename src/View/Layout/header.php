<header class="header">
    <div class="header__background">
        <div class="header__img">
            <img class="banner_image" src="./image/banner_background.jpg" alt="bg" />
        </div>
    </div>

    <nav class="nav bd-grid">
        <a href="/" class="nav__logo">
            <img src="./image/logoicon.png" alt="#" />
        </a>
        <div class="nav__box" id="nav-menu">
            <ul class="nav__list">
                <li class="nav__item">
                    <a href="/" class="nav__link">Trang chủ</a>
                </li>
                <li class="nav__item">
                    <a href="/blogs" class="nav__link">Bài viết</a>
                </li>
                <li class="nav__item">
                    <a href="#" class="nav__link">Liên hệ</a>
                </li>
            </ul>

            <div class="row">
                <div class="col">
                    <div class="main_nav_content" style="display: flex; flex-direction: row">
                        <?php if(isset($_SESSION['login'])) : $u = $_SESSION['login']; ?>
                            <div class="top_bar_user">
                                <div class="dropdown">
                                    <a href="#" class="dropbtn">
                                        <div class="user_icon"><img src="./image/user.svg" alt=""></div>
                                        <?php echo $u['name']; ?>
                                        <b class="fas fa-angle-down"></b></a>
                                    <div class="dropdown-content">
                                        <a href="?action=logout"><i class="fas fa-sign-out-alt" style="margin: 10px;"></i>Log out</a>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="top_bar_user">
                                <div class="nav__button">
                                    <a href="/login" class="nav__button-signup" >Đăng ký</a>
                                    <a href="/login" class="nav__button-signin">Đăng nhập</a>
                                </div>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>


        </div>
        <div class="nav__toggle" id="nav-toggle">
            <i class="bx bx-menu"></i>
        </div>
    </nav>

    <div class="header__information">
        <div class="header__title">
            <?php
            $pro=mysqli_query($conn,"SELECT * FROM product WHERE image like 'banner_product%'");
            $pr=mysqli_fetch_assoc($pro);
            ?>
            <div class="banner_product_image"><img src="./image/<?= $pr['image'] ?>" alt=""></div>
            <div class="header__description">
                <div class="banner_content">
                    <h1 class="banner_text">Sản phẩm bán chạy</h1>
                    <div class="banner_price"><span>$ <?= $pr['price'] ?></span>$<?= $pr['sale_price'] ?></div>
                    <div class="banner_product_name"><?= $pr['name'] ?></div>
                    <div class="button banner_button"><a href="?action=cart&&quantity=1&&id=<?= $pr['id'] ?>">Mua ngay</a></div>
                </div>
            </div>

        </div>
    </div>
</header>
