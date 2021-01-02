<?php

class ContactDAO
{

    private $conn;

    public function __construct($conn, $request)
    {
        $this->conn = $conn;
        $this->request = $request;
        $this->errorFName = "";
        $this->errorLName = "";
    }

    public function requestContactsList()
    {
        $stmt = $this->conn->prepare('SELECT c.id, c.firstname, c.lastname, GROUP_CONCAT(n.phone) as numbers FROM contacts c LEFT JOIN numbers n ON c.id = n.contact_id GROUP BY c.id');
        $stmt->execute();
        $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $contacts = [];
        foreach ($stmt->fetchAll() as $v) {
            $numbers = "";
            if ($v['numbers']) {
                $numbers = explode(",", $v['numbers']);
                $numbers = implode(", ", $numbers);
            }
            $contacts[] = new Contact($v['id'], $v['firstname'], $v['lastname'], $numbers);
        }
        return $contacts;
    }


    public function insertContacts(): bool
    {
        if ($this->request->param('cmd') === 'insertContacts') {
            if ($this->checkError()) {
                $q = 'INSERT INTO contacts (firstname, lastname) VALUES (:firstname, :lastname)';
                $stmt = $this->conn->prepare($q);
                $stmt->bindParam(':firstname', $this->request->param('firstName'));
                $stmt->bindParam(':lastname', $this->request->param('lastName'));
                $result = $stmt->execute();
                $last_id = $this->conn->lastInsertId();

                $q = 'INSERT INTO numbers (contact_id, phone) VALUES (:id, :number)';
                $stmt = $this->conn->prepare($q);
                foreach ([
                             $this->request->param('phone1'),
                             $this->request->param('phone2'),
                             $this->request->param('phone3')
                         ] as $p) {
                    if ($p) {
                        $stmt->bindParam(':id', $last_id);
                        $stmt->bindParam(':number', $p);
                        $result = $stmt->execute();
                    }
                }
                return true;
            }
        }
        return false;
    }


    public function checkError()
    {
        if (strlen($this->request->param('firstName')) < 2) {
            $this->errorFName = 'errorFirstName';
        }
        if (strlen($this->request->param('lastName')) < 2) {
            $this->errorLName = 'errorLastName';
        }
        if (empty($this->errorFName) && empty($this->errorLName)) {
            return true;
        } else {
            return false;
        }
    }


    public function editContacts()
    {
        $this->currentContact = $this->requestContactsEdit($this->request->param('id'));
        if ($this->request->param('cmd') === 'editContacts') {
            $this->currentContact = [
                'firstName' => $this->request->param('firstName'),
                'lastName' => $this->request->param('lastName'),
                'phone1' => $this->request->param('phone1'),
                'phone2' => $this->request->param('phone2'),
                'phone3' => $this->request->param('phone3')
            ];
            if ($this->checkError()) {
                $q = 'UPDATE contacts SET firstname=:firstname, lastname=:lastname WHERE id=:id';
                $stmt = $this->conn->prepare($q);
                $stmt->bindParam(':firstname', $this->request->param('firstName'));
                $stmt->bindParam(':lastname', $this->request->param('lastName'));
                $stmt->bindParam(':id', $this->request->param('id'));
                $result = $stmt->execute();

                $q = 'DELETE FROM numbers where contact_id=:id';
                $stmt = $this->conn->prepare($q);
                $stmt->bindParam(':id', $this->request->param('id'));
                $result = $stmt->execute();

                $q = 'INSERT INTO numbers (contact_id, phone) VALUES (:id, :number)';
                $stmt = $this->conn->prepare($q);
                foreach ([
                             $this->request->param('phone1'),
                             $this->request->param('phone2'),
                             $this->request->param('phone3')
                         ] as $p) {
                    if ($p) {
                        $stmt->bindParam(':id', $this->request->param('id'));
                        $stmt->bindParam(':number', $p);
                        $result = $stmt->execute();
                    }
                }
                return true;
            }
        }
        return false;
    }


    public function requestContactsEdit($id)
    {
        $stmt = $this->conn->prepare('SELECT c.id, c.firstname, c.lastname, GROUP_CONCAT(n.phone) as numbers FROM contacts c LEFT JOIN numbers n ON c.id = n.contact_id WHERE c.id=:id GROUP BY n.contact_id');
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $result = $stmt->fetchAll();
        $numbers = explode(",", $result[0]['numbers']);
        $contact = [];
        $contact['firstname'] = $result[0]['firstname'];
        $contact['lastname'] = $result[0]['lastname'];
        if ($numbers) {
            $contact['numbers'] = [];
            foreach ($numbers as $n) {
                $contact['numbers'][] = $n;
            }
        }
        return $contact;
    }


}
