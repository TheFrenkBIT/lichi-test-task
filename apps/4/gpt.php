<?php
$mysqli = new \PDO('mysql:host=172.24.0.2;port=3306;dbname=mysql', 'root', 'root');

function printGroupList($parentId = 0) {
    global $mysqli;

    // Получение списка групп товаров
    $query = "SELECT * FROM `groups` WHERE id_parent = " . $parentId;
    $result = $mysqli->query($query);

    // Вывод списка групп товаров
    echo "<ul>";
    while ($row = $result->fetch()) {
        $groupId = $row['id'];
        $groupName = $row['name'];

        // Получение количества товаров в группе и всех ее подгруппах
        $totalProducts = getTotalProducts($groupId);

        // Вывод названия группы и количества товаров
        echo "<li><a href='?group=$groupId'>$groupName ($totalProducts)</a></li>";

        // Рекурсивный вызов для вывода подгрупп
        printGroupList($groupId);
    }
    echo "</ul>";
}

// Функция для получения общего количества товаров в группе и всех ее подгруппах
function getTotalProducts($groupId) {
    global $mysqli;

    // Получение количества товаров в текущей группе
    $query = "SELECT COUNT(*) AS count FROM products WHERE id_group = " . $groupId;
    $result = $mysqli->query($query);
    $row = $result->fetch();
    $totalProducts = $row['count'];

    // Рекурсивный вызов для подгрупп
    $query = "SELECT id FROM `groups` WHERE id_parent = " . $groupId;
    $result = $mysqli->query($query);
    while ($row = $result->fetch()) {
        $subGroupId = $row['id'];
        $totalProducts += getTotalProducts($subGroupId);
    }

    return $totalProducts;
}

// Проверка наличия GET-параметра "group"
if (isset($_GET['group'])) {
    $groupId = $_GET['group'];

    // Вывод списка подгрупп выбранной группы
    $groups = printGroupList($groupId);

    // Вывод списка товаров, отнесенных к выбранной группе и всем ее подгруппам
    $query = "SELECT * FROM `products` WHERE id_group IN (SELECT id FROM `groups` WHERE id = " . $groupId . " OR id_parent = " . $groupId . ")";
    $result = $mysqli->query($query);
    echo "<ul>";
    while ($row = $result->fetch()) {
        $productName = $row['name'];
        echo "<li>$productName</li>";
    }
    echo "</ul>";
} else {
    // Вывод списка групп товаров первого уровня
    $groups = printGroupList();

    // Вывод списка всех товаров
    $query = "SELECT * FROM products";
    $result = $mysqli->query($query);
    echo "<ul>";
    while ($row = $result->fetch()) {
        $productName = $row['name'];
        echo "<li>$productName</li>";
    }
    echo "</ul>";
}

// Закрытие соединения с базой данных
?>