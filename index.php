<?php
  session_start();

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


 if (!isset($_SESSION['auth'])) {
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
<?php
 } else {
  echo "<p style='font-size: 26px;'>Пользователь, " . $users[$userInd]['name'] . "."; 
    ?>
    <a style='font-size: 26px;' href="?out">Выйти</a></p>
    <?php

    if ($_SESSION['auth'] !== 'admin') {?>
      <a style="font-size: 26px;", href='favourites.php'>Избранное</a><?php
    }
 }
?>

<?php 
  try {
    if (!file_exists('catalog.json')) throw new Exception('Файла .json не существует');
    $str = file_get_contents('catalog.json');
    if (!json_decode($str, 1)) throw new Exception('Файл .json поврежден');
    $catalog = json_decode($str, 1);
  } catch (Exception $ex) {
    echo $ex->getMessage();
    exit;
  }

  usort($catalog, fn($a, $b) => strcmp($a['category'], $b['category']));

  if (isset($_SESSION['auth'])) {
  if ($_SESSION['auth'] == 'admin') {
?>
<form action="" method="post" enctype="multipart/form-data">
  <p>
    <label>Имя</label><br>
    <input type="text" name="name" required>
  </p>

  <p>
    <label>Описание</label><br>
    <textarea name="description" rows="4" cols="50" required></textarea>
  </p>

  <p>
    <label>Категория</label><br>
    <select name="category">
      <option value="New" selected>Не выбрано</option>
      <?php
      $cat = "";
      foreach ($catalog as $product) {
        if ($cat != $product['category']) {
          echo "<option value=" . $product['category'] . ">" . $product['category'] . "</option>";
          $cat = $product['category'];
        }
      }
      ?>
    </select>
    <label>или введите новую</label>
    <input type="text" name="new_category">
  </p>

  <p>
  <label>Цена</label><br>
    <input type="number" name="price" required min=0>
  </p>

  <p>
  <label>Картинка товара</label>
    <input type="file" name="img" accept="image/*">
  </p>

  <p>
  <label>Количество товаров</label><br>
    <input type="number" name="stock" required min="0">
  </p>

  <p>
    <label>Скидка</label><br>
    <input type="text" name="offer">
  </p>
  <button type="submit" name="submit">Отправить</button>
</form>
<?php
  }}

  if(isset($_POST['submit'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    ($_POST['category'] != "New") ? $category = $_POST['category'] : $category = $_POST['new_category'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    (isset($_POST['offer'])) ? $offer = $_POST['offer'] : $offer="";
    $imgName = "";

    if(isset($_FILES['img']) && $_FILES['img']['error'] == 0) {
      $imgName = "img/" . $_FILES['img']['name'];
      move_uploaded_file($_FILES['img']['tmp_name'], $imgName);
    }

    $catalog[] = ["id" => $catalog[-1]["id"]+1,"name" => $name, "description" => $description, "category" => $category, 
    "price" => $price, "stock" => $stock, "offer" => $offer, "imageUrl" => $imgName];

    $str = json_encode($catalog);
    file_put_contents('catalog.json', $str);

    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
  }

  if(isset($_GET['favourite'])) {
    $users[$userInd]["favourites"][] = (integer)$_GET['favourite'];
    $users[$userInd]["favourites"] = array_unique($users[$userInd]["favourites"]);

    $str = json_encode($users);
    file_put_contents('users.json', $str);

    header('Location: ' . $_SERVER['PHP_SELF']);
  }

  if(isset($_GET['delete'])) {
    $deleteKey = $_GET['delete'];

    $imgPath = $catalog[$deleteKey]['imageUrl'];
    if(file_exists($imgPath)) {
        unlink($imgPath);
    }

    unset($catalog[$deleteKey]);

    $str = json_encode($catalog);
    file_put_contents('catalog.json', $str);

    header('Location: ' . $_SERVER['PHP_SELF']);
  }

  if (isset($_GET['out'])) {
    session_destroy();

    header('Location: ' . $_SERVER['PHP_SELF']);
  }

  $cat = "";
  foreach ($catalog as $key=>$product) {
      if ($cat != $product['category']) {
          echo "<h1>{$product['category']}</h1>";
          $cat = $product['category'];
      }

      echo "Название: " . $product['name'] . "<br>";
      echo ($product['imageUrl'] != '') ? "<img src='" . $product['imageUrl'] . "' width='200' height='200' alt='" . $product['name'] . "'><br>" : $product['imageUrl'];
      echo "Описание: " . $product['description'] . "<br>";
      echo "Цена: " . $product['price'] . "<br>";
      echo ($product['stock'] != 0 ? "В наличии: " . $product['stock'] . " шт" : "Нет в наличии") . "<br>";
      if ($product['offer'] !== '') echo "Акция: " . $product['offer'] . "<br>";
      if (isset($_SESSION['auth'])) {
        echo ($_SESSION['auth'] == 'admin') ? "<a href='?delete=" . $key . "'>удалить</a><br>" : "<a href='?favourite=" . $product["id"] . "'>Добавить в избранное</a><br>";
      }
      echo "<hr>";
  }
?>