<?php
// Подключаем файл с настройками для подключения к базе данных
require_once 'includes/db.php';
include 'includes/header.php';
session_start();

// Проверка, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Перенаправление на страницу входа, если пользователь не авторизован
    exit();
}

// Получаем ID номера из URL
if (!isset($_GET['room_id'])) {
    die('Ошибка: Номер не выбран');
}

$room_id = intval($_GET['room_id']);

// Проверка, существует ли выбранный номер в базе данных
$sql = "SELECT * FROM rooms WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $room_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die('Ошибка: Номер не найден');
}

$room = $result->fetch_assoc();

// Переменная для сообщений
$success_message = '';
$error = '';

// Обработка формы бронирования
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Получаем данные из формы
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    
    // Проверяем, корректность данных
    if (empty($check_in) || empty($check_out)) {
        $error = 'Пожалуйста, выберите даты заезда и выезда.';
    } else {
        // Вставка данных о бронировании в базу данных
        $user_id = $_SESSION['user_id'];
        $sql = "INSERT INTO bookings (user_id, room_id, check_in, check_out, status) VALUES (?, ?, ?, ?, 'booked')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('iiss', $user_id, $room_id, $check_in, $check_out);
        
        if ($stmt->execute()) {
            // Успех
            $success_message = 'Бронирование прошло успешно! Мы отправим вам подтверждение на email.';
        } else {
            $error = 'Ошибка при бронировании. Попробуйте позже.';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Бронирование номера: <?= $room['name']; ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Бронирование номера</h1>
        <nav>
            <ul>
                <li><a href="index.php">Главная</a></li>
                <li><a href="profile.php">Профиль</a></li>
                <li><a href="logout.php">Выход</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h2>Номер: <?= $room['name']; ?></h2>
        <img src="<?= $room['image']; ?>" alt="<?= $room['name']; ?>">
        <p><?= $room['description']; ?></p>
        <p><strong>Цена: <?= $room['price']; ?> руб.</strong></p>

        <?php if ($success_message): ?>
            <p class="success"><?= $success_message; ?></p>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <p class="error"><?= $error; ?></p>
        <?php endif; ?>

        <form method="POST">
            <label for="check_in">Дата заезда:</label>
            <input type="date" name="check_in" id="check_in" required>

            <label for="check_out">Дата выезда:</label>
            <input type="date" name="check_out" id="check_out" required>

            <button type="submit" class="btn-book">Забронировать</button>
        </form>
    </main>

    <footer>
        <p>&copy; 2025 Ваш отель. Все права защищены.</p>
    </footer>
</body>
</html>
