<?php
require_once 'db.php';
session_start();
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, full_name, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['full_name'];
            
            $_SESSION['just_logged_in'] = true;

            header("Location: index.php");
            exit;
        } else {
            $error = "<p class='p-4 mb-4 bg-red-100 text-red-700 rounded-md'>Invalid password.</p>";
        }
    } else {
        $error = "<p class='p-4 mb-4 bg-red-100 text-red-700 rounded-md'>User not found.</p>";
    }
    $stmt->close();
}
include 'header.php';
?>

<main class="max-w-md mx-auto px-4 py-12 flex-grow w-full">
    <h1 class="text-3xl font-bold text-center text-amber-900 mb-8">Login</h1>
    <?php echo $error; ?>
    <form action="login.php" method="POST" class="bg-white p-8 rounded-lg shadow-md border border-amber-100 space-y-6">
        <div>
            <label class="block text-gray-700 font-medium mb-2">Email Address</label>
            <input type="email" name="email" required class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-amber-600">
        </div>
        <div>
            <label class="block text-gray-700 font-medium mb-2">Password</label>
            <input type="password" name="password" required class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-amber-600">
        </div>
        <button type="submit" class="w-full bg-amber-800 text-white font-bold py-3 rounded-md hover:bg-amber-900">Login</button>
    </form>
</main>

<?php include 'footer.php'; ?>