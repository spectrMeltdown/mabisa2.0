<?php
include 'db.php';

$sql = "SELECT * FROM maintenance_area";
$stmt = $pdo->prepare($sql);
$stmt->execute();

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<table border="1">
    <tr>
        <th>Keyctr</th>
        <th>Description</th>
        <!-- <th>Trail</th> -->
        <th>Actions</th>
    </tr>
    <?php foreach ($results as $row): ?>
    <tr>
        <td><?php echo $row['keyctr']; ?></td>
        <td><?php echo $row['description']; ?></td>
        <td>
            <a href="update_maintenance_area.php?keyctr=<?php echo $row['keyctr']; ?>">Edit</a>
            <a href="delete_maintenance_area.php?keyctr=<?php echo $row['keyctr']; ?>">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
    <a href="index.php">Home Page</a>
