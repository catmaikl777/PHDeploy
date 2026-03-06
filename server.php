<?php
$file = 'game_state.json';
$timeout = 3; // Удалять игрока через 3 секунды неактивности

echo "OK;";

// Получаем данные из GET-запроса
$id = $_POST['id'] ?? null;
$x  = $_POST['x']  ?? 0;
$y  = $_POST['y']  ?? 0;
$a  = $_POST['a']  ?? 0; // Угол (angle)
$f  = $_POST['f']  ?? 0; // Факт выстрела (fire)
$h  = $_POST['h']  ?? 100; // Здоровье (hp)

if (!$id) {
    echo "OK; No ID provided"; // Для проверки в браузере
    exit;
}

// Загружаем данные
$players = file_exists($file) ? json_decode(file_get_contents($file), true) : [];

// Обновляем текущего игрока
$players[$id] = [
    'x' => $x, 'y' => $y, 'a' => $a, 'f' => $f, 'h' => $h, 't' => time()
];

// Очистка старых и формирование ответа
$output = "";
$currentTime = time();

foreach ($players as $pid => $data) {
    // Если игрок долго не отвечал — удаляем
    if ($currentTime - $data['t'] > $timeout) {
        unset($players[$pid]);
        continue;
    }
    
    // Добавляем в список всех, кроме самого себя
    if ($pid != $id) {
        // Формат: id, x, y, угол, выстрел, хп;
        $output .= "$pid,{$data['x']},{$data['y']},{$data['a']},{$data['f']},{$data['h']};";
    }
}

// Сохраняем обновленный список в файл
file_put_contents($file, json_encode($players));

// Отправляем данные в Pocket Code
echo $output;
?>
