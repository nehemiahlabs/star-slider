<?php
session_start();

function getVersion() {
    $versionFile = 'version.json';
    if (file_exists($versionFile)) {
        $versionData = json_decode(file_get_contents($versionFile), true);
        return $versionData['version'] ?? '1.0.0';
    }
    return '1.0.0';
}

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

// Renew session
$config['last_activity'] = date('c');
$config['expires_at'] = date('c', time() + 28800); // 8 hours
file_put_contents("$session_dir/config.json", json_encode($config, JSON_PRETTY_PRINT));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - StarSlider</title>
    <link rel="icon" type="image/png" href="assets/favicon.png">
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body class="dashboard-body">
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
    

    <!-- Transparent Header -->
    <header class="transparent-header">
        <div class="header-content">
            <div class="logo-section">
                <img src="screen/assets/logo_pb.png" alt="Nehemiah Labs" class="header-logo">
                <div class="header-greeting">
                    <span id="greetingWord">Welcome</span>, <span class="user-name"><?= htmlspecialchars($config['name']) ?></span>!
                </div>
            </div>
            <div class="header-buttons">
                <button class="btn btn-outline-light me-2" onclick="document.getElementById('imageUpload').click()" title="Upload Images">
                    <i data-feather="upload"></i> Upload
                </button>
                <?php if (!empty($config['images'])): ?>
                <button class="btn btn-play-green me-2" onclick="togglePlayMenu()" title="Play Slideshow" id="playBtn">
                    <i data-feather="play"></i> Play
                </button>
                <?php endif; ?>
                <button class="btn btn-outline-danger" onclick="logout()" title="Logout">
                    <i data-feather="log-out"></i> Logout
                </button>
            </div>
        </div>
    </header>

    <!-- Play Menu Modal -->
    <?php if (!empty($config['images'])): ?>
    <div class="modal fade" id="playMenuModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header border-0 pb-0">
                    <h4 class="modal-title w-100 text-center">
                        <i data-feather="settings" class="me-2"></i>Slideshow Settings
                    </h4>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-3">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="setting-card">
                                <div class="setting-icon">
                                    <i data-feather="clock"></i>
                                </div>
                                <div class="setting-content">
                                    <label for="transitionTime" class="form-label fw-bold">Transition Time</label>
                                    <div class="d-flex align-items-center">
                                        <input type="range" class="form-range flex-grow-1 me-2" id="transitionTime" min="1" max="30" 
                                               value="<?= $config['slideshow']['transition_time'] ?>" 
                                               oninput="updateSetting('transition_time', this.value)">
                                        <span class="time-badge" id="transitionTimeValue"><?= $config['slideshow']['transition_time'] ?>s</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <div class="setting-card">
                                <div class="setting-icon">
                                    <i data-feather="zap"></i>
                                </div>
                                <div class="setting-content">
                                    <label for="transitionEffect" class="form-label fw-bold">Transition Effect</label>
                                    <select class="form-select" id="transitionEffect" onchange="updateSetting('transition_effect', this.value)">
                                        <option value="fade" <?= $config['slideshow']['transition_effect'] === 'fade' ? 'selected' : '' ?>>✨ Fade</option>
                                        <option value="slide-left" <?= $config['slideshow']['transition_effect'] === 'slide-left' ? 'selected' : '' ?>>← Slide Left</option>
                                        <option value="slide-right" <?= $config['slideshow']['transition_effect'] === 'slide-right' ? 'selected' : '' ?>>→ Slide Right</option>
                                        <option value="zoom-in" <?= $config['slideshow']['transition_effect'] === 'zoom-in' ? 'selected' : '' ?>>🔍 Zoom In</option>
                                        <option value="zoom-out" <?= $config['slideshow']['transition_effect'] === 'zoom-out' ? 'selected' : '' ?>>🔎 Zoom Out</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="setting-card">
                                <div class="setting-icon">
                                    <i data-feather="play-circle"></i>
                                </div>
                                <div class="setting-content">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="autoPlay" 
                                               <?= $config['slideshow']['auto_play'] ? 'checked' : '' ?>
                                               onchange="updateSetting('auto_play', this.checked)">
                                        <label class="form-check-label fw-bold" for="autoPlay">Auto-play slideshow</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="setting-card">
                                <div class="setting-icon">
                                    <i data-feather="activity"></i>
                                </div>
                                <div class="setting-content">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="showProgressBar" 
                                               <?= ($config['slideshow']['show_progress_bar'] ?? false) ? 'checked' : '' ?>
                                               onchange="updateSetting('show_progress_bar', this.checked)">
                                        <label class="form-check-label fw-bold" for="showProgressBar">Show progress bar</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-success btn-start-show w-100" onclick="startSlideshow()">
                        <i data-feather="play"></i> Start the Show
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Main Content -->
    <div class="main-content">
        <?php if (empty($config['images'])): ?>
            <div class="empty-state-center">
                <div class="empty-icon">🌟</div>
                <h3>No images yet</h3>
                <p>Upload some images to get started with your slideshow</p>
                <button class="btn btn-success btn-upload-enhanced" onclick="document.getElementById('imageUpload').click()">
                    <i data-feather="upload"></i> Upload Your First Image
                </button>
            </div>
        <?php else: ?>
            <div class="images-grid" id="imagesGrid">
                <?php foreach ($config['images'] as $index => $image): ?>
                    <div class="image-item" data-filename="<?= htmlspecialchars($image['filename']) ?>" data-order="<?= $image['order'] ?>">
                        <div class="image-container">
                            <img src="<?= htmlspecialchars($session_dir . '/images/' . $image['filename']) ?>" 
                                 alt="<?= htmlspecialchars($image['original_name']) ?>" class="img-fluid">
                            <div class="image-overlay">
                                <button class="btn btn-danger btn-sm" onclick="deleteImage('<?= htmlspecialchars($image['filename']) ?>')">
                                    <i data-feather="trash-2"></i>
                                </button>
                            </div>
                        </div>
                        <div class="image-info">
                            <div class="image-name"><?= htmlspecialchars($image['original_name']) ?></div>
                            <div class="image-meta">Order: <?= $image['order'] ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Hidden File Input -->
    <input type="file" id="imageUpload" accept="image/*" multiple style="display: none;">

    <!-- Upload Progress -->
    <div id="uploadProgress" class="upload-progress" style="display: none;">
        <div class="progress-content">
            <h3>Uploading Images...</h3>
            <div class="progress-bar">
                <div class="progress-fill" id="progressFill"></div>
            </div>
            <div class="progress-text" id="progressText">0%</div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer-info dashboard-footer">
        <div>This is StarSlider - A dynamic image slideshow system for professional presentations</div>
        <div>&copy; <?= date('Y') ?> Nehemiah Labs. All rights reserved | Version <?= getVersion() ?></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        let dragSort;
        let playMenuModal;
        
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Feather icons
            feather.replace();
            
            // Initialize Bootstrap modal
            const modalElement = document.getElementById('playMenuModal');
            if (modalElement) {
                playMenuModal = new bootstrap.Modal(modalElement);
            }
            
            // Set initial random greeting word
            const greetingElement = document.getElementById('greetingWord');
            if (greetingElement) {
                greetingElement.textContent = greetingWords[currentWordIndex];
            }
            
            initializeDragAndDrop();
            initializeImageUpload();
        });

        function initializeDragAndDrop() {
            const grid = document.getElementById('imagesGrid');
            if (!grid || grid.querySelector('.empty-state-center')) return;
            
            dragSort = Sortable.create(grid, {
                animation: 150,
                onEnd: function(evt) {
                    updateImageOrder();
                }
            });
        }

        function initializeImageUpload() {
            const uploadInput = document.getElementById('imageUpload');
            uploadInput.addEventListener('change', function(e) {
                if (e.target.files.length > 0) {
                    uploadImages(e.target.files);
                }
            });
        }

        function togglePlayMenu() {
            if (playMenuModal) {
                playMenuModal.show();
            }
        }

        function uploadImages(files) {
            const formData = new FormData();
            formData.append('action', 'upload_image');
            
            for (let i = 0; i < files.length; i++) {
                formData.append('images[]', files[i]);
            }

            const progressDiv = document.getElementById('uploadProgress');
            const progressFill = document.getElementById('progressFill');
            const progressText = document.getElementById('progressText');
            
            progressDiv.style.display = 'block';

            const xhr = new XMLHttpRequest();
            
            xhr.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable) {
                    const percentComplete = (e.loaded / e.total) * 100;
                    progressFill.style.width = percentComplete + '%';
                    progressText.textContent = Math.round(percentComplete) + '%';
                }
            });

            xhr.onload = function() {
                progressDiv.style.display = 'none';
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Upload failed: ' + response.message);
                    }
                } else {
                    alert('Upload failed. Please try again.');
                }
            };

            xhr.onerror = function() {
                progressDiv.style.display = 'none';
                alert('Upload failed. Please try again.');
            };

            xhr.open('POST', 'ajax.php');
            xhr.send(formData);
        }

        function deleteImage(filename) {
            if (!confirm('Are you sure you want to delete this image?')) {
                return;
            }

            fetch('ajax.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=delete_image&filename=' + encodeURIComponent(filename)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Failed to delete image: ' + data.message);
                }
            })
            .catch(error => {
                alert('Failed to delete image. Please try again.');
            });
        }

        function updateImageOrder() {
            const items = document.querySelectorAll('.image-item');
            const order = Array.from(items).map((item, index) => ({
                filename: item.getAttribute('data-filename'),
                order: index + 1
            }));

            // Update display immediately
            items.forEach((item, index) => {
                const orderElement = item.querySelector('.image-meta');
                if (orderElement) {
                    orderElement.textContent = `Order: ${index + 1}`;
                }
                item.setAttribute('data-order', index + 1);
            });

            fetch('ajax.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'reorder_images',
                    order: order
                })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    alert('Failed to update image order: ' + data.message);
                    location.reload();
                }
            })
            .catch(error => {
                alert('Failed to update image order. Please try again.');
                location.reload();
            });
        }

        function updateSetting(key, value) {
            fetch('ajax.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=update_settings&key=${key}&value=${encodeURIComponent(value)}`
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    alert('Failed to update setting: ' + data.message);
                }
                
                if (key === 'transition_time') {
                    document.getElementById('transitionTimeValue').textContent = value + 's';
                }
            })
            .catch(error => {
                alert('Failed to update setting. Please try again.');
            });
        }

        function startSlideshow() {
            // Close modal and start slideshow
            if (playMenuModal) {
                playMenuModal.hide();
            }
            // Store fullscreen request in sessionStorage
            sessionStorage.setItem('autoFullscreen', 'true');
            window.location.href = 'slideshow.php';
        }

        function logout() {
            if (confirm('Are you sure you want to logout? This will end your current session.')) {
                fetch('ajax.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=destroy_session'
                })
                .then(() => {
                    window.location.href = 'index.php';
                });
            }
        }

        // Re-initialize Feather icons after dynamic content changes
        function refreshFeatherIcons() {
            feather.replace();
        }

        // Random greeting words rotation
        const greetingWords = ['Welcome', 'Hello', 'Good job', 'Good work', 'Good show', 'Great work', 'Nice job', 'Well done', 'Awesome', 'Fantastic', 'Excellent', 'Outstanding', 'Brilliant', 'Amazing', 'Superb'];
        let currentWordIndex = Math.floor(Math.random() * greetingWords.length);

        function rapidWordChange(changeCount = 0) {
            const greetingElement = document.getElementById('greetingWord');
            if (!greetingElement) return;
            
            if (changeCount < 5) {
                // Add jump effect
                greetingElement.classList.add('jump');
                
                // Change word after a short delay
                setTimeout(() => {
                    currentWordIndex = (currentWordIndex + 1) % greetingWords.length;
                    greetingElement.textContent = greetingWords[currentWordIndex];
                }, 200);
                
                // Remove jump class and schedule next change
                setTimeout(() => {
                    greetingElement.classList.remove('jump');
                    rapidWordChange(changeCount + 1);
                }, 500); // 0.5 second each change
            }
        }

        function startGreetingCycle() {
            rapidWordChange(0); // Start 5 rapid changes
            setTimeout(startGreetingCycle, 15000); // Repeat every 15 seconds
        }

        // Start the first cycle after page load
        setTimeout(startGreetingCycle, 2000); // Wait 2 seconds after page load
    </script>
</body>
</html>
