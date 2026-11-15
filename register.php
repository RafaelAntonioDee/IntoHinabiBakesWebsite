<?php
require_once 'db.php';
$alert = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $full_name, $email, $password);

    if ($stmt->execute()) {
        $alert = "<p class='p-4 mb-4 bg-green-100 text-green-700 rounded-md'>Account created! <a href='login.php' class='underline'>Login here</a>.</p>";
    } else {
        $alert = "<p class='p-4 mb-4 bg-red-100 text-red-700 rounded-md'>Email already registered or registration failed.</p>";
    }
    $stmt->close();
}
include 'header.php';
?>

<main class="max-w-md mx-auto px-4 py-12 flex-grow w-full">
    <h1 class="text-3xl font-bold text-center text-amber-900 mb-8">Sign Up</h1>
    <?php echo $alert; ?>
    <form action="register.php" method="POST" class="bg-white p-8 rounded-lg shadow-md border border-amber-100 space-y-6">
        <div>
            <label class="block text-gray-700 font-medium mb-2">Full Name</label>
            <input type="text" name="full_name" required class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-amber-600">
        </div>
        <div>
            <label class="block text-gray-700 font-medium mb-2">Email Address</label>
            <input type="email" name="email" required class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-amber-600">
        </div>
        <div>
            <label class="block text-gray-700 font-medium mb-2">Password</label>
            <input type="password" name="password" required class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-amber-600">
        </div>
        <button type="submit" class="w-full bg-amber-800 text-white font-bold py-3 rounded-md hover:bg-amber-900">Create Account</button>
    </form>
</main>

<?php include 'includes/footer.php'; ?>