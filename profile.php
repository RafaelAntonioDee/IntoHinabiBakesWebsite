<?php
require_once 'db.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT full_name, email, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

include 'header.php';
?>

<main class="max-w-2xl mx-auto px-4 py-12 flex-grow w-full">
    <div class="bg-white p-8 rounded-lg shadow-md border border-amber-100">
        <div class="flex items-center space-x-4 mb-6">
            <div class="w-16 h-16 bg-amber-800 text-white text-2xl font-bold flex items-center justify-center rounded-full">
                <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-amber-900"><?php echo htmlspecialchars($user['full_name']); ?></h1>
                <p class="text-gray-500 text-sm">Account Dashboard</p>
            </div>
        </div>

        <div class="space-y-4 border-t border-gray-100 pt-6">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase">Full Name</label>
                <p class="text-lg font-medium text-gray-800"><?php echo htmlspecialchars($user['full_name']); ?></p>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase">Email Address</label>
                <p class="text-lg font-medium text-gray-800"><?php echo htmlspecialchars($user['email']); ?></p>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase">Member Since</label>
                <p class="text-lg font-medium text-gray-800"><?php echo date("F j, Y", strtotime($user['created_at'])); ?></p>
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-gray-100 flex justify-between items-center">
            <a href="index.php" class="text-amber-800 font-semibold hover:underline">← Back to Home</a>
            <a href="logout.php" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md font-semibold text-sm transition">
                Logout
            </a>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>