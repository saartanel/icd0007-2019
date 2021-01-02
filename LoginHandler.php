<?php

class LoginHandler {

    private $username = "user";
    private $password = "secret";

    public function login(string $username, string $password): bool {
        if ($username === $this->username && $password === $this->password) {
            $_SESSION['loggedin'] = true;
            return true;
        }
        return false;
    }

    public function logout(): void {
        $_SESSION['loggedin'] = false;
    }

    public function isLoggedIn(): bool {
        return $_SESSION['loggedin'] ?? false;
    }
}
