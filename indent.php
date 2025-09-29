<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'db_connection.php';
// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['item'])) {
    $user_id = $_SESSION['user_id'];
    $items = $_POST['item'];
    $particulars = $_POST['particulars'];
    $quantities = $_POST['quantity_required'];
    // $s_nos = $_POST['s_no'];
    $success = false;
    for ($i = 0; $i < count($items); $i++) {
        $item = $items[$i];
        $particular = $particulars[$i];
        $quantity = $quantities[$i];
        // $s_no = $s_nos[$i];
        $stmt = $conn->prepare("INSERT INTO indent (user_id, item, particulars, quantity_required) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $user_id, $item, $particular, $quantity);
        $success = $stmt->execute();
        $stmt->close();
    }
    if ($success) {
        echo '<div style="color: green; font-weight: bold; text-align: center; margin-bottom: 15px;">Indent submitted successfully!</div>';
    } else {
        echo '<div style="color: red; font-weight: bold; text-align: center; margin-bottom: 15px;">Error submitting indent. Please try again.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Indent Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .header {
            display: flex;
            align-items: center; 
            justify-content: center; 
            margin-bottom: 20px;
        }
        .header img {
            max-width: 100px; 
            margin-right: 20px; 
        }
        .header h1 {
            font-size: 20px; 
            color: #000; 
            margin: 0;
        }
        .header h2 {
            font-size: 20px; 
            color: #000; 
            margin: 5px 0;
            font-family: Georgia, serif;
            font-weight: bold;
        }
        .header h2:nth-of-type(2) {
            font-size: 18px; 
            color: #333; 
        }
        .form-container {
    background: #fff;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    max-width: 1500px; /* Increased width */
    margin: 0 auto;
}
        .form-container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        .form-container label {
            font-weight: bold;
            color: #555;
        }
        .form-container .top-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .form-container .top-row .left {
            display: flex;
            align-items: center;
            flex: 1;
        }
        .form-container .top-row .right {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            flex: 1;
        }
        .form-container .top-row label {
            margin-right: 10px; /* Add spacing between label and input */
        }
        .form-container .top-row input[type="text"],
        .form-container .top-row input[type="date"] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            width: 200px; /* Set fixed width for inputs */
        }
        #department {
            width: 400px; 
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table td{
            height: 2px;
        }
        table th, table td {
            border: 1px solid #ccc;
            padding: 5px 8px;
            text-align: left;
        }
        table th {
            background-color: #f4f4f4;
            font-weight: bold;
        }
        table td input[type="text"] {
            padding: 4px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }
        table td input[name="s_no[]"] {
            width: 15px; 
            text-align: center; 
            background-color: #f4f4f4; 
            border: none;
        }
        
        table td input[name="quantity_required[]"] {
            width: 92%; 
            text-align: center; 
        }
        table td input[name="item[]"],
        table td input[name="particulars[]"] {
            width: 92%; 
        }
        th:nth-child(4), td:nth-child(4) {
    width: 200px;      /* or any small value you want */
    min-width: 1px;
    max-width: 400px;
    text-align: center;
    padding-left: 2px;
    padding-right: 2px;
}
        .form-container button {
            width: auto; /* Adjust button size to fit its content */
            padding: 10px 20px; 
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            display: block;
            margin: 20px auto; /* Center the button */
        }
        .form-container button:hover {
            background-color: #0056b3;
        }
      
     .add-row-btn {
    background-color: #28a745;
    color: #fff;
    border: none;
    border-radius: 50%;
    width: 28px;         /* Bigger button */
    height: 38px;
    font-size: 20px;     /* Smaller plus sign, fits inside */
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0;
    cursor: pointer;
    vertical-align: middle;
    padding: 0;
    line-height: 1;
}
.remove-row-btn {
    background-color: #dc3545;
    color: #fff;
    border: none;
    border-radius: 4px;
    padding: 0px 6px;
    font-size: 14px;
    margin: 0;
    cursor: pointer;
    vertical-align: middle;
}
.action-buttons {
    display: inline-flex;
    flex-direction: row;
    align-items: center;
    justify-content: center;
    gap: 4px; /* No space between buttons */
}
.add-row-btn, .remove-row-btn {
    margin-right: 0;
    margin-left: 0;
}

    </style>
</head>
<body>
<?php include 'header.php'; ?>
    <div class="header">
        <img src="logo.png" alt="Logo"> <!-- Ensure logo.png is in the same directory -->
        <div>
            <h1>Department of Computer Sciences</h1>
            <h2>BGSB University, Rajouri (J&K)</h2>
            <h2>COMPUTER MAINTENANCE WING</h2>
        </div>
    </div>

    <div class="form-container">
        <h2>Indent</h2>

        <form method="POST" action="" autocomplete="off">
        <table id="indentTable">
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Item</th>
                    <th>Particulars</th>
                    <th>Quantity Required</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
    <tr>
    <td><input type="text" name="s_no[]" value="1" readonly></td>
    <td>
        <select name="item[]" required>
            <option value="">Select Item</option>
            <?php
            $item_query = $conn->query("SELECT item_name FROM indent_items ORDER BY item_name ASC");
            if ($item_query && $item_query->num_rows > 0) {
                while ($item_row = $item_query->fetch_assoc()) {
                    echo '<option value="' . htmlspecialchars($item_row['item_name']) . '">' . htmlspecialchars($item_row['item_name']) . '</option>';
                }
            }
            ?>
        </select>
    </td>
    <td><input type="text" name="particulars[]" placeholder="Enter Particulars" required></td>
    <td>
        <select name="quantity_required[]" required>
            <option value="">Select</option>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
            <option value="6">6</option>
            <option value="7">7</option>
            <option value="8">8</option>
            <option value="9">9</option>
            <option value="10">10</option>
        </select>
    </td>
    <td class="action-cell">
        <div class="action-buttons">
        <button type="button" class="add-row-btn" onclick="addRow(this)">+</button>
        
</div>
    </td>
</tr>
</tbody>
        </table>

        <div style="display: flex; justify-content: center; gap: 2px; margin-top: 20px;">
            <button type="submit" style="border-radius: 4px 0 0 4px; margin:0;">Submit</button>
            <button type="button" onclick="window.location.href='Index.php'" style="background-color: #dc3545; color: #fff; border: none; border-radius: 0 4px 4px 0; margin:0; padding: 10px 20px; font-size: 16px; cursor: pointer;">Cancel</button>
        </div>
        </form>
    </div>

    <script>
// Validation for particulars field: min 50, max 200 words, only allowed characters
document.querySelector('form').addEventListener('submit', function(e) {
    const particulars = document.getElementsByName('particulars[]');
    const particularsRegex = /^[A-Za-z0-9 ,.\/"']+$/;
    const hasLetter = /[A-Za-z]/;
    let valid = true;
    for (let i = 0; i < particulars.length; i++) {
        let val = particulars[i].value.trim();
        // Collapse multiple spaces
        val = val.replace(/\s+/g, ' ');
        // Remove empty strings from word count
        const wordCount = val.split(' ').filter(Boolean).length;
        if (!particularsRegex.test(val) || !hasLetter.test(val)) {
            particulars[i].setCustomValidity('Particulars must contain letters. Only letters, spaces, comma, dot, /, single quote, double quote are allowed.');
            valid = false;
        } else if (wordCount < 10) {
            particulars[i].setCustomValidity('Particulars must be at least 10 words.');
            valid = false;
        } else if (wordCount > 100) {
            particulars[i].setCustomValidity('Particulars must be no more than 100 words.');
            valid = false;
        } else {
            particulars[i].setCustomValidity('');
        }
    }
    if (!valid) {
        e.preventDefault();
        // Show error for first invalid particulars field
        for (let i = 0; i < particulars.length; i++) {
            if (!particulars[i].checkValidity()) {
                particulars[i].reportValidity();
                break;
            }
        }
    }
});

function addRow(button) {
    const table = document.getElementById('indentTable').getElementsByTagName('tbody')[0];
    const rowCount = table.rows.length;
    const newRow = table.insertRow(button ? button.parentElement.parentElement.rowIndex : rowCount);

    // Fetch item options from PHP and inject into JS
    let itemOptions = `<option value=\"\">Select Item</option>`;
    <?php
    $item_query_js = $conn->query("SELECT item_name FROM indent_items ORDER BY item_name ASC");
    if ($item_query_js && $item_query_js->num_rows > 0) {
        while ($item_row_js = $item_query_js->fetch_assoc()) {
            echo 'itemOptions += `<option value=\\"' . addslashes(htmlspecialchars($item_row_js['item_name'])) . '\\">' . addslashes(htmlspecialchars($item_row_js['item_name'])) . '</option>`;';
        }
    }
    ?>

    newRow.innerHTML = `
        <td><input type="text" name="s_no[]" value="${rowCount + 1}" readonly></td>
        <td>
            <select name="item[]" required>${itemOptions}</select>
        </td>
        <td><input type="text" name="particulars[]" placeholder="Enter Particulars" required></td>
        <td>
            <select name="quantity_required[]" required>
                <option value="">Select</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
                <option value="8">8</option>
                <option value="9">9</option>
                <option value="10">10</option>
            </select>
        </td>
        <td class="action-cell">
            <div class="action-buttons">
                <button type="button" class="add-row-btn" onclick="addRow(this)">+</button>
                <button type="button" class="remove-row-btn" onclick="removeRow(this)">Remove</button>
            </div>
        </td>
    `;

    // Update serial numbers
    const rows = document.querySelectorAll('#indentTable tbody tr');
    rows.forEach((row, index) => {
        row.querySelector('input[name="s_no[]"]').value = index + 1;
    });
}

function removeRow(button) {
    // Find the closest parent <tr> and remove it
    const row = button.closest('tr');
    row.remove();

    // Update serial numbers after a row is removed
    const rows = document.querySelectorAll('#indentTable tbody tr');
    rows.forEach((row, index) => {
        row.querySelector('input[name="s_no[]"]').value = index + 1;
    });
}
</script>
    <?php include 'footer.php'; ?>
</body>
</html>