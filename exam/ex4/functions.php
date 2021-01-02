<?php

require_once 'Person.php';

function statementToPersonList($stmt) {

    $dictionary = [];
    $personList = [];

    foreach ($stmt as $row) {
        $id = $row['id'];
        $name = $row['name'];
        $number = $row['number'];

        if(!isset($personList[$id])) {
            $personList[$id] = new Person($id, $name);
            $personList[$id] -> addPhone($number);
        } else {
            $personList[$id] -> addPhone($number);
        }
    }

    return $personList;
}
