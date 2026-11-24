<?php
// =============================================================================
// Murli Creations - 1-Click Hostinger & Server Setup Wizard
// Guides the user through database configuration, checks, and migrations
// =============================================================================

session_start();

$pageTitle = "Murli Creations - Setup Wizard";
require_once __DIR__ . '/config/database.php';

$step = $_GET['step'] ?? 'welcome';
$error = null;
$success = null;
$migrationLogs = [];

// Handle Setup Action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'run_migration') {
        require_once __DIR__ . '/config/db_init.php';
        $res = run_database_migrations(true);
        if ($res['success']) {
            $success = "Database initialized and migrated successfully!";
            $migrationLogs = $res['messages'] ?? [];
            $step = 'completed';
        } else {
            $error = $res['message'] ?? "An error occurred during database migration.";
        }
    }
}

// System Pre-flight Checks
$phpVersion = phpversion();
$phpOk = version_compare($phpVersion, '7.4.0', '>=');
$pdoOk = extension_loaded('pdo') && extension_loaded('pdo_mysql');
$jsonOk = extension_loaded('json');
$storageWritable = is_writable(__DIR__ . '/storage') || (!file_exists(__DIR__ . '/storage') && is_writable(__DIR__));
$dbConnected = is_db_connected();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0F382C;
            --primary-dark: #0A261E;
            --gold: #D4AF37;
            --gold-light: #FBF7EE;
            --gold-border: #E8D8A6;
            --bg: #F4F6F5;
            --card-bg: #FFFFFF;
            --text-main: #1C2826;
            --text-muted: #5C6F68;
            --success: #10B981;
            --danger: #EF4444;
            --warning: #F59E0B;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 15px;
        }

        .setup-card {
            background: var(--card-bg);
            max-width: 720px;
            width: 100%;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
            border: 1px solid var(--gold-border);
            overflow: hidden;
        }

        .setup-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #fff;
            padding: 35px 30px;
            text-align: center;
            border-bottom: 3px solid var(--gold);
        }

        .setup-header h1 {
            font-family: 'Cinzel', serif;
            font-size: 26px;
            letter-spacing: 1px;
            margin-bottom: 6px;
            color: var(--gold);
        }

        .setup-header p {
            font-size: 14px;
            opacity: 0.88;
        }

        .setup-body {
            padding: 30px;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }
        .badge-success { background: #ECFDF5; color: #065F46; }
        .badge-danger { background: #FEF2F2; color: #991B1B; }
        .badge-warning { background: #FFFBEB; color: #92400E; }

        .check-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            margin-bottom: 25px;
        }

        .check-table td {
            padding: 12px 14px;
            border-bottom: 1px solid #E5E7EB;
            font-size: 14px;
        }

        .check-table td:last-child {
            text-align: right;
        }

        .code-box {
            background: #1E293B;
            color: #F8FAFC;
            padding: 16px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            line-height: 1.6;
            margin: 15px 0;
            overflow-x: auto;
        }

        .alert {
            padding: 16px;
            border-radius: 10px;
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 20px;
        }
        .alert-danger { background: #FEF2F2; border: 1px solid #F87171; color: #991B1B; }
        .alert-success { background: #ECFDF5; border: 1px solid #34D399; color: #065F46; }
        .alert-warning { background: #FFFBEB; border: 1px solid #FCD34D; color: #92400E; }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 14px 28px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--gold) 0%, #B89025 100%);
            color: #fff;
            width: 100%;
            box-shadow: 0 4px 12px rgba(212, 175, 55, 0.3);
        }
        .btn-primary:hover {
            opacity: 0.95;
            transform: translateY(-1px);
        }

        .btn-outline {
            border: 1px solid var(--primary);
            color: var(--primary);
            background: #fff;
        }
        .btn-outline:hover {
            background: #F0FDF4;
        }

        .log-box {
            background: #0B1914;
            color: #A7F3D0;
            padding: 15px;
            border-radius: 8px;
            font-family: monospace;
            font-size: 13px;
            max-height: 220px;
            overflow-y: auto;
            margin: 15px 0;
        }
        .log-item {
            padding: 3px 0;
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }

        .credentials-card {
            background: var(--gold-light);
            border: 1px solid var(--gold-border);
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="setup-card">
    <div class="setup-header">
        <h1>MURLI CREATIONS</h1>
        <p>Heritage Lippan Art & Devotional Studio • Deployment Setup Wizard</p>
    </div>

    <div class="setup-body">

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <strong>Error:</strong> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($step === 'completed'): ?>
            <div class="alert alert-success">
                <strong>🎉 Setup Completed Successfully!</strong><br>
                All 18 MySQL tables, authentic products, categories, reviews, and admin system have been created and initialized.
            </div>

            <?php if (!empty($migrationLogs)): ?>
                <div class="log-box">
                    <?php foreach ($migrationLogs as $log): ?>
                        <div class="log-item">✓ <?= htmlspecialchars($log) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="credentials-card">
                <h3 style="font-family:'Cinzel',serif; color:var(--primary); margin-bottom:10px;">Admin Access Credentials</h3>
                <p style="font-size:14px; margin-bottom:6px;"><strong>Admin Portal URL:</strong> <a href="/admin.php" target="_blank" style="color:var(--primary); font-weight:600;">/admin.php</a></p>
                <p style="font-size:14px; margin-bottom:6px;"><strong>Username:</strong> <code>admin</code></p>
                <p style="font-size:14px; margin-bottom:10px;"><strong>Password:</strong> <code>murli@2026</code></p>
                <small style="color:var(--text-muted);">Please change your admin password upon first login from the Admin Panel Settings.</small>
            </div>

            <div style="display:flex; gap:12px; margin-top:25px;">
                <a href="/index.php" class="btn btn-outline" style="flex:1;">Visit Frontend Store</a>
                <a href="/admin.php" class="btn btn-primary" style="flex:1;">Go to Admin Panel</a>
            </div>

            <div style="margin-top:20px; font-size:12px; text-align:center; color:var(--text-muted);">
                🔒 <em>For security, you may now delete or rename <code>setup.php</code> in your File Manager.</em>
            </div>

        <?php else: ?>

            <h3 style="font-size:17px; margin-bottom:12px; color:var(--primary);">System & Server Pre-Flight Check</h3>

            <table class="check-table">
                <tr>
                    <td><strong>PHP Version</strong> (Requires 7.4+)</td>
                    <td>
                        <?php if ($phpOk): ?>
                            <span class="status-badge badge-success">✓ <?= htmlspecialchars($phpVersion) ?></span>
                        <?php else: ?>
                            <span class="status-badge badge-danger">✗ <?= htmlspecialchars($phpVersion) ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td><strong>PDO MySQL Extension</strong></td>
                    <td>
                        <?php if ($pdoOk): ?>
                            <span class="status-badge badge-success">✓ Enabled</span>
                        <?php else: ?>
                            <span class="status-badge badge-danger">✗ Missing</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td><strong>JSON Extension</strong></td>
                    <td>
                        <?php if ($jsonOk): ?>
                            <span class="status-badge badge-success">✓ Enabled</span>
                        <?php else: ?>
                            <span class="status-badge badge-danger">✗ Missing</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td><strong>Storage Directory Permissions</strong></td>
                    <td>
                        <?php if ($storageWritable): ?>
                            <span class="status-badge badge-success">✓ Writable</span>
                        <?php else: ?>
                            <span class="status-badge badge-warning">⚠ Check storage/ chmod</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td><strong>MySQL Database Connection</strong></td>
                    <td>
                        <?php if ($dbConnected): ?>
                            <span class="status-badge badge-success">✓ Connected (<?= htmlspecialchars(DB_NAME) ?>)</span>
                        <?php else: ?>
                            <span class="status-badge badge-danger">✗ Not Connected</span>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>

            <?php if (!$dbConnected): ?>
                <div class="alert alert-warning">
                    <strong>Notice: MySQL is not yet connected.</strong><br>
                    Please update your Hostinger database credentials in <code>config/database.php</code>:
                    <div class="code-box">
define('DB_HOST', 'localhost'); // In Hostinger, usually 'localhost'<br>
define('DB_NAME', 'u123456789_murli'); // Your Hostinger Database Name<br>
define('DB_USER', 'u123456789_admin'); // Your Hostinger Database Username<br>
define('DB_PASS', 'YourSecretPassword@123'); // Your Database Password
                    </div>
                    <em>After editing <code>config/database.php</code>, refresh this page.</em>
                </div>
            <?php else: ?>
                <div class="alert alert-success">
                    <strong>✓ MySQL Connected:</strong> Ready to initialize all tables, seed products, and configure admin.
                </div>

                <form method="POST" action="setup.php">
                    <input type="hidden" name="action" value="run_migration">
                    <button type="submit" class="btn btn-primary">
                        🚀 Run Complete Database Migration & Initialize Store
                    </button>
                </form>
            <?php endif; ?>

            <div style="margin-top:20px; font-size:13px; color:var(--text-muted); line-height:1.6;">
                <strong>💡 Note:</strong> Frontend and Admin Panel are unified in this package. Both will run automatically on your domain as soon as database migration finishes.
            </div>

        <?php endif; ?>

    </div>
</div>

</body>
</html>
