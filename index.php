<?php
session_start();

function getTimeBasedGreeting() {
    // Set Brazil timezone
    date_default_timezone_set('America/Sao_Paulo');
    $hour = (int)date('H');
    
    // Debug: uncomment next line to see current hour
    // echo "Current hour: " . $hour . " ";
    
    if ($hour < 12) {
        return 'Good morning';
    } elseif ($hour < 18) {
        return 'Good afternoon';
    } else {
        return 'Good evening';
    }
}

function getVersion() {
    $versionFile = 'version.json';
    if (file_exists($versionFile)) {
        $versionData = json_decode(file_get_contents($versionFile), true);
        return $versionData['version'] ?? '1.0.0';
    }
    return '1.0.0';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    $name = trim($_POST['name']);
    if (!empty($name)) {
        $session_id = uniqid('ss_', true);
        $session_dir = "sessions/$session_id";
        
        if (!is_dir($session_dir)) {
            mkdir($session_dir, 0755, true);
            mkdir("$session_dir/images", 0755, true);
        }
        
        $config = [
            'name' => $name,
            'session_id' => $session_id,
            'created_at' => date('c'),
            'last_activity' => date('c'),
            'expires_at' => date('c', time() + 28800), // 8 hours
            'images' => [],
            'slideshow' => [
                'transition_time' => 3,
                'transition_effect' => 'fade',
                'auto_play' => true,
                'show_progress_bar' => false
            ]
        ];
        
        file_put_contents("$session_dir/config.json", json_encode($config, JSON_PRETTY_PRINT));
        $_SESSION['session_id'] = $session_id;
        
        header('Location: dashboard.php');
        exit;
    }
}

$existing_session = null;
if (isset($_SESSION['session_id'])) {
    $session_dir = "sessions/{$_SESSION['session_id']}";
    if (is_dir($session_dir) && file_exists("$session_dir/config.json")) {
        $config = json_decode(file_get_contents("$session_dir/config.json"), true);
        if ($config && strtotime($config['expires_at']) > time()) {
            $existing_session = $config;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StarSlider - Dynamic Image Slideshow System</title>
    <link rel="icon" type="image/png" href="assets/favicon.png">
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <!-- Dynamic Starfield Background -->
    
    <!-- Stars Amarelas (Pequenas) -->
    <div class="star yellow-small" style="top: 15%; left: 20%; animation-delay: -0.5s;"></div>
    <div class="star yellow-small" style="top: 35%; left: 80%; animation-delay: -1.2s;"></div>
    <div class="star yellow-small" style="top: 60%; left: 15%; animation-delay: -2s;"></div>
    <div class="star yellow-small" style="top: 80%; left: 75%; animation-delay: -0.8s;"></div>
    <div class="star yellow-small" style="top: 10%; left: 60%; animation-delay: -2.5s;"></div>
    <div class="star yellow-small" style="top: 45%; left: 40%; animation-delay: -1.8s;"></div>
    <div class="star yellow-small" style="top: 25%; left: 90%; animation-delay: -0.3s;"></div>
    <div class="star yellow-small" style="top: 70%; left: 5%; animation-delay: -2.9s;"></div>
    
    <!-- Stars Laranjas (Médias) -->
    <div class="star orange-medium" style="top: 25%; left: 50%; animation-delay: -1s;"></div>
    <div class="star orange-medium" style="top: 55%; left: 85%; animation-delay: -2.3s;"></div>
    <div class="star orange-medium" style="top: 75%; left: 30%; animation-delay: -0.3s;"></div>
    <div class="star orange-medium" style="top: 20%; left: 10%; animation-delay: -1.7s;"></div>
    <div class="star orange-medium" style="top: 65%; left: 65%; animation-delay: -2.8s;"></div>
    <div class="star orange-medium" style="top: 5%; left: 45%; animation-delay: -0.7s;"></div>
    <div class="star orange-medium" style="top: 85%; left: 95%; animation-delay: -1.4s;"></div>
    
    <!-- Stars Azuis (Grandes) -->
    <div class="star blue-large" style="top: 30%; left: 70%; animation-delay: -0.7s;"></div>
    <div class="star blue-large" style="top: 70%; left: 45%; animation-delay: -1.4s;"></div>
    <div class="star blue-large" style="top: 40%; left: 25%; animation-delay: -2.1s;"></div>
    <div class="star blue-large" style="top: 85%; left: 90%; animation-delay: -0.9s;"></div>
    <div class="star blue-large" style="top: 12%; left: 35%; animation-delay: -2.6s;"></div>
    <div class="star blue-large" style="top: 90%; left: 20%; animation-delay: -1.1s;"></div>
    
    <!-- Stars Vermelhas (Gigantes) -->
    <div class="star red-giant" style="top: 50%; left: 5%; animation-delay: -1.5s;"></div>
    <div class="star red-giant" style="top: 15%; left: 85%; animation-delay: -2.7s;"></div>
    <div class="star red-giant" style="top: 90%; left: 55%; animation-delay: -0.4s;"></div>
    <div class="star red-giant" style="top: 35%; left: 95%; animation-delay: -1.9s;"></div>
    
    <!-- Stars Roxas (Médias) -->
    <div class="star purple-medium" style="top: 5%; left: 35%; animation-delay: -1.9s;"></div>
    <div class="star purple-medium" style="top: 95%; left: 20%; animation-delay: -0.6s;"></div>
    <div class="star purple-medium" style="top: 40%; left: 95%; animation-delay: -2.4s;"></div>
    <div class="star purple-medium" style="top: 65%; left: 8%; animation-delay: -1.3s;"></div>
    <div class="star purple-medium" style="top: 22%; left: 65%; animation-delay: -2.1s;"></div>
    <div class="star purple-medium" style="top: 78%; left: 88%; animation-delay: -0.8s;"></div>
    
    <!-- Stars Cinzas (Pequenas) -->
    <div class="star gray-small" style="top: 25%; left: 5%; animation-delay: -1.1s;"></div>
    <div class="star gray-small" style="top: 65%; left: 95%; animation-delay: -2.6s;"></div>
    <div class="star gray-small" style="top: 85%; left: 35%; animation-delay: -0.2s;"></div>
    <div class="star gray-small" style="top: 5%; left: 75%; animation-delay: -1.6s;"></div>
    <div class="star gray-small" style="top: 42%; left: 12%; animation-delay: -2.2s;"></div>
    <div class="star gray-small" style="top: 77%; left: 92%; animation-delay: -0.9s;"></div>
    <div class="star gray-small" style="top: 18%; left: 48%; animation-delay: -1.8s;"></div>
    <div class="star gray-small" style="top: 88%; left: 68%; animation-delay: -0.5s;"></div>
    
    <!-- Planetas Grandes -->
    <div class="planet jupiter" style="top: 20%; left: 10%; animation-delay: -10s;"></div>
    <div class="planet saturn-big" style="top: 70%; left: 80%; animation-delay: -25s;"></div>
    <div class="planet neptune" style="top: 45%; left: 85%; animation-delay: -40s;"></div>
    
    <!-- Nebulosas Coloridas -->
    <div class="nebula orange" style="width: 200px; height: 200px; top: 10%; left: 70%; animation-delay: -8s;"></div>
    <div class="nebula blue" style="width: 250px; height: 250px; top: 60%; left: 5%; animation-delay: -15s;"></div>
    <div class="nebula purple" style="width: 180px; height: 180px; top: 30%; left: 40%; animation-delay: -22s;"></div>
    <div class="nebula pink" style="width: 150px; height: 150px; top: 80%; left: 60%; animation-delay: -12s;"></div>
    

    <div class="welcome-container">
        <div class="welcome-content">
            <?php if ($existing_session): ?>
                <img src="assets/logo.png" alt="Nehemiah Labs" class="logo">
                <div class="greeting"><?= getTimeBasedGreeting() ?>, <?= htmlspecialchars($existing_session['name']) ?>!</div>
                <div class="question">Welcome back to your slideshow session</div>
                
                <div class="name-input-container">
                    <a href="dashboard.php" class="btn-primary">Continue Session</a>
                    <button type="button" class="btn-primary" onclick="startNewSession()" style="margin-left: 15px; background: rgba(255, 255, 255, 0.9); color: #9b7bc7; border: 2px solid #9b7bc7;">Start New Session</button>
                </div>
                
                <div class="new-session-form" id="newSessionForm" style="display: none;">
                    <div class="greeting"><?= getTimeBasedGreeting() ?>!</div>
                    <div class="question">What's your name?</div>
                    <form method="POST" action="">
                        <div class="name-input-container">
                            <input type="text" name="name" class="name-input" placeholder="Enter your name" required maxlength="50">
                        </div>
                        <div class="button-container" style="margin-top: 20px;">
                            <button type="submit" class="btn-primary">Start Session</button>
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <img src="assets/logo.png" alt="Nehemiah Labs" class="logo">
                <div class="greeting"><?= getTimeBasedGreeting() ?>!</div>
                <div class="question">What's your name?</div>
                
                <form method="POST" action="">
                    <div class="name-input-container">
                        <input type="text" name="name" class="name-input" placeholder="Enter your name" required maxlength="50">
                    </div>
                    <div class="button-container" style="margin-top: 20px;">
                        <button type="submit" class="btn-primary">Start Session</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <div class="footer-info">
        <div>This is StarSlider - A dynamic image slideshow system for professional presentations</div>
        <div>&copy; <?= date('Y') ?> Nehemiah Labs. All rights reserved | Version <?= getVersion() ?></div>
    </div>

    <script>
        function startNewSession() {
            document.querySelector('.name-input-container').style.display = 'none';
            document.querySelector('#newSessionForm').style.display = 'block';
        }
    </script>
</body>
</html>