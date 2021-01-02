<?php

session_start();

require_once 'vendor/tpl.php';
require_once 'Request.php';
require_once 'config.php';
require_once 'contact.php';
require_once 'ContactDAO.php';
require_once 'LoginHandler.php';
require_once 'MessageStore.php';

$login = new LoginHandler();

$request = new Request($_REQUEST);

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $errors['db'] = 'Andmebaasiviga: ' . $e->getMessage();
}

$translate = 'et';

if (!empty($request->param('translate'))) {
    setcookie('language', $request->param('translate'), 60 * 60 * 24 * 60 + time(), "/");
    $translate = $request->param('translate');
} elseif (isset($_COOKIE['language'])) {
    $translate = $_COOKIE['language'];
}

$store = new MessageStore('lang', $translate);

$contacts = new ContactDAO($conn, $request);

$data = [];

$command = $request->param('command')
    ? $request->param('command')
    : 'show_list_page';

if ($login->isLoggedIn()) {
    if ($command === 'show_list_page') {
        $data = [
            'templates' => 'list.html',
            'contacts' => $contacts->requestContactsList(),
        ];
    } elseif ($command === 'show_add_page') {
        if ($contacts->insertContacts()) {
            header('Location: ?command=show_list_page');
            exit();
        } else {
            $data = [
                'templates' => 'form.html',
                'cmd' => 'insertContacts',
                'firstName' => $contacts->request->param('firstName'),
                'lastName' => $contacts->request->param('lastName'),
                'phone1' => $contacts->request->param('phone1'),
                'phone2' => $contacts->request->param('phone2'),
                'phone3' => $contacts->request->param('phone3'),
                'errorFName' => $store->getMessage($contacts->errorFName),
                'errorLName' => $store->getMessage($contacts->errorLName)
            ];
        }
    } elseif ($command === 'edit') {
        if ($contacts->editContacts()) {
            header('Location: ?command=show_list_page');
            exit();
        } else {
            $id = $_GET['id'];
            $data = [
                'templates' => 'form.html',
                'cmd' => 'editContacts',
                'errorFName' => $contacts->errorFName,
                'errorLName' => $contacts->errorLName
            ];
            if ($request->param('cmd') === 'editContacts') {
                $data['firstName'] = $contacts->request->param('firstName');
                $data['lastName'] = $contacts->request->param('lastName');
                $data['phone1'] = $contacts->request->param('phone1');
                $data['phone2'] = $contacts->request->param('phone2');
                $data['phone3'] = $contacts->request->param('phone3');
            } else {
                $contact = $contacts->requestContactsEdit($id);
                $data['firstName'] = $contact['firstname'];
                $data['lastName'] = $contact['lastname'];
                if ($contact['numbers']) {
                    $number = 1;
                    foreach ($contact['numbers'] as $n) {
                        $data['phone' . $number] = $n;
                        $number += 1;
                    }
                }
            }
        }
    } else {
        if ($command === 'logout') {
            $login->logout();
            header('Location: ?');
            exit();
        }
    }
} else {
    $data = [
        'templates' => 'login.html',
        'cmd' => 'login',
    ];
    if ($request->param('cmd') === 'login') {
        if ($login->login($contacts->request->param('username'), $contacts->request->param('password'))) {
            header('Location: ?command=show_list_page');
            exit();
        } else {
            $data['errorLogin'] = $store->getMessage('errorLoginMessage');
            $data['userName'] = $contacts->request->param('username');
            $data['formPassword'] = $contacts->request->param('password');
        }
    }
}

$store->addMessagesTo($data);
$data['command'] = $command;
print renderTemplate('templates/main.html', $data, []);
