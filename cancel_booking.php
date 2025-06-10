<?php
session_start();
require_once 'includes/db.php';

// Проверяем, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Получаем ID бронирования из параметра запроса
if (isset($_GET['id'])) {
    $booking_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    // Проверяем, существует ли бронирование и принадлежит ли оно текущему пользователю
    $stmt = $conn->prepare("SELECT id, user_id, status FROM bookings WHERE id = ?");
    $stmt->bind_param('i', $booking_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($booking_id, $booking_user_id, $status);
        $stmt->fetch();

        // Проверяем, что бронирование принадлежит текущему пользователю и его статус — "Забронирован"
        if ($booking_user_id === $user_id && $status === 'Забронирован') {
            // Отменяем бронирование
            $update_stmt = $conn->prepare("UPDATE bookings SET status = 'Отменено' WHERE id = ?");
            $update_stmt->bind_param('i', $booking_id);
            $update_stmt->execute();

            // Перенаправляем обратно на страницу профиля с уведомлением
            $_SESSION['message'] = "Бронирование отменено успешно.";
            header('Location: profile.php');
            exit();
        } else {
            $_SESSION['error'] = "Бронирование не найдено или оно уже отменено.";
        }
    } else {
        $_SESSION['error'] = "Бронирование не найдено.";
    }
} else {
    $_SESSION['error'] = "Неверный запрос.";
}

header('Location: profile.php');
exit();
