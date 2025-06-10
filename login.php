<?php
session_start();
require_once 'includes/db.php';

// Если пользователь уже авторизован, перенаправляем его на нужную страницу
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: admin/dashboard.php');
        exit();
    } else {
        header('Location: profile.php');
        exit();
    }
}

// Обработка формы входа
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Проверяем, заполнены ли все поля
    if (empty($email) || empty($password)) {
        $error = "Пожалуйста, заполните все поля.";
    } else {
        // Поиск пользователя в базе данных
        $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($user_id, $hashed_password, $role);
            $stmt->fetch();

            // Проверка пароля
            if (password_verify($password, $hashed_password)) {
                // Успешный вход
                $_SESSION['user_id'] = $user_id;
                $_SESSION['role'] = $role;

                // Перенаправление в зависимости от роли
                if ($role === 'admin') {
                    header('Location: admin/dashboard.php');
                    exit();
                } else {
                    header('Location: profile.php');
                    exit();
                }
            } else {
                $error = "Неверный пароль.";
            }
        } else {
            $error = "Пользователь с таким email не найден.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<div class="container">
    <h2>Вход в систему</h2>

    <?php if (!empty($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <input type="email" name="email" placeholder="Email" required><br><br>
        <input type="password" name="password" placeholder="Пароль" required><br><br>
        <button type="submit">Войти</button>
    </form>

    <p>Нет аккаунта? <a href="register.php">Регистрация</a></p>
</div>

<?php include 'includes/footer.php'; ?>

</body>
</html>
