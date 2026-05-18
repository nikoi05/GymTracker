<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../BL/exerciseManage.php';

if (!isset($_SESSION['userID'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$em = new ExerciseManage();
$action = $_POST['action'] ?? '';
$actorUserId = (int)$_SESSION['userID'];

function cleanText($v) {
    return trim((string)($v ?? ''));
}

if ($action === 'getSteps') {
    $exerciseID = (int)($_POST['exerciseID'] ?? 0);
    if ($exerciseID <= 0) { echo json_encode([]); exit; }
    echo json_encode($em->getStepsByExerciseId($exerciseID));
    exit;
}

if ($action === 'add' || $action === 'edit') {
    $payload = [
        'name' => cleanText($_POST['name'] ?? ''),
        'icon' => cleanText($_POST['icon'] ?? ''),
        'muscle' => cleanText($_POST['muscle'] ?? ''),
        'typeID' => (int)($_POST['typeID'] ?? 0),
        'difficulty' => cleanText($_POST['difficulty'] ?? ''),
        'location' => cleanText($_POST['location'] ?? ''),
        'equipment' => cleanText($_POST['equipment'] ?? ''),
        'desc' => cleanText($_POST['desc'] ?? ''),
        'tags' => cleanText($_POST['tags'] ?? ''),
        'steps' => json_decode((string)($_POST['steps'] ?? '[]'), true),
    ];

    // Optional icon upload (multipart)
    if (isset($_FILES['icon_file']) && is_array($_FILES['icon_file'])) {
        $file = $_FILES['icon_file'];
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK && !empty($file['tmp_name'])) {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($file['tmp_name']);

            $allowed = [
                'image/png'  => 'png',
                'image/jpeg' => 'jpg',
                'image/gif'  => 'gif',
                'image/webp' => 'webp',
            ];

            if (!isset($allowed[$mime])) {
                echo json_encode(['error' => 'Invalid icon file type.']);
                exit;
            }

            $uploadDir = __DIR__ . '/../assets/images';
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0775, true)) {
                    echo json_encode(['error' => 'Could not create upload directory.']);
                    exit;
                }
            }

            $originalName = (string)($file['name'] ?? 'icon');
            $originalBase = preg_replace('/[^a-zA-Z0-9_\-]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
            if ($originalBase === '') $originalBase = 'icon';

            $ext = $allowed[$mime];
            $finalName = $originalBase . '_' . time() . '.' . $ext;
            $destPath = $uploadDir . '/' . $finalName;

            if (!move_uploaded_file($file['tmp_name'], $destPath)) {
                echo json_encode(['error' => 'Failed to move uploaded icon.']);
                exit;
            }

            // store relative path in DB
            $payload['icon'] = 'assets/images/' . $finalName;
        }
    }



    if ($payload['name'] === '' || empty($payload['steps']) || !is_array($payload['steps'])) {
        echo json_encode(['error' => 'Name and at least one step are required.']);
        exit;
    }

    $payload['steps'] = array_values(array_filter(array_map('trim', $payload['steps']), function($s){ return $s !== ''; }));
    if (count($payload['steps']) === 0) {
        echo json_encode(['error' => 'At least one valid step is required.']);
        exit;
    }

    if ($action === 'add') {
        $res = $em->createExerciseWithLogs($payload, $actorUserId);
    } else {
        $exerciseID = (int)($_POST['id'] ?? 0);
        if ($exerciseID <= 0) { echo json_encode(['error' => 'Invalid exercise id']); exit; }
        $res = $em->updateExerciseWithLogs($exerciseID, $payload, $actorUserId);
    }

    echo json_encode($res['ok'] ? ['success' => true] : ['error' => ($res['error'] ?? 'Operation failed')]);
    exit;
}

if ($action === 'delete') {
    $exerciseID = (int)($_POST['id'] ?? 0);
    if ($exerciseID <= 0) { echo json_encode(['error' => 'Invalid exercise id']); exit; }
    $res = $em->deleteExerciseWithLogs($exerciseID, $actorUserId);
    echo json_encode($res['ok'] ? ['success' => true] : ['error' => ($res['error'] ?? 'Delete failed')]);
    exit;
}

echo json_encode(['error' => 'Unknown action']);
exit;
