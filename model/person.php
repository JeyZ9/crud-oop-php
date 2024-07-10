<?php
include_once 'address.php';

class Person {
    private $id;
    private $name;
    private $age;
    private $address;

    public function __construct($name, $age, $address) {
        $this->name = $name;
        $this->age = $age;
        $this->address = $address;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getAge() {
        return $this->age;
    }

    public function getAddress() {
        return $this->address;
    }

    public function saveToDatabase($conn) {
        $p1 = $this->getName(); 
        $p2 = $this->getAge(); 
        $p3 = $this->address->getStreet(); 
        $p4 = $this->address->getCity(); 
        $p5 = $this->address->getState(); 
        $p6 = $this->address->getPostalCode();
        $stmt = $conn->prepare("INSERT INTO persons (name, age, street, city, state, postal_code) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sissss", 
            $p1, 
            $p2, 
            $p3, 
            $p4, 
            $p5, 
            $p6
        );
        $stmt->execute();
        $stmt->close();
    }

    public static function getAll($conn, $offset, $limit) {
        $stmt = $conn->prepare("SELECT * FROM persons LIMIT ?, ?");
        $stmt->bind_param("ii", $offset, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        $persons = [];
        while ($row = $result->fetch_assoc()) {
            $address = new Address($row['street'], $row['city'], $row['state'], $row['postal_code']);
            $person = new Person($row['name'], $row['age'], $address);
            $person->setId($row['id']);
            $persons[] = $person;
        }
        $stmt->close();
        return $persons;
    }

    public function updateInDatabase($conn) {
        $stmt = $conn->prepare("UPDATE persons SET name=?, age=?, street=?, city=?, state=?, postal_code=? WHERE id=?");
        $stmt->bind_param("sissssi", 
            $this->getName(), 
            $this->getAge(), 
            $this->address->getStreet(), 
            $this->address->getCity(), 
            $this->address->getState(), 
            $this->address->getPostalCode(),
            $this->getId()
        );
        $stmt->execute();
        $stmt->close();
    }

    public static function deleteFromDatabase($conn, $id) {
        $stmt = $conn->prepare("DELETE FROM persons WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
}
?>