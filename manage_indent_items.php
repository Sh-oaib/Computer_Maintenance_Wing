<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
require_once 'db_connection.php';
// Handle add/remove item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['indent_action'])) {
    $indent_action = $_POST['indent_action'];
    $item_name = trim($_POST['item_name'] ?? '');
    if ($indent_action === 'add' && $item_name !== '') {
        $stmt = $conn->prepare("INSERT INTO indent_items (item_name) VALUES (?)");
        $stmt->bind_param("s", $item_name);
        $stmt->execute();
        $stmt->close();
        echo '<div style="color:green;font-weight:bold;">Item added successfully.</div>';
    }
    if ($indent_action === 'remove' && isset($_POST['item_id'])) {
        $item_id = intval($_POST['item_id']);
        $stmt = $conn->prepare("DELETE FROM indent_items WHERE id = ?");
        $stmt->bind_param("i", $item_id);
        $stmt->execute();
        $stmt->close();
        echo '<div style="color:red;font-weight:bold;">Item removed.</div>';
    }
}
// Fetch all indent items
$items_result = $conn->query("SELECT id, item_name FROM indent_items ORDER BY item_name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manage Indent Items</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; }
        .container { max-width: 700px; margin: 40px auto; background: #fff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); padding: 30px; }
        h1 { color: #007bff; text-align: center; margin-bottom: 30px; }
        form { display: flex; gap: 10px; align-items: center; margin-bottom: 20px; }
        input[type="text"] { padding: 8px; font-size: 15px; border-radius: 4px; border: 1px solid #ccc; width: 220px; }
        button { padding: 8px 16px; font-size: 15px; border: none; border-radius: 4px; color: #fff; cursor: pointer; }
        .add-btn { background-color: #28a745; }
        .remove-btn { background-color: #dc3545; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { padding: 12px 15px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background-color: #007bff; color: #fff; }
        tr:hover { background-color: #f1f1f1; }
        .back-btn { display: inline-block; margin-bottom: 20px; background: #007bff; color: #fff; padding: 8px 18px; border-radius: 4px; text-decoration: none; font-size: 16px; }
    </style>
</head>
<body>
    <div class="container">
        <a href="admin_panel.php" class="back-btn">&larr; Back to Admin Panel</a>
        <h1>Manage Indent Items</h1>
        <form method="POST">
            <input type="text" name="item_name" placeholder="New Item Name" required />
            <button type="submit" name="indent_action" value="add" class="add-btn">Add Item</button>
        </form>
        <table>
            <thead>
                <tr><th style="width:70%;">Item Name</th><th>Action</th></tr>
            </thead>
            <tbody>
                <?php if ($items_result && $items_result->num_rows > 0): ?>
                    <?php while ($item = $items_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                    <button type="submit" name="indent_action" value="remove" class="remove-btn">Remove</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="2" style="color:#888;">No items found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
