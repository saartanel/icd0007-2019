<?php

require_once 'functions.php';
require_once 'Person.php';

$conn = getConnectionWithData('data.sql');

$stmt = $conn->prepare(
    'SELECT id, name, number 
       FROM person 
       LEFT JOIN phone ON id = person_id ORDER BY name');

$stmt->execute();

$personList = statementToPersonList($stmt);

foreach ($personList as $person) {
    printf("%s: %s \n", $person->name,
        implode(', ', $person->phones));
}











function getConnectionWithData($dataFile) {
    $conn = new PDO('sqlite::memory:');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $statements = explode(';', join('', file($dataFile)));

    foreach ($statements as $statement) {
        $conn->prepare($statement)->execute();
    }

    return $conn;
}

