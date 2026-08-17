<?php
header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: no-referrer');

// Same-origin only — no Access-Control-Allow-Origin wildcard.

try {
    $db = new PDO('sqlite:' . __DIR__ . '/timekeeping.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ATTR_ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $stmt = $db->query('SELECT id, date, project, hours, notes, created_at FROM entries ORDER BY date DESC, created_at DESC');
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        if (!is_array($data) || !isset($data['date'], $data['project'], $data['hours'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields']);
            exit;
        }

        $date = trim((string) $data['date']);
        $project = trim((string) $data['project']);
        $hours = $data['hours'];
        $notes = isset($data['notes']) ? trim((string) $data['notes']) : '';

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid date format']);
            exit;
        }

        if ($project === '' || strlen($project) > 200) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid project']);
            exit;
        }

        if (!is_numeric($hours)) {
            http_response_code(400);
            echo json_encode(['error' => 'Hours must be a number']);
            exit;
        }

        $hours = (float) $hours;
        if ($hours < 0.25 || $hours > 24) {
            http_response_code(400);
            echo json_encode(['error' => 'Hours must be between 0.25 and 24']);
            exit;
        }

        if (strlen($notes) > 1000) {
            http_response_code(400);
            echo json_encode(['error' => 'Notes too long']);
            exit;
        }

        $stmt = $db->prepare('INSERT INTO entries (date, project, hours, notes) VALUES (?, ?, ?, ?)');
        $stmt->execute([$date, $project, $hours, $notes]);
        echo json_encode(['id' => (int) $db->lastInsertId(), 'success' => true]);
        break;

    case 'DELETE':
        $id = $_GET['id'] ?? null;
        if ($id === null || $id === '' || !ctype_digit((string) $id)) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing or invalid ID']);
            exit;
        }
        $stmt = $db->prepare('DELETE FROM entries WHERE id = ?');
        $stmt->execute([(int) $id]);
        echo json_encode(['success' => true]);
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}
