<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start the session only if it hasn't been started
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Card</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .form-container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 700px;
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
        .form-container input,
        .form-container textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }
        .form-container textarea {
            resize: vertical;
        }
        .form-container button {
            width: auto;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin: 0;
            display: inline-block;
        }
        .form-container button:hover {
            background-color: #007bff;
        }
        .note {
            font-size: 14px;
            color: #555;
            margin-top: 2px;
            margin-bottom: 10px;
            background: #f9f9f9;
            padding: 10px;
            border-left: 4px solid #007bff;
        }
        .conditional-input {
    display: flex;
    align-items: center;
    margin-bottom: 10px; /* Reduce spacing between sections */
    gap: 10px; /* Add small spacing between label and buttons */
}
.conditional-input label {
    margin: 0; /* Remove extra margin around the label */
    font-weight: bold;
    color: #555;
}
.toggle-buttons {
    display: flex;
    gap: 5px; /* Reduce spacing between buttons */
}
.toggle-buttons button {
    padding: 5px 10px; /* Adjust padding for smaller buttons */
    border: 1px solid black;
    margin-right: 5px; /* Add small margin to the right of each button */
    border: 1px solid #ccc;
    border-radius: 4px;
    background-color: white;
    cursor: pointer;
    font-size: 14px;
    text-align: center;
    width: auto; 
    min-width: 50px; 
    color: black;
}
        .toggle-buttons button.active {
            background-color: #007bff;
            color: #fff;
            border-color: #007bff;
        }
        .toggle-buttons button:disabled {
            cursor: not-allowed;
            background-color: #e0e0e0;
            color: #aaa;
        }
        .details-input {
            margin-top: 10px;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Job Card for Requisition: Repair</h2>
        <p><strong>To be filled in by the requisitioner:</strong></p>
        <form action="/project_cmw/submit_jobcard.php" method="POST">


            <label for="equipment">1. Name of Equipment:</label>
            <select id="equipment" name="equipment" required style="width:100%;padding:10px;margin-bottom:15px;border:1px solid #ccc;border-radius:4px;font-size:14px;">
                <option value="">Select Equipment</option>
                <option value="Monitor">Monitor</option>
                <option value="Laptop">Laptop</option>
                <option value="Printer">Printer</option>
                <option value="Scanner">Scanner</option>
                <option value="Projector">Projector</option>
                <option value="Keyboard">Keyboard</option>
                <option value="Mouse">Mouse</option>
            </select>

           <label for="equipment_make">2. Make of Equipment:</label>
           <select id="equipment_make" name="equipment_make" required style="width:100%;padding:10px;margin-bottom:15px;border:1px solid #ccc;border-radius:4px;font-size:14px;">
                <option value="">Select Make</option>
                <option value="Hp">Hp</option>
                <option value="Wipro">Wipro</option>
                <option value="Dell">Dell</option>
                <option value="Logitech">Logitech</option>
                <option value="LG">LG</option>
                <option value="Canon">Canon</option>
                <option value="Fujitsu">Fujitsu</option>
            </select>

            <!-- Out of Warranty Section -->
<div class="conditional-input">
    <label>3. Out of Warranty:</label>
    <div class="toggle-buttons" id="outOfWarrantyToggle">
        <button type="button" data-value="yes">Yes</button>
        <button type="button" data-value="no">No</button>
    </div>
</div>
<input type="hidden" id="out_of_warranty" name="out_of_warranty" required>
<input type="text" id="out_of_warranty_details" name="out_of_warranty_details" placeholder="Enter details if out of warranty" class="details-input" disabled>

<!-- Under AMC Section -->
<div class="conditional-input">
    <label>4. Under AMC:</label>
    <div class="toggle-buttons" id="underAmcToggle">
        <button type="button" data-value="yes">Yes</button>
        <button type="button" data-value="no">No</button>
    </div>
</div>
<input type="hidden" id="under_amc" name="under_amc" required>
<input type="text" id="under_amc_details" name="under_amc_details" placeholder="Enter details if under AMC" class="details-input" disabled>

            <label for="description">5. Description of Job/Fault:</label>
            <textarea id="description" name="description" rows="4" required minlength="10" maxlength="200" title="Letters, spaces, comma, dot, /, ', &quot;, 50-200 words."></textarea>

            <div class="note">
                <strong>Note:</strong> The CMW is not responsible for any data loss. It is therefore requested to take the data backup of the systems before sending the same to the CMW.
            </div>

            <div style="display:flex;gap:2px;justify-content:center;align-items:center;margin-top:10px;">
                <button type="submit">Submit</button>
                <button type="button" onclick="window.location.href='Index.php'" style="background-color:#dc3545;">Cancel</button>
            </div>
        </form>
    </div>

    <script>
        // Extra validation for equipment and equipment_make fields
        document.querySelector('form').addEventListener('submit', function(e) {
            const equipment = document.getElementById('equipment');
            const equipmentMake = document.getElementById('equipment_make');
            const description = document.getElementById('description');
            const particulars = document.getElementsByName('particulars[]');
            const letterRegex = /^[A-Za-z ]{1,20}$/;
            // Require at least one letter, allow optional punctuation and newlines (\n, \r)
            const descRegex = /^[A-Za-z ,.\/"'\n\r]+$/;
            const hasLetter = /[A-Za-z]/;
            // For particulars validation (same as description, but no newlines)
            const particularsRegex = /^[A-Za-z ,.\/"']+$/;
            let valid = true;
            // Validate all particulars fields
            for (let i = 0; i < particulars.length; i++) {
                let val = particulars[i].value.trim();
                // Collapse multiple spaces
                val = val.replace(/\s+/g, ' ');
                const wordCount = val.length > 0 ? val.split(' ').length : 0;
                if (!particularsRegex.test(val) || !hasLetter.test(val)) {
                    particulars[i].setCustomValidity('Particulars must contain letters. Only letters, spaces, comma, dot, /, single quote, double quote are allowed.');
                    valid = false;
                } else if (wordCount < 50) {
                    particulars[i].setCustomValidity('Particulars must be at least 50 words.');
                    valid = false;
                } else if (wordCount > 200) {
                    particulars[i].setCustomValidity('Particulars must be no more than 200 words.');
                    valid = false;
                } else {
                    particulars[i].setCustomValidity('');
                }
            }
            // Equipment validation
            if (!letterRegex.test(equipment.value)) {
                equipment.setCustomValidity('Only letters and spaces allowed, max 20 characters.');
                valid = false;
            } else {
                equipment.setCustomValidity('');
            }
            if (!letterRegex.test(equipmentMake.value)) {
                equipmentMake.setCustomValidity('Only letters and spaces allowed, max 20 characters.');
                valid = false;
            } else {
                equipmentMake.setCustomValidity('');
            }
            // Description validation
            let descValue = description.value.trim();
            // Replace all newlines with spaces, then collapse multiple spaces
            descValue = descValue.replace(/\n+/g, ' ').replace(/\s+/g, ' ');
            const wordCount = descValue.length > 0 ? descValue.split(' ').length : 0;
            if (!descRegex.test(descValue) || !hasLetter.test(descValue)) {
                description.setCustomValidity('Description must contain letters. Only letters, spaces, comma, dot, /, single quote, double quote are allowed.');
                valid = false;
            } else {
                if (wordCount < 10) {
                    description.setCustomValidity('Description must be at least 20 words.');
                    valid = false;
                } else if (wordCount > 100) {
                    description.setCustomValidity('Description must be no more than 100 words.');
                    valid = false;
                } else {
                    description.setCustomValidity('');
                }
            }
            if (!valid) {
                e.preventDefault();
                equipment.reportValidity();
                equipmentMake.reportValidity();
                description.reportValidity();
                // Show error for first invalid particulars field
                for (let i = 0; i < particulars.length; i++) {
                    if (!particulars[i].checkValidity()) {
                        particulars[i].reportValidity();
                        break;
                    }
                }
            }
        });
     // Handle Out of Warranty Section (REVERSED LOGIC)
    const outOfWarrantyToggle = document.getElementById('outOfWarrantyToggle');
    const outOfWarrantyInput = document.getElementById('out_of_warranty');
    const outOfWarrantyDetails = document.getElementById('out_of_warranty_details');

    outOfWarrantyToggle.addEventListener('click', function (event) {
    if (event.target.tagName === 'BUTTON') {
        const value = event.target.getAttribute('data-value');
        outOfWarrantyInput.value = value;

        // Toggle active state
        Array.from(outOfWarrantyToggle.children).forEach(button => button.classList.remove('active'));
        event.target.classList.add('active');

        // Enable/disable details input (REVERSED)
        if (value === 'no') {
            outOfWarrantyDetails.disabled = false;
            outOfWarrantyDetails.required = true;
        } else {
            outOfWarrantyDetails.disabled = true;
            outOfWarrantyDetails.required = false;
            outOfWarrantyDetails.value = ''; // Clear the input
        }
    }
});

        // Handle Under AMC Section
        const underAmcToggle = document.getElementById('underAmcToggle');
        const underAmcInput = document.getElementById('under_amc');
        const underAmcDetails = document.getElementById('under_amc_details');

        underAmcToggle.addEventListener('click', function (event) {
            if (event.target.tagName === 'BUTTON') {
                const value = event.target.getAttribute('data-value');
                underAmcInput.value = value;

                // Toggle active state
                Array.from(underAmcToggle.children).forEach(button => button.classList.remove('active'));
                event.target.classList.add('active');

                // Enable/disable details input
                if (value === 'yes') {
                    underAmcDetails.disabled = false;
                    underAmcDetails.required = true;
                } else {
                    underAmcDetails.disabled = true;
                    underAmcDetails.required = false;
                    underAmcDetails.value = ''; // Clear the input
                }
            }
        });
    </script>
</body>
</html>