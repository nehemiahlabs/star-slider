<?php
session_start();

if (!isset($_SESSION['session_id'])) {
    header('Location: index.php');
    exit;
}

$session_id = $_SESSION['session_id'];
$session_dir = "sessions/$session_id";

if (!is_dir($session_dir) || !file_exists("$session_dir/config.json")) {
    session_destroy();
    header('Location: index.php');
    exit;
}

$config = json_decode(file_get_contents("$session_dir/config.json"), true);

if (!$config || strtotime($config['expires_at']) <= time()) {
    session_destroy();
    header('Location: index.php');
    exit;
}

// Renew session activity
$config['last_activity'] = date('c');
file_put_contents("$session_dir/config.json", json_encode($config, JSON_PRETTY_PRINT));

if (empty($config['images'])) {
    header('Location: dashboard.php');
    exit;
}

// Sort images by order
usort($config['images'], function($a, $b) {
    return $a['order'] - $b['order'];
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slideshow - StarSlider</title>
    <link rel="icon" type="image/png" href="assets/favicon.png">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #000;
            font-family: system-ui, 'IBM Plex Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            overflow: hidden;
            cursor: none;
        }

        .slideshow-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: all 0.8s ease-in-out;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .slide.active {
            opacity: 1;
        }

        .slide img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            display: block;
        }

        .slide.fade-out {
            opacity: 0;
        }

        .slide.slide-left {
            transform: translateX(-100%);
        }

        .slide.slide-right {
            transform: translateX(100%);
        }

        .slide.zoom-in {
            transform: scale(1.2);
        }

        .slide.zoom-out {
            transform: scale(0.8);
        }

        .controls {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 10px;
            background: rgba(0, 0, 0, 0.7);
            padding: 10px 20px;
            border-radius: 25px;
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 1000;
        }

        .controls.visible {
            opacity: 1;
        }

        .controls button {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 8px 12px;
            border-radius: 15px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.3s ease;
        }

        .controls button:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .progress-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            height: 3px;
            background: rgba(155, 123, 199, 0.8);
            transition: width linear;
            z-index: 1000;
        }

        .exit-hint {
            position: fixed;
            top: 20px;
            right: 20px;
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
            background: rgba(0, 0, 0, 0.5);
            padding: 10px 15px;
            border-radius: 20px;
            opacity: 1;
            transition: opacity 0.3s ease;
            z-index: 1000;
        }

        .exit-hint.hidden {
            opacity: 0;
        }

        @keyframes slideInLeft {
            from { transform: translateX(-100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        @keyframes zoomIn {
            from { transform: scale(0.8); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        @keyframes zoomOut {
            from { transform: scale(1.2); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes greenBlink {
            0%, 90% {
                box-shadow: 0 4px 15px rgba(40, 167, 69, 0.4);
            }
            95% {
                box-shadow: 0 4px 25px rgba(40, 167, 69, 0.8), 0 0 20px rgba(255, 255, 255, 0.3);
                transform: scale(1.02);
            }
            100% {
                box-shadow: 0 4px 15px rgba(40, 167, 69, 0.4);
                transform: scale(1);
            }
        }

        .slide.animate-slide-left {
            animation: slideInLeft 0.8s ease-out forwards;
        }

        .slide.animate-slide-right {
            animation: slideInRight 0.8s ease-out forwards;
        }

        .slide.animate-zoom-in {
            animation: zoomIn 0.8s ease-out forwards;
        }

        .slide.animate-zoom-out {
            animation: zoomOut 0.8s ease-out forwards;
        }

        .slide.animate-fade {
            animation: fadeIn 0.8s ease-out forwards;
        }
    </style>
</head>
<body>
    <div class="slideshow-container" id="slideshowContainer">
        <?php foreach ($config['images'] as $index => $image): ?>
            <div class="slide <?= $index === 0 ? 'active' : '' ?>" data-index="<?= $index ?>">
                <img src="<?= htmlspecialchars($session_dir . '/images/' . $image['filename']) ?>" 
                     alt="<?= htmlspecialchars($image['original_name']) ?>"
                     loading="lazy">
            </div>
        <?php endforeach; ?>
    </div>

    <div class="controls" id="controls">
        <button onclick="previousSlide()">◀ Previous</button>
        <button onclick="togglePlayPause()" id="playPauseBtn">⏸ Pause</button>
        <button onclick="nextSlide()">Next ▶</button>
        <button onclick="exitTodashboard()" id="exitBtn">✕ Exit</button>
    </div>

    <?php if ($config['slideshow']['show_progress_bar'] ?? false): ?>
    <div class="progress-bar" id="progressBar"></div>
    <?php endif; ?>
    

    <script>
        const config = <?= json_encode($config) ?>;
        let currentSlide = 0;
        let slideInterval = null;
        let isPlaying = true;
        let progressInterval = null;
        let progressStart = 0;
        let controlsTimeout = null;
        let isExitingViaButton = false;

        const slides = document.querySelectorAll('.slide');
        const controls = document.getElementById('controls');
        const progressBar = document.getElementById('progressBar');
        const playPauseBtn = document.getElementById('playPauseBtn');

        document.addEventListener('DOMContentLoaded', function() {
            // Show click prompt to enter fullscreen
            showFullscreenPrompt();
            startSlideshow();
            
            // Add additional click listener to exit button
            const exitBtn = document.getElementById('exitBtn');
            if (exitBtn) {
                exitBtn.addEventListener('click', function(e) {
                    console.log('Exit button clicked via event listener');
                    e.preventDefault();
                    e.stopPropagation();
                    exitFullscreen();
                });
            }
        });

        function showFullscreenPrompt() {
            // Always show the large center prompt first
            showManualPrompt();
            
            // Check if we should auto-enter fullscreen
            if (sessionStorage.getItem('autoFullscreen') === 'true') {
                sessionStorage.removeItem('autoFullscreen');
                // Add a small delay to ensure page is fully loaded, then try fullscreen
                setTimeout(() => {
                    enterFullscreen();
                }, 500);
            }
        }

        function showManualPrompt() {
            // Remove any existing prompts first
            const existingPrompt = document.getElementById('fullscreenPrompt');
            if (existingPrompt) {
                existingPrompt.remove();
            }
            
            const promptDiv = document.createElement('div');
            promptDiv.id = 'fullscreenPrompt';
            promptDiv.innerHTML = `
                <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
                           background: rgba(0, 0, 0, 0.8); z-index: 9999; display: flex; 
                           align-items: center; justify-content: center;">
                    <div style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f1419 100%); 
                               backdrop-filter: blur(20px); border: 2px solid rgba(155, 123, 199, 0.4); 
                               border-radius: 20px; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6), 0 0 30px rgba(155, 123, 199, 0.2); 
                               padding: 40px; text-align: center; color: white; font-family: inherit; 
                               max-width: 500px; margin: 20px;">
                        <div style="color: #9b7bc7; font-size: 2rem; margin-bottom: 15px;">⛶</div>
                        <h3 style="margin-bottom: 20px; font-size: 24px; font-weight: 600; color: white;">
                            Enter Fullscreen Mode
                        </h3>
                        <p style="margin-bottom: 30px; font-size: 16px; color: rgba(255, 255, 255, 0.8); line-height: 1.5;">
                            For the best slideshow experience, we recommend using fullscreen mode. 
                            This will hide browser elements and show your images at maximum size.
                        </p>
                        <div style="margin-bottom: 20px;">
                            <button onclick="enterFullscreen(); hideLargePromptAndShowSmall();" 
                                    style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); 
                                           color: white; border: none; padding: 16px 32px; 
                                           border-radius: 25px; cursor: pointer; font-size: 18px;
                                           animation: greenBlink 3s infinite; font-weight: 600; 
                                           margin: 0 10px; min-width: 200px;
                                           box-shadow: 0 4px 15px rgba(40, 167, 69, 0.4);
                                           transition: all 0.3s ease;">
                                ⛶ Enter Fullscreen
                            </button>
                        </div>
                        <div>
                            <button onclick="hideLargePromptAndShowSmall();" 
                                    style="background: transparent; color: rgba(255,255,255,0.7); 
                                           border: 1px solid rgba(255,255,255,0.3); 
                                           padding: 12px 24px; border-radius: 25px; cursor: pointer; 
                                           font-size: 14px; transition: all 0.3s ease;
                                           min-width: 150px;">
                                Skip for now
                            </button>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(promptDiv);
        }

        function hideLargePromptAndShowSmall() {
            // Hide the large prompt
            const largePrompt = document.getElementById('fullscreenPrompt');
            if (largePrompt) {
                largePrompt.remove();
            }
            
            // Show small persistent button
            showPersistentFullscreenButton();
        }

        function enterFullscreen() {
            const elem = document.documentElement;
            
            console.log('Attempting to enter fullscreen...');
            
            // Try different fullscreen methods
            if (elem.requestFullscreen) {
                elem.requestFullscreen()
                    .then(() => {
                        console.log('Successfully entered fullscreen');
                        hideAllFullscreenPrompts();
                    })
                    .catch((error) => {
                        console.log('Fullscreen request failed:', error);
                        // Don't show button automatically - user can click Skip if needed
                    });
            } else if (elem.mozRequestFullScreen) {
                try {
                    elem.mozRequestFullScreen();
                    console.log('Requested Mozilla fullscreen');
                    setTimeout(hideAllFullscreenPrompts, 500);
                } catch (e) {
                    console.log('Mozilla fullscreen failed:', e);
                }
            } else if (elem.webkitRequestFullscreen) {
                try {
                    elem.webkitRequestFullscreen();
                    console.log('Requested Webkit fullscreen');
                    setTimeout(hideAllFullscreenPrompts, 500);
                } catch (e) {
                    console.log('Webkit fullscreen failed:', e);
                }
            } else if (elem.msRequestFullscreen) {
                try {
                    elem.msRequestFullscreen();
                    console.log('Requested MS fullscreen');
                    setTimeout(hideAllFullscreenPrompts, 500);
                } catch (e) {
                    console.log('MS fullscreen failed:', e);
                }
            } else {
                console.log('Fullscreen API not supported');
            }
        }

        function hideAllFullscreenPrompts() {
            // Hide large center prompt
            const largePrompt = document.getElementById('fullscreenPrompt');
            if (largePrompt) {
                largePrompt.remove();
            }
            
            // Hide small corner button
            const smallButton = document.getElementById('persistentFullscreenBtn');
            if (smallButton) {
                smallButton.remove();
            }
            
            console.log('Hidden all fullscreen prompts');
        }

        function showPersistentFullscreenButton() {
            const existingButton = document.getElementById('persistentFullscreenBtn');
            if (existingButton) return;
            
            const button = document.createElement('button');
            button.id = 'persistentFullscreenBtn';
            button.innerHTML = '⛶ Enter Fullscreen';
            button.style.cssText = `
                position: fixed;
                top: 20px;
                left: 20px;
                background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
                color: white;
                border: none;
                padding: 16px 24px;
                border-radius: 25px;
                cursor: pointer;
                font-size: 16px;
                font-weight: 600;
                z-index: 1001;
                font-family: inherit;
                transition: opacity 0.3s ease;
                animation: greenBlink 3s infinite;
                box-shadow: 0 4px 15px rgba(40, 167, 69, 0.4);
            `;
            
            button.onclick = () => {
                enterFullscreen();
            };
            
            document.body.appendChild(button);
        }


        function exitFullscreen() {
            console.log('Exit button clicked - going to dashboard');
            
            // Disable fullscreen change listener temporarily
            isExitingViaButton = true;
            
            // Try to exit fullscreen if we're in it, but don't wait for it
            try {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.mozCancelFullScreen) {
                    document.mozCancelFullScreen();
                } else if (document.webkitExitFullscreen) {
                    document.webkitExitFullscreen();
                } else if (document.msExitFullscreen) {
                    document.msExitFullscreen();
                }
            } catch (e) {
                console.log('Error exiting fullscreen:', e);
            }
            
            // Always go to dashboard regardless of fullscreen state
            setTimeout(() => {
                console.log('Redirecting to dashboard...');
                window.location.href = 'dashboard.php';
            }, 100);
        }

        // Alternative simple exit function for testing
        function exitTodashboard() {
            console.log('Alternative exit function called');
            window.location.href = 'dashboard.php';
        }

        function startSlideshow() {
            if (config.slideshow.auto_play) {
                scheduleNextSlide();
                startProgress();
            }
            updatePlayPauseButton();
        }

        function scheduleNextSlide() {
            if (slideInterval) {
                clearInterval(slideInterval);
            }
            slideInterval = setTimeout(() => {
                if (isPlaying) {
                    nextSlide();
                }
            }, config.slideshow.transition_time * 1000);
        }

        function startProgress() {
            if (!progressBar) return; // Skip if progress bar is disabled
            
            if (progressInterval) {
                clearInterval(progressInterval);
            }
            progressStart = Date.now();
            progressBar.style.width = '0%';
            
            progressInterval = setInterval(() => {
                if (isPlaying) {
                    const elapsed = (Date.now() - progressStart) / 1000;
                    const progress = (elapsed / config.slideshow.transition_time) * 100;
                    progressBar.style.width = Math.min(progress, 100) + '%';
                }
            }, 50);
        }

        function nextSlide() {
            const currentSlideEl = slides[currentSlide];
            currentSlide = (currentSlide + 1) % slides.length;
            const nextSlideEl = slides[currentSlide];
            
            showSlide(currentSlideEl, nextSlideEl);
            
            if (isPlaying && config.slideshow.auto_play) {
                scheduleNextSlide();
                startProgress();
            }
            
            renewSession();
        }

        function previousSlide() {
            const currentSlideEl = slides[currentSlide];
            currentSlide = currentSlide === 0 ? slides.length - 1 : currentSlide - 1;
            const prevSlideEl = slides[currentSlide];
            
            showSlide(currentSlideEl, prevSlideEl);
            
            if (isPlaying && config.slideshow.auto_play) {
                scheduleNextSlide();
                startProgress();
            }
            
            renewSession();
        }

        function showSlide(fromSlide, toSlide) {
            // Remove all animation classes
            slides.forEach(slide => {
                slide.classList.remove('active', 'animate-fade', 'animate-slide-left', 'animate-slide-right', 'animate-zoom-in', 'animate-zoom-out');
            });
            
            // Hide current slide
            fromSlide.classList.remove('active');
            
            // Show new slide with animation
            toSlide.classList.add('active');
            
            const effect = config.slideshow.transition_effect;
            switch (effect) {
                case 'slide-left':
                    toSlide.classList.add('animate-slide-left');
                    break;
                case 'slide-right':
                    toSlide.classList.add('animate-slide-right');
                    break;
                case 'zoom-in':
                    toSlide.classList.add('animate-zoom-in');
                    break;
                case 'zoom-out':
                    toSlide.classList.add('animate-zoom-out');
                    break;
                default:
                    toSlide.classList.add('animate-fade');
            }
        }

        function togglePlayPause() {
            isPlaying = !isPlaying;
            updatePlayPauseButton();
            
            if (isPlaying && config.slideshow.auto_play) {
                scheduleNextSlide();
                startProgress();
            } else {
                clearInterval(slideInterval);
                clearInterval(progressInterval);
            }
        }

        function updatePlayPauseButton() {
            playPauseBtn.textContent = isPlaying ? '⏸ Pause' : '▶ Play';
        }

        function showControls() {
            controls.classList.add('visible');
            document.body.style.cursor = 'default';
            
            if (controlsTimeout) {
                clearTimeout(controlsTimeout);
            }
            controlsTimeout = setTimeout(() => {
                hideControls();
            }, 3000);
        }

        function hideControls() {
            controls.classList.remove('visible');
            document.body.style.cursor = 'none';
        }


        function renewSession() {
            fetch('ajax.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=renew_session'
            }).catch(() => {
                // Silently fail - session renewal is not critical for slideshow
            });
        }

        // Event listeners
        document.addEventListener('keydown', function(e) {
            switch(e.key) {
                case 'Escape':
                    exitFullscreen();
                    break;
                case ' ':
                    e.preventDefault();
                    togglePlayPause();
                    showControls();
                    break;
                case 'ArrowLeft':
                    previousSlide();
                    showControls();
                    break;
                case 'ArrowRight':
                    nextSlide();
                    showControls();
                    break;
            }
        });

        document.addEventListener('mousemove', showControls);
        document.addEventListener('click', showControls);

        // Listen for fullscreen changes with different browser prefixes
        document.addEventListener('fullscreenchange', handleFullscreenChange);
        document.addEventListener('webkitfullscreenchange', handleFullscreenChange);
        document.addEventListener('mozfullscreenchange', handleFullscreenChange);
        document.addEventListener('MSFullscreenChange', handleFullscreenChange);

        function handleFullscreenChange() {
            const isFullscreen = !!(document.fullscreenElement || 
                                   document.webkitFullscreenElement || 
                                   document.mozFullScreenElement ||
                                   document.msFullscreenElement);
            
            console.log('Fullscreen change detected. Is fullscreen:', isFullscreen, 'Exiting via button:', isExitingViaButton);
            
            if (isFullscreen) {
                // Entered fullscreen - hide all prompts
                hideAllFullscreenPrompts();
            } else {
                // Exited fullscreen
                if (document.readyState === 'complete' && !isExitingViaButton) {
                    console.log('Exited fullscreen, redirecting to dashboard');
                    
                    // Redirect to dashboard when exiting fullscreen via ESC or F11
                    setTimeout(() => {
                        window.location.href = 'dashboard.php';
                    }, 100);
                }
            }
        }

        // Initial state
        hideControls();
    </script>
</body>
</html>