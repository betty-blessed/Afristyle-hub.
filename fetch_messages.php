<?php
include 'includes/connection.php';

if (!isset($_GET['user_id']) || !isset($_GET['admin_id'])) {
    die("Invalid request.");
}

$user_id = intval($_GET['user_id']);
$admin_id = intval($_GET['admin_id']);

$query = "SELECT * FROM conversations 
          WHERE (user_id = ? AND admin_id = ?)
          ORDER BY created_at ASC";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $admin_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()): ?>
    <div class="message <?= $row['sender'] == 'customer' ? 'customer' : 'admin' ?>">
        <p><?= htmlspecialchars($row['message']); ?></p>
    </div>
<?php endwhile; ?>
