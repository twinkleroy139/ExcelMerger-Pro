<?php
// login.php
require_once __DIR__ . '/includes/auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!empty($username) && !empty($password)) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header('Location: index.php');
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    } else {
        $error = 'Please fill in all fields.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ExcelMerger Pro - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 min-h-screen flex items-center justify-center text-gray-100 font-mono">
    <div class="max-w-md w-full bg-gray-800 border border-cyan-500/30 p-8 rounded-xl shadow-2xl">
        <h2 class="text-2xl font-bold text-cyan-400 mb-2 uppercase tracking-wider text-center">System Login</h2>
        <p class="text-xs text-gray-400 text-center mb-6">Access your user-isolated history & master logs</p>

        <?php if (!empty($error)): ?>
            <div class="mb-4 bg-red-950/50 border border-red-500 text-red-300 p-3 rounded text-xs">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST" class="space-y-4">
            <div>
                <label class="block text-xs text-cyan-300 mb-1">USERNAME</label>
                <input type="text" name="username" required class="w-full bg-gray-900 border border-cyan-500/30 rounded px-3 py-2 text-sm text-gray-200 focus:outline-none focus:border-cyan-400">
            </div>
            <div>
                <label class="block text-xs text-cyan-300 mb-1">PASSWORD</label>
                <input type="password" name="password" required class="w-full bg-gray-900 border border-cyan-500/30 rounded px-3 py-2 text-sm text-gray-200 focus:outline-none focus:border-cyan-400">
            </div>
            <button type="submit" class="w-full bg-cyan-600 hover:bg-cyan-500 text-gray-950 font-bold py-2.5 rounded transition uppercase text-xs tracking-widest mt-2">
                Authenticate
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="index.php" class="text-xs text-gray-400 hover:text-cyan-300">&larr; Back to Dashboard</a>
        </div>
        
        <div class="mt-4 pt-4 border-t border-gray-700 text-center text-[10px] text-gray-500">
            Default Admin Creds: admin / password123
        </div>
    </div>
</body>
</html>