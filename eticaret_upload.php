<?php
/**
 * E-Ticaret Image Upload API
 * 
 * This PHP backend handles image uploads to the e-commerce website hosting.
 * Images are stored in: assets/uploads/product_photos/
 * 
 * Actions:
 * - test_connection: Tests if the API is reachable and writable
 * - upload_image: Uploads a base64 encoded image to the server
 * - delete_image: Deletes an image from the server
 */

// CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Sadece POST istekleri kabul edilir.']);
    exit;
}

// Read JSON body
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Geçersiz JSON verisi.']);
    exit;
}

// Validate API secret
$api_secret = $input['api_secret'] ?? '';
$config_secret = getenv('ETICARET_FTP_API_SECRET');

// If env variable not set, check a config file
if (!$config_secret) {
    $config_file = __DIR__ . '/eticaret_config.php';
    if (file_exists($config_file)) {
        include $config_file;
        $config_secret = defined('ETICARET_FTP_API_SECRET') ? ETICARET_FTP_API_SECRET : '';
    }
}

if (empty($config_secret) || $api_secret !== $config_secret) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Geçersiz API anahtarı.']);
    exit;
}

$action = $input['action'] ?? '';
$upload_dir = __DIR__ . '/assets/uploads/product_photos/';

switch ($action) {
    case 'test_connection':
        handleTestConnection($upload_dir);
        break;

    case 'upload_image':
        handleUploadImage($input, $upload_dir);
        break;

    case 'delete_image':
        handleDeleteImage($input, $upload_dir);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Geçersiz action: ' . $action]);
        break;
}

/**
 * Test connection - checks if directory exists and is writable
 */
function handleTestConnection($upload_dir) {
    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0755, true)) {
            echo json_encode([
                'success' => false,
                'message' => 'Yükleme dizini oluşturulamadı: ' . $upload_dir
            ]);
            return;
        }
    }

    if (!is_writable($upload_dir)) {
        echo json_encode([
            'success' => false,
            'message' => 'Yükleme dizini yazılabilir değil: ' . $upload_dir
        ]);
        return;
    }

    echo json_encode([
        'success' => true,
        'message' => 'Bağlantı başarılı! Sunucu erişilebilir ve yükleme dizini hazır.',
        'upload_path' => 'assets/uploads/product_photos/'
    ]);
}

/**
 * Upload image from base64 data
 */
function handleUploadImage($input, $upload_dir) {
    $image_data = $input['image_data'] ?? '';
    $file_name = $input['file_name'] ?? '';

    if (empty($image_data) || empty($file_name)) {
        echo json_encode(['success' => false, 'message' => 'image_data ve file_name gereklidir.']);
        return;
    }

    // Sanitize filename
    $file_name = preg_replace('/[^a-zA-Z0-9._-]/', '_', $file_name);
    
    // Validate extension
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_extensions)) {
        echo json_encode(['success' => false, 'message' => 'Desteklenmeyen dosya formatı: ' . $ext]);
        return;
    }

    // Create directory if needed
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Decode base64 image
    $decoded = base64_decode($image_data);
    if ($decoded === false) {
        echo json_encode(['success' => false, 'message' => 'Base64 decode hatası.']);
        return;
    }

    // Validate image data
    $tmp_file = tempnam(sys_get_temp_dir(), 'img_');
    file_put_contents($tmp_file, $decoded);
    $image_info = getimagesize($tmp_file);
    unlink($tmp_file);

    if ($image_info === false) {
        echo json_encode(['success' => false, 'message' => 'Geçersiz resim verisi.']);
        return;
    }

    // Write file
    $file_path = $upload_dir . $file_name;
    $result = file_put_contents($file_path, $decoded);

    if ($result === false) {
        echo json_encode(['success' => false, 'message' => 'Dosya yazma hatası.']);
        return;
    }

    // Build public URL
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $public_url = $protocol . '://' . $host . '/assets/uploads/product_photos/' . $file_name;

    echo json_encode([
        'success' => true,
        'message' => 'Resim başarıyla yüklendi: ' . $file_name,
        'file_name' => $file_name,
        'file_size' => $result,
        'image_url' => $public_url,
        'upload_path' => 'assets/uploads/product_photos/' . $file_name
    ]);
}

/**
 * Delete image from server
 */
function handleDeleteImage($input, $upload_dir) {
    $file_name = $input['file_name'] ?? '';

    if (empty($file_name)) {
        echo json_encode(['success' => false, 'message' => 'file_name gereklidir.']);
        return;
    }

    // Sanitize filename (prevent directory traversal)
    $file_name = basename($file_name);
    $file_name = preg_replace('/[^a-zA-Z0-9._-]/', '_', $file_name);

    $file_path = $upload_dir . $file_name;

    if (!file_exists($file_path)) {
        echo json_encode(['success' => false, 'message' => 'Dosya bulunamadı: ' . $file_name]);
        return;
    }

    if (unlink($file_path)) {
        echo json_encode([
            'success' => true,
            'message' => 'Dosya başarıyla silindi: ' . $file_name
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Dosya silinemedi: ' . $file_name]);
    }
}
?>
