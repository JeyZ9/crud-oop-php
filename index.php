<?php
include_once './model/database.php';
include_once './model/person.php';

$database = new Database();
$conn = $database->getConnection();

$limit = 5;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$persons = Person::getAll($conn, $offset, $limit);

$total_persons = $conn->query("SELECT COUNT(*) AS count FROM persons")->fetch_assoc()['count'];
$total_pages = ceil($total_persons / $limit);

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Person List</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
    </style>
</head>
<body>
    <h2>Person List</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Age</th>
            <th>Street</th>
            <th>City</th>
            <th>State</th>
            <th>Postal Code</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($persons as $person): ?>
        <tr>
            <td><?php echo $person->getId(); ?></td>
            <td><?php echo $person->getName(); ?></td>
            <td><?php echo $person->getAge(); ?></td>
            <td><?php echo $person->getAddress()->getStreet(); ?></td>
            <td><?php echo $person->getAddress()->getCity(); ?></td>
            <td><?php echo $person->getAddress()->getState(); ?></td>
            <td><?php echo $person->getAddress()->getPostalCode(); ?></td>
            <td>
                <a href="update.php?id=<?php echo $person->getId(); ?>">Update</a> | 
                <a href="delete.php?id=<?php echo $person->getId(); ?>" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <div>
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="index.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>
    </div>
</body>
</html>