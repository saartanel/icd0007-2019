<?php

class MessageStore {

    public $dict;

    public function __construct($dir, $lang) {
        $lines = file("$dir/messages-$lang.txt");

        foreach ($lines as $line) {
            list($key, $value) = explode('=', trim($line));

            $this->dict[$key] = $value;
        }
    }

    public function addMessagesTo(&$dict) {
        foreach ($this->dict as $key => $value) {
            $dict[$key] = $value;
        }
    }

    public function getMessage($key, $substitutions = []) {
        if (empty($key)) {
            return $key;
        }
        $message = $this->dict[$key];

        $message = preg_replace_callback(
            '/\{(\d+)\}/',
            function ($matches) use ($substitutions) {
                $index = $matches[1] - 1; // counting starts from {1}.

                return isset($substitutions[$index])
                    ? $substitutions[$index] : '';
            },
            $message
        );

        return $message;
    }
}
