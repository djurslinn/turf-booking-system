<?php
session_start();
include 'config.php';

if (!isset($_SESSION['owner_id'])) {
    header("Location: login_owner.php");
    exit;
}

$success = $error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $turf_name = mysqli_real_escape_string($conn, $_POST['turf_name']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $grass_type = mysqli_real_escape_string($conn, $_POST['grass_type']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    $size = intval($_POST['size']);
    $map_url = mysqli_real_escape_string($conn, $_POST['map_url']);

    // Upload image
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image_name = basename($_FILES['image']['name']);
        $target_dir = "uploads/";
        $target_file = $target_dir . time() . "_" . $image_name;

        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_path = mysqli_real_escape_string($conn, $target_file);
        } else {
            $error = "Failed to upload image.";
        }
    } else {
        $error = "Please upload a turf image.";
    }

    if (!$error) {
        $landmark = mysqli_real_escape_string($conn, $_POST['landmark']);
        $street = mysqli_real_escape_string($conn, $_POST['street']);
        $city = mysqli_real_escape_string($conn, $_POST['city']);
        $district = mysqli_real_escape_string($conn, $_POST['district']);
        $state = mysqli_real_escape_string($conn, $_POST['state']);
        $pin_code = mysqli_real_escape_string($conn, $_POST['pin_code']);
	$price_day = mysqli_real_escape_string($conn, $_POST['price_day']);
	$price_night = mysqli_real_escape_string($conn, $_POST['price_night']);

        $address_sql = "INSERT INTO tbl_address (landmark, street, city, district, state, pin_code)
                        VALUES ('$landmark', '$street', '$city', '$district', '$state', '$pin_code')";

        if (mysqli_query($conn, $address_sql)) {
            $address_id = mysqli_insert_id($conn);
            $owner_id = $_SESSION['owner_id'];

            $turf_sql = "INSERT INTO turf (name, category,grass_type,description, size, image_path, map_url, owner_id, address_id,price_day,price_night)
                         VALUES ('$turf_name', '$category','$grass_type','$description', '$size', '$image_path', '$map_url', $owner_id, $address_id,$price_day,$price_night)";

            if (mysqli_query($conn, $turf_sql)) {
                $success = "Turf registered successfully. Awaiting admin approval.";
                $last_turf_id = mysqli_insert_id($conn);
            } else {
                $error = "Turf insert failed: " . mysqli_error($conn);
            }
        } else {
            $error = "Address insert failed: " . mysqli_error($conn);
        }
    }
}
?>



<!DOCTYPE html>
<html>
<head>
    <title>Owner Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: url('images/login_bg.png') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            flex-direction: column;
        }

        .main-content {
            flex: 1;
            padding: 80px 0 40px; /* Space below navbar and above footer */
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            max-width: 700px;
            margin: auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.2);
        }

        h2, h3, h4 {
            text-align: center;
            margin-top: 0;
        }

        input, textarea, select {
            width: 100%;
            padding: 10px;
            margin: 10px 0 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background: #28a745;
            color: white;
            border: none;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background: #218838;
        }

        .success { color: green; text-align: center; }
        .error { color: red; text-align: center; }

        a.button-link {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        a.button-link:hover {
            background: #0069d9;
        }

        footer {
            background: #333;
            color: white;
            text-align: center;
            padding: 10px 0;
        }

        @media (max-width: 600px) {
            .main-content {
                padding: 60px 10px 40px;
            }
            .container {
                padding: 20px;
                width: 100%;
                border-radius: 0;
                box-shadow: none;
            }
            h2, h3, h4 {
                font-size: 20px;
            }
            input, textarea, select {
                padding: 8px;
                font-size: 14px;
            }
            input[type="submit"], a.button-link {
                font-size: 14px;
                padding: 10px;
            }
        }
        .back-btn {
    position: fixed;       /* Sticky */
    top: 20px;             /* Distance from top */
    left: 20px;            /* Distance from left */
    z-index: 1000;         /* Stay above other content */
    
    display: inline-block;
    padding: 8px 16px;
    background: #2e7d32;   /* same green as action buttons */
    color: #fff;
    text-decoration: none;
    border-radius: 6px;
    font-weight: bold;
    transition: background 0.2s;
    box-shadow: 0 2px 6px rgba(0,0,0,0.3); /* subtle shadow */
}

.back-btn:hover {
    background: #1b5e20;   /* darker green on hover */
}

    </style>
</head>
<body>
     <a href="owner_dashboard.php" class="back-btn">← Back to Dashboard</a>
    <div class="main-content">
        <div class="container">
          
            <h2>Welcome, <?php echo htmlspecialchars($_SESSION['owner_name']); ?></h2>
            <h3>Register New Turf</h3>

            <?php if (isset($success)): ?>
                <p class='success'><?php echo $success; ?></p>
                
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <p class='error'><?php echo $error; ?></p>
            <?php endif; ?>

            <form method="POST" action="" enctype="multipart/form-data">
                <label>Turf Name</label>
                <input type="text" name="turf_name" required>

                <label>Category</label>
                <select name="category" required>
                    <option value="">--Select--</option>
                    <option value="FOOTBALL">FOOTBALL</option>
                    <option value="CRICKET">CRICKET</option>
                    <option value="MULTI_SPORT">MULTI SPORT</option>
                </select>

                 <label>Grass Type</label>
                <select name="grass_type" required>
                    <option value="">--Select--</option>
                    <option value="Natural Grass">Natural Grass</option>
                    <option value="Artificial Grass">Artificial/Synthetic Grass</option>
                    <option value="Hybrid Grass">Hybrid Grass(Artifical + Natural)</option>
                </select>
                
                <label>Size</label>
                <select name="size" required>
                    <option value="">--Select--</option>
                    <option value="5-a-Side">5-a-side</option>
                    <option value="7-a-Side">7-a-Side</option>
                </select>

                  <label></label>Description of Your Turf</label>   
                <input type="text" name="description" required>

                <label>Upload Turf Image</label>
                <input type="file" name="image" accept="image/*" required>

                <label>Map URL</label>
                <input type="url" name="map_url" required>

                <h4>Address Information</h4>

                <label>Landmark</label>
                <input type="text" name="landmark" required>

                <label>Street</label>
                <input type="text" name="street" required>

                <label>City</label>
                <input type="text" name="city" required>

                <label>District</label>
                <input type="text" name="district" required>

                <label>State</label>
                <input type="text" name="state" required>

                <label>Pin Code</label>
                <input type="text" name="pin_code" maxlength="6" required>

                <label>Price of Day</label>
                <input type="number" step="0.01" min="0" name="price_day" required>

                <label>Price of Night</label>
                <input type="number" step="0.01" min="0" name="price_night" required>

                <input type="submit" value="Register Turf">
            </form>
        </div>
    </div>

   
</body>
</html>
 <?php include 'footer.php'; ?>
