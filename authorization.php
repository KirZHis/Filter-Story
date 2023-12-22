<p style="font-size: 26px;"><a href="index.php">Каталог</a></p>

<?php
    session_start();

    try {
        if (!file_exists('users.json')) throw new Exception('Файла .json не существует');
        $str = file_get_contents('users.json');
        if (!json_decode($str, 1)) throw new Exception('Файл .json поврежден');
        $users = json_decode($str, 1);
    } catch (Exception $ex) {
        echo $ex->getMessage();
        exit;
    }


    if (isset($_POST['submit'])) {
        foreach ($users as $user) {
            if ($user['login'] == $_POST['login'] and $user['password'] == $_POST['password']) {
                $_SESSION['auth'] = $user['role'];
                $_SESSION['login'] = $user['login'];
                $auth = true;
                break;
            }
        }

        if (!isset($_SESSION['auth'])) {
            echo 'Неправильный логин или пароль<br><br>';
        }
    }

    if (!isset($_SESSION['auth'])) {
?>
        <form action="" method="post">
            <label for="login">Логин</label><br>
            <input type="text" name="login" required><br>

            <label for="password">Пароль</label><br>
            <input type="password" name="password"required><br>

            <button type="submit" name="submit">Отправить</button>
        </form>
<?php
    } else {
        header('Location: index.php');
        exit;
    }
?>