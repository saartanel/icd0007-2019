<?php

class Person {
    public $id;
    public $name;
    public $phones = [];

    public function __construct($id, $name) {
        $this->id = $id;
        $this->name = $name;
    }

    public function addPhone($phone) {
        $this->phones[] = $phone;
    }
}

