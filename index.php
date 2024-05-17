<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "barbershop_booking";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle booking request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $chair_id = $_POST['chair_id'];
    $user_id = $_POST['user_id'];
    $booking_date = $_POST['booking_date'];
    $booking_time = $_POST['booking_time'];

    // Check if the time slot is already booked
    $sql = "SELECT * FROM bookings WHERE chair_id = ? AND booking_date = ? AND booking_time = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $chair_id, $booking_date, $booking_time);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Time slot already booked.');</script>";
    } else {
        // Insert booking
        $sql = "INSERT INTO bookings (chair_id, user_id, booking_date, booking_time) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiss", $chair_id, $user_id, $booking_date, $booking_time);
        if ($stmt->execute()) {
            echo "<script>alert('Booking successful!');</script>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// Fetch chairs
$sql = "SELECT * FROM chairs";
$chairs = $conn->query($sql);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Barbershop Booking System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: auto;
            overflow: hidden;
        }
        header {
            background: #333;
            color: #fff;
            padding-top: 30px;
            min-height: 70px;
            border-bottom: #77a9d1 3px solid;
        }
        header a {
            color: #fff;
            text-decoration: none;
            text-transform: uppercase;
            font-size: 16px;
        }
        header ul {
            padding: 0;
            list-style: none;
        }
        header li {
            display: inline;
            padding: 0 20px 0 20px;
        }
        .main {
            padding: 15px;
            background: #fff;
            margin-top: 20px;
            border-radius: 5px;
        }
        .chairs {
            display: flex;
            justify-content: space-around;
            margin-top: 20px;
        }
        .chair {
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background: #f9f9f9;
            text-align: center;
            transition: background 0.3s;
        }
        .chair:hover {
            background: #e9e9e9;
        }
        form {
            margin-top: 20px;
        }
        label {
            display: block;
            margin-top: 10px;
        }
        input[type="text"],
        input[type="number"],
        input[type="date"],
        input[type="time"],
        select {
            width: 100%;
            padding: 10px;
            margin: 5px 0 20px 0;
            display: inline-block;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            width: 100%;
            background-color: #4CAF50;
            color: white;
            padding: 14px 20px;
            margin: 8px 0;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 15px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>Barbershop Booking System</h1>
        </div>
    </header>
    <div class="container">
        <div class="main">
            <h2>Book a Chair</h2>
            <div class="chairs">
                <?php while($row = $chairs->fetch_assoc()) { ?>
                    <div class="chair">
                        <h3><?= $row['name'] ?></h3>
                    </div>
                <?php } ?>
            </div>
            <form method="post" action="">
                <label for="chair_id">Choose a chair:</label>
                <select name="chair_id" id="chair_id" required>
                    <?php while($row = $chairs->fetch_assoc()) { ?>
                        <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
                    <?php } ?>
                </select>
                <label for="user_id">User ID:</label>
                <input type="number" name="user_id" id="user_id" required>
                <label for="booking_date">Date:</label>
                <input type="date" name="booking_date" id="booking_date" required>
                <label for="booking_time">Time:</label>
                <input type="time" name="booking_time" id="booking_time" required>
                <input type="submit" value="Book">
            </form>
            <h2>Booked Times</h2>
            <table>
                <tr>
                    <th>Chair</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>User ID</th>
                </tr>
                <?php
                $sql = "SELECT chairs.name AS chair_name, bookings.booking_date, bookings.booking_time, bookings.user_id 
                        FROM bookings 
                        JOIN chairs ON bookings.chair_id = chairs.id 
                        ORDER BY bookings.booking_date, bookings.booking_time";
                $result = $conn->query($sql);
                while($row = $result->fetch_assoc()) {
                    echo "<tr><td>{$row['chair_name']}</td><td>{$row['booking_date']}</td><td>{$row['booking_time']}</td><td>{$row['user_id']}</td></tr>";
                }
                ?>
            </table>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>
