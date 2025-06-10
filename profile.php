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

// Получаем данные о пользователе
$user_id = $_SESSION['user_id'];

// Обработка запроса на отмену бронирования
if (isset($_GET['cancel_id'])) {
    $cancel_id = $_GET['cancel_id'];

    // Запрос на изменение статуса бронирования
    $sql = "UPDATE bookings SET status = 'cancelled' WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $cancel_id, $user_id);
    $stmt->execute();

    // Проверяем, был ли запрос выполнен успешно
    if ($stmt->affected_rows > 0) {
        // Перенаправляем на страницу профиля после успешной отмены
        header('Location: profile.php');
        exit();
    } else {
        $error_message = 'Не удалось отменить бронирование. Попробуйте еще раз.';
    }
}

// Запрос на получение всех бронирований пользователя
$sql = "SELECT b.id, b.check_in, b.check_out, b.status, r.name AS room_name, r.price 
        FROM bookings b
        JOIN rooms r ON b.room_id = r.id
        WHERE b.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мой профиль</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Мой профиль</h1>

    </header>

    <main>
        <h2>Мои бронирования</h2>

        <?php if (isset($error_message)): ?>
            <p style="color: red;"><?= $error_message; ?></p>
        <?php endif; ?>

        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Номер</th>
                        <th>Дата заезда</th>
                        <th>Дата выезда</th>
                        <th>Статус</th>
                        <th>Цена</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($booking = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $booking['room_name']; ?></td>
                            <td><?= $booking['check_in']; ?></td>
                            <td><?= $booking['check_out']; ?></td>
                            <td><?= ucfirst($booking['status']); ?></td>
                            <td><?= $booking['price']; ?> руб.</td>
                            <td>
                                <?php if ($booking['status'] == 'booked'): ?>
                                    <a href="profile.php?cancel_id=<?= $booking['id']; ?>" 
                                       onclick="return confirm('Вы уверены, что хотите отменить бронирование?');">
                                       Отменить
                                    </a>
                                <?php else: ?>
                                    Отменено
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>У вас нет активных бронирований.</p>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; 2025 Ваш отель. Все права защищены.</p>
    </footer>
</body>
</html>
