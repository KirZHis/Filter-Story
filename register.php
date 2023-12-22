<p style="font-size: 26px;"><a href="index.php">Каталог</a></p>

<form action="" method="post">
    <label for="name">Имя</label><br>
    <input type="text" name="name" required><br>

    <label for="email">Почта</label><br>
    <input type="email" name="email" required><br>

    <label for="login">Логин</label><br>
    <input type="text" name="login" required><br>

    <label for="password">Пароль</label><br>
    <input type="password" name="password"required><br>

    <label for="passwordConf">Пароль</label><br>
    <input type="password" name="passwordConf"required><br>

    <button type="submit" name="submit">Отправить</button>
</form>

<?php
    try {
        if (!file_exists('users.json')) throw new Exception('Файла .json не существует');
        $str = file_get_contents('users.json');
        if (!json_decode($str, 1)) throw new Exception('Файл .json поврежден');
        $users = json_decode($str, 1);
    } catch (Exception $ex) {
        echo $ex->getMessage();
        exit;
    }


    if(isset($_POST['submit'])) {
        if ($_POST['password'] == $_POST['passwordConf']) {
            echo 'Регистрация успешна<br>';

            $users[] = ["name" => $_POST['name'], "login" => $_POST['login'], 
            "password" => $_POST['password'], "email" => $_POST['email'], "role" => "user", "favourites" => []];

            $str = json_encode($users);
            file_put_contents('users.json', $str);

            echo "<a href='/authorization.php'>Авторизация</a>";
            exit;
        } else {
            echo 'Введенные пароли не совпадают';
        }
    }
?>