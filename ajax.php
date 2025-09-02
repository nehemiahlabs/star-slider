<?php
session_start();
header('Content-Type: application/json');

function sendResponse($success, $message = '', $data = null) {
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

function validateSession() {
    if (!isset($_SESSION['session_id'])) {
        sendResponse(false, 'No active session');
    }
    
    $session_id = $_SESSION['session_id'];
    $session_dir = "sessions/$session_id";
    
    if (!is_dir($session_dir) || !file_exists("$session_dir/config.json")) {
        session_destroy();
        sendResponse(false, 'Invalid session');
    }
    
    $config = json_decode(file_get_contents("$session_dir/config.json"), true);
    
    if (!$config || strtotime($config['expires_at']) <= time()) {
        session_destroy();
        sendResponse(false, 'Session expired');
    }
    
    return [$session_id, $session_dir, $config];
}

function sanitizeFilename($filename) {
    $filename = preg_replace('/[^a-zA-Z0-9\._-]/', '_', $filename);
    $filename = preg_replace('/_{2,}/', '_', $filename);
    return trim($filename, '_');
}

function isValidImageType($file) {
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file);
    finfo_close($finfo);
    
    return in_array($mimeType, $allowedTypes);
}

// Check for JSON input
$input = json_decode(file_get_contents('php://input'), true);
$action = null;

if (isset($_POST['action'])) {
    $action = $_POST['action'];
} elseif (isset($_GET['action'])) {
    $action = $_GET['action'];
} elseif ($input && isset($input['action'])) {
    $action = $input['action'];
}

if (!$action) {
    sendResponse(false, 'No action specified');
}

switch ($action) {
    case 'upload_image':
        list($session_id, $session_dir, $config) = validateSession();
        
        if (!isset($_FILES['images']) || !is_array($_FILES['images']['tmp_name'])) {
            sendResponse(false, 'No images uploaded');
        }
        
        $uploadedImages = [];
        $maxOrder = 0;
        
        foreach ($config['images'] as $img) {
            $maxOrder = max($maxOrder, $img['order']);
        }
        
        for ($i = 0; $i < count($_FILES['images']['tmp_name']); $i++) {
            if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) {
                continue;
            }
            
            $tmpPath = $_FILES['images']['tmp_name'][$i];
            $originalName = $_FILES['images']['name'][$i];
            $fileSize = $_FILES['images']['size'][$i];
            
            // Validate file size (max 10MB)
            if ($fileSize > 10 * 1024 * 1024) {
                sendResponse(false, "File '$originalName' is too large (max 10MB)");
            }
            
            // Validate file type
            if (!isValidImageType($tmpPath)) {
                sendResponse(false, "File '$originalName' is not a valid image type");
            }
            
            // Generate unique filename
            $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
            $filename = uniqid('img_', true) . '.' . $extension;
            $filename = sanitizeFilename($filename);
            
            $destPath = "$session_dir/images/$filename";
            
            if (!move_uploaded_file($tmpPath, $destPath)) {
                sendResponse(false, "Failed to save '$originalName'");
            }
            
            // Add to config
            $maxOrder++;
            $config['images'][] = [
                'filename' => $filename,
                'original_name' => $originalName,
                'upload_time' => date('c'),
                'order' => $maxOrder
            ];
            
            $uploadedImages[] = $filename;
        }
        
        if (empty($uploadedImages)) {
            sendResponse(false, 'No valid images were uploaded');
        }
        
        // Update config
        $config['last_activity'] = date('c');
        file_put_contents("$session_dir/config.json", json_encode($config, JSON_PRETTY_PRINT));
        
        sendResponse(true, count($uploadedImages) . ' image(s) uploaded successfully', $uploadedImages);
        break;
        
    case 'delete_image':
        list($session_id, $session_dir, $config) = validateSession();
        
        if (!isset($_POST['filename'])) {
            sendResponse(false, 'No filename specified');
        }
        
        $filename = $_POST['filename'];
        $imageIndex = -1;
        
        foreach ($config['images'] as $index => $image) {
            if ($image['filename'] === $filename) {
                $imageIndex = $index;
                break;
            }
        }
        
        if ($imageIndex === -1) {
            sendResponse(false, 'Image not found');
        }
        
        // Delete file
        $imagePath = "$session_dir/images/$filename";
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
        
        // Remove from config
        array_splice($config['images'], $imageIndex, 1);
        
        // Reorder remaining images
        foreach ($config['images'] as $index => $image) {
            $config['images'][$index]['order'] = $index + 1;
        }
        
        $config['last_activity'] = date('c');
        file_put_contents("$session_dir/config.json", json_encode($config, JSON_PRETTY_PRINT));
        
        sendResponse(true, 'Image deleted successfully');
        break;
        
    case 'reorder_images':
        list($session_id, $session_dir, $config) = validateSession();
        
        if (!isset($input['order']) || !is_array($input['order'])) {
            sendResponse(false, 'Invalid order data');
        }
        
        $newOrder = $input['order'];
        $updatedImages = [];
        
        foreach ($newOrder as $item) {
            if (!isset($item['filename']) || !isset($item['order'])) {
                continue;
            }
            
            foreach ($config['images'] as $image) {
                if ($image['filename'] === $item['filename']) {
                    $image['order'] = $item['order'];
                    $updatedImages[] = $image;
                    break;
                }
            }
        }
        
        $config['images'] = $updatedImages;
        $config['last_activity'] = date('c');
        file_put_contents("$session_dir/config.json", json_encode($config, JSON_PRETTY_PRINT));
        
        sendResponse(true, 'Image order updated successfully');
        break;
        
    case 'update_settings':
        list($session_id, $session_dir, $config) = validateSession();
        
        if (!isset($_POST['key']) || !isset($_POST['value'])) {
            sendResponse(false, 'Missing setting parameters');
        }
        
        $key = $_POST['key'];
        $value = $_POST['value'];
        
        // Validate setting keys
        $validSettings = [
            'transition_time' => 'int',
            'transition_effect' => 'string',
            'auto_play' => 'bool',
            'show_progress_bar' => 'bool'
        ];
        
        if (!isset($validSettings[$key])) {
            sendResponse(false, 'Invalid setting key');
        }
        
        // Convert and validate value
        switch ($validSettings[$key]) {
            case 'int':
                $value = (int)$value;
                if ($key === 'transition_time' && ($value < 1 || $value > 30)) {
                    sendResponse(false, 'Transition time must be between 1 and 30 seconds');
                }
                break;
            case 'bool':
                $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                break;
            case 'string':
                if ($key === 'transition_effect') {
                    $validEffects = ['fade', 'slide-left', 'slide-right', 'zoom-in', 'zoom-out'];
                    if (!in_array($value, $validEffects)) {
                        sendResponse(false, 'Invalid transition effect');
                    }
                }
                break;
        }
        
        $config['slideshow'][$key] = $value;
        $config['last_activity'] = date('c');
        file_put_contents("$session_dir/config.json", json_encode($config, JSON_PRETTY_PRINT));
        
        sendResponse(true, 'Setting updated successfully');
        break;
        
    case 'get_settings':
        list($session_id, $session_dir, $config) = validateSession();
        sendResponse(true, 'Settings retrieved successfully', $config['slideshow']);
        break;
        
    case 'get_images':
        list($session_id, $session_dir, $config) = validateSession();
        sendResponse(true, 'Images retrieved successfully', $config['images']);
        break;
        
    case 'renew_session':
        list($session_id, $session_dir, $config) = validateSession();
        
        $config['last_activity'] = date('c');
        $config['expires_at'] = date('c', time() + 28800); // Extend for 8 hours
        file_put_contents("$session_dir/config.json", json_encode($config, JSON_PRETTY_PRINT));
        
        sendResponse(true, 'Session renewed successfully');
        break;
        
    case 'check_session':
        try {
            list($session_id, $session_dir, $config) = validateSession();
            sendResponse(true, 'Session is valid', [
                'session_id' => $session_id,
                'name' => $config['name'],
                'expires_at' => $config['expires_at']
            ]);
        } catch (Exception $e) {
            sendResponse(false, 'Session is invalid');
        }
        break;
        
    case 'destroy_session':
        if (isset($_SESSION['session_id'])) {
            $session_id = $_SESSION['session_id'];
            $session_dir = "sessions/$session_id";
            
            // Clean up session files
            if (is_dir($session_dir)) {
                // Remove images
                $imagesDir = "$session_dir/images";
                if (is_dir($imagesDir)) {
                    $files = glob("$imagesDir/*");
                    foreach ($files as $file) {
                        if (is_file($file)) {
                            unlink($file);
                        }
                    }
                    rmdir($imagesDir);
                }
                
                // Remove config
                if (file_exists("$session_dir/config.json")) {
                    unlink("$session_dir/config.json");
                }
                
                rmdir($session_dir);
            }
            
            session_destroy();
        }
        
        sendResponse(true, 'Session destroyed successfully');
        break;
        
    default:
        sendResponse(false, 'Unknown action');
}
?>