<?php
// Function to get input values either from POST or GET
function getInputValues() {
    $values = [];
    
    // Check if form was submitted via POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $values = [
            'a' => isset($_POST['a']) ? $_POST['a'] : null,
            'b' => isset($_POST['b']) ? $_POST['b'] : null,
            'c' => isset($_POST['c']) ? $_POST['c'] : null,
            'd' => isset($_POST['d']) ? $_POST['d'] : null,
            'e' => isset($_POST['e']) ? $_POST['e'] : null
        ];
    } 
    // Check if values were provided via URL parameters
    else if (!empty($_GET)) {
        $values = [
            'a' => isset($_GET['a']) ? $_GET['a'] : null,
            'b' => isset($_GET['b']) ? $_GET['b'] : null,
            'c' => isset($_GET['c']) ? $_GET['c'] : null,
            'd' => isset($_GET['d']) ? $_GET['d'] : null,
            'e' => isset($_GET['e']) ? $_GET['e'] : null
        ];
    }
    
    return $values;
}

// Get user input
$input_values = getInputValues();

// Check if we have all required values
if (count(array_filter($input_values, function($value) { return $value !== null; })) != 5) {
    echo "<h2>Error</h2>";
    echo "<p>All five numerical values are required.</p>";
    echo "<p><a href='form.php'>Go back to the form</a></p>";
    exit;
}

// Get and display the public IP address (always displayed)
$publicIP = trim(shell_exec('curl -4 ifconfig.io'));
if (!empty($publicIP)) {
    echo "<p><strong>Public IP Address:</strong> " . htmlspecialchars($publicIP) . "</p>";
} else {
    echo "<p><strong>Could not retrieve public IP address.</strong></p>";
}

// Escape values for shell command
$escaped_values = array_map('escapeshellarg', $input_values);

// Build the command to call the Python script
$command = "python3 data_management.py {$escaped_values['a']} {$escaped_values['b']} {$escaped_values['c']} {$escaped_values['d']} {$escaped_values['e']}";

// Execute the Python script and capture the output
$output = shell_exec($command);

// Decode the JSON response
$result = json_decode($output, true);

// Check if there was an error
if (isset($result['error'])) {
    echo "<h2>Error</h2>";
    echo "<p>{$result['error']}</p>";
    echo "<p><a href='form.php'>Go back to the form</a></p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Processing Results</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            max-width: 800px;
            margin: 0 auto;
        }
        h1, h2 {
            color: #333;
        }
        .result-section {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .positive {
            color: green;
        }
        .negative {
            color: red;
        }
        .highlight {
            background-color: #f9f9f9;
            padding: 10px;
            border-left: 3px solid #4CAF50;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Data Processing Results</h1>
    
    <div class="result-section">
        <h2>Input Values</h2>
        <p>Original values: <?php echo implode(", ", $result['original_values']); ?></p>
    </div>
    
    <div class="result-section">
        <h2>Basic Analysis</h2>
        
        <?php if ($result['has_negative']): ?>
            <p class="negative">Found negative values: <?php echo implode(", ", $result['negative_values']); ?></p>
        <?php else: ?>
            <p class="positive">All values are non-negative.</p>
        <?php endif; ?>
        
        <p>Average of all values: <strong><?php echo number_format($result['average'], 2); ?></strong>
            <?php if ($result['is_avg_greater_than_50']): ?>
                <span class="positive"> (Greater than 50)</span>
            <?php else: ?>
                <span> (Not greater than 50)</span>
            <?php endif; ?>
        </p>
    </div>
    
    <div class="result-section">
        <h2>Bit Operations and Counting</h2>
        <p>Number of positive values: <?php echo $result['positive_count']; ?></p>
        <p>
            Positive count is 
            <?php if ($result['is_positive_count_even']): ?>
                <strong>even</strong> (bitwise AND with 1 equals 0)
            <?php else: ?>
                <strong>odd</strong> (bitwise AND with 1 equals 1)
            <?php endif; ?>
        </p>
    </div>
    
    <div class="result-section">
        <h2>Filtered and Sorted Values</h2>
        <?php if (count($result['values_greater_than_10']) > 0): ?>
            <p>Values greater than 10:</p>
            <div class="highlight">
                <p>Original order: <?php echo implode(", ", $result['values_greater_than_10']); ?></p>
                <p>Sorted order: <?php echo implode(", ", $result['sorted_values']); ?></p>
            </div>
        <?php else: ?>
            <p>No values greater than 10 found.</p>
        <?php endif; ?>
    </div>
    
    <p><a href="form.php">Process more numbers</a></p>
</body>
</html>