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

// Получаем список доступных номеров
$sql = "SELECT * FROM rooms";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Бронирование номеров</title>
    <link rel="stylesheet" href="styles.css"> <!-- Ссылка на CSS -->
</head>
<body>
    <header>
        <h1>Добро пожаловать на сайт бронирования</h1>
        <nav>
            <ul>
                <li><a href="index.php">Главная</a></li>
                <li><a href="profile.php">Профиль</a></li>
                <li><a href="logout.php">Выход</a></li>
            </ul>
        </nav>
    </header>

    <!-- Новый блок с обложкой и информацией о гостинице -->
    <section class="hotel-info">
        <div class="container">
            <img src="images/hotel-cover.jpg" alt="Обложка отеля" class="hotel-cover">
            <div class="hotel-description">
                <h2>Добро пожаловать в наш отель на берегу моря!</h2>
                <p>Наслаждайтесь комфортом и уютом нашего отеля, расположенного в живописном месте на самом берегу моря. Мы предлагаем широкий выбор номеров, от стандартных до люксов с видом на море, чтобы сделать ваше пребывание незабываемым.</p>
                <p>Наши гости могут наслаждаться не только высококачественным обслуживанием, но и живописными видами, прекрасными пляжами и эксклюзивными удобствами. Откройте для себя лучший отдых с нами!</p>
            </div>
        </div>
    </section>

    <!-- Новый блок с отзывами -->
    <section class="reviews">
        <div class="container">
            <h2>Отзывы наших гостей</h2>
            <div class="review-list">
                <div class="review-card">
                    <p class="review-text">"Отель просто супер! Мы с семьей провели здесь незабываемый отпуск. Вид на море потрясающий, а обслуживание на высшем уровне. Обязательно вернемся!"</p>
                    <p class="review-author">Марина, Санкт-Петербург</p>
                </div>
                <div class="review-card">
                    <p class="review-text">"Очень уютный отель. Прекрасные номера, вкусная еда и внимательный персонал. Место просто идеально для отдыха. Рекомендую всем!"</p>
                    <p class="review-author">Алексей, Москва</p>
                </div>
                <div class="review-card">
                    <p class="review-text">"Провели пару дней в этом отеле, и все было просто замечательно. Чисто, комфортно, тихо. Пляж рядом, рекомендую для отдыха с детьми!"</p>
                    <p class="review-author">Елена, Казань</p>
                </div>
            </div>
        </div>
    </section>
    <section class="services">
    <div class="container">
        <h2>Наши услуги</h2>
        <div class="service-list">
            <div class="service-card">
             
                <h3>Спа и массажи</h3>
                <p>Расслабьтесь в нашем спа-комплексе, где вас ждут массажи и процедуры для полного расслабления.</p>
            </div>
            <div class="service-card">
        
                <h3>Ресторан</h3>
                <p>Наш ресторан предлагает лучшие блюда местной и международной кухни.</p>
            </div>
            <div class="service-card">
             
                <h3>Бесплатный Wi-Fi</h3>
                <p>Оставайтесь на связи с нашим бесплатным высокоскоростным интернетом на всей территории отеля.</p>
            </div>
        </div>
    </div>
</section>


    <main>
        <h2>Доступные номера</h2>
        <?php if ($result->num_rows > 0): ?>
            <div class="room-list">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="room-card">
                        <img src="<?= $row['image']; ?>" alt="<?= $row['name']; ?>">
                        <h3><?= $row['name']; ?></h3>
                        <p><?= $row['description']; ?></p>
                        <p><strong>Цена: <?= $row['price']; ?> руб.</strong></p>
                        <a href="booking.php?room_id=<?= $row['id']; ?>" class="btn-book">Забронировать</a>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p>К сожалению, в данный момент нет доступных номеров.</p>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; 2025 Ваш отель. Все права защищены.</p>
    </footer>
</body>
</html>
