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
    die("Anslutningsfel: " . $conn->connect_error);
}

// Handle booking request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $chair_id = $_POST['chair_id'];
    $name = $_POST['name'];
    $phone_number = $_POST['phone_number'];
    $booking_date = $_POST['booking_date'];
    $booking_time = $_POST['booking_time'];

    // Check if the time slot is already booked
    $sql = "SELECT * FROM bookings WHERE chair_id = ? AND booking_date = ? AND booking_time = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $chair_id, $booking_date, $booking_time);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Tid redan bokad.');</script>";
    } else {
        // Insert booking
        $sql = "INSERT INTO bookings (chair_id, name, phone_number, booking_date, booking_time) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issss", $chair_id, $name, $phone_number, $booking_date, $booking_time);
        if ($stmt->execute()) {
            echo "<script>alert('Bokning lyckades!');</script>";
        } else {
            echo "Fel: " . $sql . "<br>" . $conn->error;
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
    <title>Bokningssystem för Frisörsalong</title>
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
            text-align: center;
        }
        header h1 {
            margin: 0;
            padding-bottom: 10px;
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
            width: 120px;
            height: 150px;
            background: url('https://example.com/chair-icon.png') no-repeat center center;
            background-size: contain;
            border: 2px solid #ccc;
            border-radius: 5px;
            text-align: center;
            cursor: pointer;
            transition: background 0.3s;
            position: relative;
        }
        .chair:hover {
            background-color: #e9e9e9;
        }
        .chair.selected {
            border-color: #77a9d1;
        }
        .chair h3 {
            position: absolute;
            bottom: 10px;
            width: 100%;
            margin: 0;
            color: #333;
        }
        form {
            margin-top: 20px;
        }
        label {
            display: block;
            margin-top: 10px;
        }
        input[type="text"],
        input[type="tel"],
        input[type="date"],
        input[type="time"] {
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
            <h1>Bokningssystem för Frisörsalong</h1>
        </div>
    </header>
    <div class="container">
        <div class="main">
            <h2>Boka en Stol</h2>
            <div class="chairs">
                <?php while($row = $chairs->fetch_assoc()) { ?>
                    <div class="chair" data-chair-id="<?= $row['id'] ?>">
                        <h3><?= $row['name'] ?></h3>
                    </div>
                <?php } ?>
            </div>
            <form method="post" action="">
                <input type="hidden" name="chair_id" id="chair_id" required>
                <label for="name">Namn:</label>
                <input type="text" name="name" id="name" required>
                <label for="phone_number">Telefonnummer:</label>
                <input type="tel" name="phone_number" id="phone_number" required>
                <label for="booking_date">Datum:</label>
                <input type="date" name="booking_date" id="booking_date" required>
                <label for="booking_time">Tid:</label>
                <input type="time" name="booking_time" id="booking_time" required>
                <input type="submit" value="Boka">
            </form>
            <h2>Bokade Tider</h2>
            <table>
                <tr>
                    <th>Stol</th>
                    <th>Datum</th>
                    <th>Tid</th>
                    <th>Namn</th>
                    <th>Telefonnummer</th>
                </tr>
                <?php
                $sql = "SELECT chairs.name AS chair_name, bookings.booking_date, bookings.booking_time, bookings.name, bookings.phone_number 
                        FROM bookings 
                        JOIN chairs ON bookings.chair_id = chairs.id 
                        ORDER BY bookings.booking_date, bookings.booking_time";
                $result = $conn->query($sql);
                while($row = $result->fetch_assoc()) {
                    echo "<tr><td>{$row['chair_name']}</td><td>{$row['booking_date']}</td><td>{$row['booking_time']}</td><td>{$row['name']}</td><td>{$row['phone_number']}</td></tr>";
                }
                ?>
            </table>
        </div>
    </div>
    <script>
        document.querySelectorAll('.chair').forEach(chair => {
            chair.addEventListener('click', function() {
                document.querySelectorAll('.chair').forEach(chair => {
                    chair.classList.remove('selected');
                });
                this.classList.add('selected');
                document.getElementById('chair_id').value = this.getAttribute('data-chair-id');
            });
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>
