<?php
$mysqli = new \PDO('mysql:host=172.24.0.3;port=3306;dbname=mysql', 'root', 'root');
if ( isset($_GET['group']) )
{
    $groupId = $_GET['group'];
    $groups = getGroupList($groupId, $groupId);
} else {
    $groups = getGroupList();
}
echo '<a href="/apps/4">Все товары</a>';
echo generateHtmlList($groups);
function getGroupList(int $parentId = 0, int $currentGroupId = 0, array $children = []) : array
{
    global $mysqli;
    $query = "SELECT * FROM `groups` WHERE id_parent = " . $parentId;
    $result = $mysqli->query($query);
    $groups = [];
    while ($row = $result->fetch()) {
        $groupId = $row['id'];
        $groupName = $row['name'];

        $totalProducts = getTotalProducts($groupId, (isset($_REQUEST['group']) && $groupId == $_REQUEST['group']) || !isset($_REQUEST['group']));
        $groups[$groupId] = [
            'name' => $groupName,
            'children' => $groupId == $currentGroupId ? $children : [],
            'total_products' => $totalProducts,
        ];
    }
    if ($parentId == 0)
    {
        return  $groups;
    }
    $stmt = $mysqli->query("SELECT * FROM `groups` WHERE id = " . $parentId);
    $parentGroup = $stmt->fetch();
    return getGroupList($parentGroup['id_parent'], $parentGroup['id'], $groups);
}

function getTotalProducts( int $groupId, bool $showProducts = false) : int
{
    global $mysqli;

    $query = "SELECT name FROM `products` WHERE id_group = " . $groupId;
    $result = $mysqli->query($query);
    $productCount = 0;
    while ($row = $result->fetch()) {
        if ($showProducts)
        {
            echo "<li>".$row['name']."</li>";
        }
        $productCount++;
    }

    $query = "SELECT id FROM `groups` WHERE id_parent = " . $groupId;
    $result = $mysqli->query($query);
    while ($row = $result->fetch()) {
        $subGroupId = $row['id'];
        $productCount += getTotalProducts($subGroupId, $showProducts);
    }

    return $productCount;
}
function generateHtmlList( array $array) : string
{
    $output = '';
    foreach ($array as $key=>$item) {
        $output .= '<li>';
        $output .= '<a href="?group='.$key.'">'.$item['name'].'('.$item['total_products'].')'.'</a>';

        if (!empty($item['children'])) {
            $output .= '<ul>';
            $output .= generateHtmlList($item['children']);
            $output .= '</ul>';
        }

        $output .= '</li>';
    }

    return $output;
}