<?php
include 'config.php';

$conn = pg_connect("host=$host dbname=$dbname user=$user password=$password");

if (!$conn) {
    die("Error de conexión");
}

$sql = "SELECT name, visited FROM places ORDER BY visited, name";
$result = pg_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>TravelRoad</title>
</head>
<body>
    <h1>My Travel Bucket</h1>
    
    <h2>Places I'd Like to Visit</h2>
    <ul>
    <?php while ($row = pg_fetch_assoc($result)): ?>
        <?php if (!$row['visited']): ?>
            <li><?= htmlspecialchars($row['name']) ?></li>
        <?php endif; ?>
    <?php endwhile; ?>
    </ul>
    
    <h2>Places I've Already Been To</h2>
    <ul>
    <?php pg_result_seek($result, 0); ?>
    <?php while ($row = pg_fetch_assoc($result)): ?>
        <?php if ($row['visited']): ?>
            <li><?= htmlspecialchars($row['name']) ?></li>
        <?php endif; ?>
    <?php endwhile; ?>
    
    <?php pg_close($conn); ?>
    </ul>
</body>
</html>
