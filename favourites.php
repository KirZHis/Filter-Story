<?php
    session_start();

    if (isset($_SESSION['auth'])) {
        $str = file_get_contents('users.json');
        $users = json_decode($str, 1);
        
        $userInd = 0;
        if (isset($_SESSION['login'])) {
            $count = 0;
            foreach ($users as $user) {
                if ($user["login"] == $_SESSION['login']) {
                $userInd = $count;
                break;
                }
                $count++;
            }
        }

        $str = file_get_contents('catalog.json');
        $catalog = json_decode($str, 1);

        if(isset($_GET['delete_fav'])) {
            $deleteProduct = $_GET['delete_fav'];
            $deleteKey = array_search($deleteProduct, $users[$userInd]['favourites']);
            unset($users[$userInd]['favourites'][$deleteKey]);
        
            $str = json_encode($users);
            file_put_contents('users.json', $str);
        
            header('Location: ' . $_SERVER['PHP_SELF']);
        }

        echo "<p style='font-size: 26px;'>Пользователь, " . $users[$userInd]['name'] . ".</p>";
        echo "<p style='font-size: 20px;'>Список избранного:</p>";

        echo "<ul>";
        if ($users[$userInd]["favourites"] != []) {
            foreach ($catalog as $product) {
                if (in_array($product["id"], $users[$userInd]["favourites"])) {
                    echo "<li>";
                    echo "Название: " . $product['name'] . "<br>";
                    echo ($product['imageUrl'] != '') ? "<img src='" . $product['imageUrl'] . "' width='200' height='200' alt='" . $product['name'] . "'><br>" : $product['imageUrl'];
                    echo "Описание: " . $product['description'] . "<br>";
                    echo "Цена: " . $product['price'] . "<br>";
                    echo ($product['stock'] != 0 ? "В наличии: " . $product['stock'] . " шт" : "Нет в наличии") . "<br>";
                    if ($product['offer'] !== '') echo "Акция: " . $product['offer'] . "<br>";
                    if (isset($_SESSION['auth'])) {
                    echo "<a href='?delete_fav=" . $product["id"] . "'>Удалить из избранного</a><br>";
                    }
                    echo "<hr>";
                    echo "</li>";
                }
            }
        } else {
            echo "Пока ничего нет";
        }
        echo "</ul>";

    } else {
        ?>
        <style>
        .header {
            background-color: #f1f1f1;
            padding: 10px 0;
            text-align: center;
        }

        .header a {
            margin: 0 10px;
            text-decoration: none;
            font-size: 26px;
        }

        .header p {
            font-size: 26px;
            background-color: #f1f1f1;
        }
        </style>

        <div class="header">
        <a href="register.php">Регистрация</a> |
        <a href="authorization.php">Авторизация</a>
        </div>
        <p style="font-size: 26px;"><a href="index.php">Каталог</a></p>
        <?php
        echo "<p style='font-size: 26px;'>Вы еще не авторизованы!</p>";
    }
?>