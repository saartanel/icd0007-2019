<?php

require_once '../vendor/tpl.php';

$translations = ['red' => 'Punane', 'blue' => 'Sinine'];

if (isset($_POST['color'])) {
    $color = $_POST['color'];
    header('Location: ?color=' . $color);
    exit;
} else if (isset($_GET['color'])) {
    $color = $translations[$_GET['color']];
    $data['color'] = $color;
    $data['fileName'] = 'content.html';
} else {
    $data['fileName'] = 'form.html';
}

print renderTemplate('main.html', $data);
