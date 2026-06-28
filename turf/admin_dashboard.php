<?php
require_once 'config.php';
session_start();

if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_name'])) {
    header("Location: admin_login.php");
    exit();
}

$justApproved = false;
$justRejected = false;
$justDeleted = false;

// Handle Approve
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approve']) && isset($_POST['turf_id'])) {
    $turf_id = intval($_POST['turf_id']);
    $sql = "UPDATE turf SET is_approved = 1 WHERE turf_id = $turf_id";
    if (mysqli_query($conn, $sql)) {
        $justApproved = true;
    }
}

// Handle Reject
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reject']) && isset($_POST['turf_id'])) {
    $turf_id = intval($_POST['turf_id']);
    $sql = "UPDATE turf SET is_approved = 0 WHERE turf_id = $turf_id";
    if (mysqli_query($conn, $sql)) {
        $justRejected = true;
    }
}

// Handle Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete']) && isset($_POST['type']) && isset($_POST['id'])) {
    $type = $_POST['type'];
    $id = intval($_POST['id']);

    if ($type == 'turf') {
        $sql = "DELETE FROM turf WHERE turf_id = $id";
    } elseif ($type == 'user') {
        $sql = "DELETE FROM customer WHERE customer_id = $id";
    } elseif ($type == 'owner') {
        $sql = "DELETE FROM owner WHERE owner_id = $id";
    }

    if (isset($sql) && mysqli_query($conn, $sql)) {
        $justDeleted = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            background-color: #0f172a;
            color: #e2e8f0;
        }

        .sidebar {
            width: 220px;
            background: #1e293b;
            min-height: 100vh;
            padding: 20px;
            position: fixed;
        }
        .sidebar h2 { color: #a3e635; margin-bottom: 30px; font-size: 20px; }
        .sidebar a {
            display: block;
            color: #e2e8f0;
            text-decoration: none;
            margin: 12px 0;
            padding: 10px;
            border-radius: 6px;
            transition: background 0.2s;
        }
        .sidebar a:hover, .sidebar a.active { background: #a3e635; color: #0f172a; }
        .sidebar .logout{margin-top:auto;background:rgba(255,255,255,.1);text-align:center}

        .main { margin-left: 220px; padding: 20px; width: 100%; }

        h1 { margin-bottom: 20px; color: #a3e635; }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            justify-content: start;
        }

        .card {
            background: #1e293b;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.4);
            padding: 15px;
            max-width: 300px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .card img { width: 100%; height: 180px; object-fit: cover; border-radius: 8px; }
        .card h3 { margin: 10px 0; }
        .card p { font-size: 14px; margin: 6px 0; color: #cbd5e1; }

        .btn {
            display: inline-block;
            padding: 8px 14px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin: 5px 3px 0 0;
            font-size: 13px;
        }
        .btn-approve { background: #22c55e; color: white; }
        .btn-approve:hover { background: #16a34a; }
        .btn-reject { background: #ef4444; color: white; }
        .btn-reject:hover { background: #b91c1c; }
        .btn-view { background: #3b82f6; color: white; }
        .btn-view:hover { background: #2563eb; }
        .btn-delete { background: #f97316; color: white; }
        .btn-delete:hover { background: #c2410c; }

        .modal {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.6);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: #1e293b;
            padding: 20px;
            border-radius: 10px;
            max-width: 500px;
            width: 90%;
        }
        .modal h3 { margin-bottom: 10px; color: #a3e635; }
        .modal p { margin: 6px 0; color: #cbd5e1; }
        .close-btn { float: right; cursor: pointer; color: #ef4444; }

        table { width: 100%; border-collapse: collapse; margin-top: 15px; background: #1e293b; border-radius: 10px; overflow: hidden; }
        table th, table td { padding: 12px; border-bottom: 1px solid #334155; text-align: left; }
        table th { background: #0f172a; color: #a3e635; }

        @media (max-width: 768px) {
            table, thead, tbody, th, td, tr { display: block; width: 100%; }
            table tr { margin-bottom: 15px; }
            table td { text-align: right; padding-left: 50%; position: relative; }
            table td::before { content: attr(data-label); position: absolute; left: 12px; width: 45%; padding-right: 10px; white-space: nowrap; color: #a3e635; font-weight: bold; text-align: left; }
            table th { display: none; }
        }

        .no-data { text-align: center; color: #94a3b8; margin-top: 40px; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="?section=approvals" class="<?php echo (!isset($_GET['section']) || $_GET['section']=='approvals')?'active':''; ?>">Turf Approvals</a>
    <a href="?section=turfs" class="<?php echo (isset($_GET['section']) && $_GET['section']=='turfs')?'active':''; ?>">All Turfs</a>
    <a href="?section=users" class="<?php echo (isset($_GET['section']) && $_GET['section']=='users')?'active':''; ?>">Users</a>
    <a href="?section=owners" class="<?php echo (isset($_GET['section']) && $_GET['section']=='owners')?'active':''; ?>">Owners</a>
    <a class="logout" href="logout.php">Logout</a>
</div>

<div class="main">
    <?php
    $section = $_GET['section'] ?? 'approvals';

    if ($section == 'approvals') {
        echo "<h1>Pending Turf Approvals</h1>";
        $sql = "SELECT t.*, o.name AS owner_name, o.email AS owner_email FROM turf t JOIN owner o ON t.owner_id = o.owner_id WHERE is_approved = 0";
        $result = mysqli_query($conn, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            echo "<div class='grid'>";
            while ($row = mysqli_fetch_assoc($result)) {
                $imagePath = !empty($row['image_path']) ? $row['image_path'] : 'images/default_turf.png';
                $details = htmlspecialchars(json_encode($row));
                echo "<div class='card'>";
                echo "<img src='".htmlspecialchars($imagePath)."' alt='Turf'>";
                echo "<h3>".htmlspecialchars($row['name'])."</h3>";
                echo "<form method='POST'>
                        <input type='hidden' name='turf_id' value='".$row['turf_id']."'>
                        <button type='submit' name='approve' class='btn btn-approve'>Approve</button>
                        <button type='submit' name='reject' class='btn btn-reject'>Reject</button>
                        <button type='button' class='btn btn-view' onclick='showDetails(".$details.")'>View Details</button>
                      </form>";
                echo "</div>";
            }
            echo "</div>";
        } else {
            echo "<p class='no-data'>No unapproved turfs found.</p>";
        }
    }

    if ($section == 'turfs') {
        echo "<h1>All Turfs & Bookings</h1>";
        $sql = "SELECT t.*, (SELECT COUNT(*) FROM slots s WHERE s.turf_id = t.turf_id) AS total_bookings FROM turf t";
        $result = mysqli_query($conn, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            echo "<table>
                    <tr><th>Turf</th><th>Owner ID</th><th>Status</th><th>Total Bookings</th><th>Action</th></tr>";
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                        <td data-label='Turf'>".htmlspecialchars($row['name'])."</td>
                        <td data-label='Owner ID'>".$row['owner_id']."</td>
                        <td data-label='Status'>".($row['is_approved'] ? 'Approved' : 'Pending')."</td>
                        <td data-label='Total Bookings'>".$row['total_bookings']."</td>
                        <td><form method='POST'><input type='hidden' name='type' value='turf'><input type='hidden' name='id' value='".$row['turf_id']."'><button type='submit' name='delete' class='btn btn-delete' onclick=\"return confirm('Delete this turf?');\">Delete</button></form></td>
                      </tr>";
            }
            echo "</table>";
        } else { echo "<p class='no-data'>No turfs found.</p>"; }
    }

    if ($section == 'users') {
        echo "<h1>Registered Users</h1>";
        $sql = "SELECT * FROM customer";
        $result = mysqli_query($conn, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            echo "<table>
                    <tr><th>User ID</th><th>Name</th><th>Email</th><th>Action</th></tr>";
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                        <td data-label='User ID'>".$row['customer_id']."</td>
                        <td data-label='Name'>".htmlspecialchars($row['name'])."</td>
                        <td data-label='Email'>".htmlspecialchars($row['email'])."</td>
                        <td><form method='POST'><input type='hidden' name='type' value='user'><input type='hidden' name='id' value='".$row['customer_id']."'><button type='submit' name='delete' class='btn btn-delete' onclick=\"return confirm('Delete this user?');\">Delete</button></form></td>
                      </tr>";
            }
            echo "</table>";
        } else { echo "<p class='no-data'>No users found.</p>"; }
    }

    if ($section == 'owners') {
        echo "<h1>Registered Owners</h1>";
        $sql = "SELECT * FROM owner";
        $result = mysqli_query($conn, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            echo "<table>
                    <tr><th>Owner ID</th><th>Name</th><th>Email</th><th>Action</th></tr>";
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                        <td data-label='Owner ID'>".$row['owner_id']."</td>
                        <td data-label='Name'>".htmlspecialchars($row['name'])."</td>
                        <td data-label='Email'>".htmlspecialchars($row['email'])."</td>
                        <td><form method='POST'><input type='hidden' name='type' value='owner'><input type='hidden' name='id' value='".$row['owner_id']."'><button type='submit' name='delete' class='btn btn-delete' onclick=\"return confirm('Delete this owner?');\">Delete</button></form></td>
                      </tr>";
            }
            echo "</table>";
        } else { echo "<p class='no-data'>No owners found.</p>"; }
    }
    ?>
</div>

<div id="detailsModal" class="modal">
  <div class="modal-content">
    <span class="close-btn" onclick="closeModal()">&times;</span>
    <h3>Turf Details</h3>
    <div id="modalBody"></div>
  </div>
</div>

<?php if ($justApproved): ?><script>alert("Turf approved successfully!");</script><?php endif; ?>
<?php if ($justRejected): ?><script>alert("Turf rejected successfully!");</script><?php endif; ?>
<?php if ($justDeleted): ?><script>alert("Deleted successfully!");</script><?php endif; ?>

<script>
function showDetails(data) {
    var details = typeof data === 'string' ? JSON.parse(data) : data;
    var body = "<p><b>Name:</b> "+details.name+"</p>"+
               "<p><b>Category:</b> "+details.category+"</p>"+
               "<p><b>Size:</b> "+details.size+"</p>"+
               "<p><b>Location: </b><a href=\" "+details.map_url+"\"><button type='button' class='btn btn-view'>view on map</button></a></p>"+
               "<p><b>Owner:</b> "+details.owner_name+" ("+details.owner_email+")</p>";

    document.getElementById('modalBody').innerHTML = body;
    document.getElementById('detailsModal').style.display = 'flex';
}
function closeModal(){
    document.getElementById('detailsModal').style.display = 'none';
}
</script>

</body>
</html>
