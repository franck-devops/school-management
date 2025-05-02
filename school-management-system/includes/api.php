<?php
require_once 'config.php';
require_once 'db.php';
require_once 'auth.php';
require_once 'functions.php';

header('Content-Type: application/json');

// Check if API request
if (!isset($_GET['action'])) {
    echo json_encode(['success' => false, 'message' => 'No action specified']);
    exit();
}

$action = $_GET['action'];
$response = ['success' => false, 'message' => 'Invalid action'];

try {
    switch ($action) {
        case 'get_class_students':
            if (!isset($_GET['class_id'])) {
                throw new Exception('Class ID not provided');
            }
            
            $classId = (int)$_GET['class_id'];
            $stmt = $pdo->prepare("SELECT * FROM students WHERE class_id = ? ORDER BY last_name, first_name");
            $stmt->execute([$classId]);
            $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $response = ['success' => true, 'data' => $students];
            break;
            
        case 'get_subject_teachers':
            if (!isset($_GET['subject_id'])) {
                throw new Exception('Subject ID not provided');
            }
            
            $subjectId = (int)$_GET['subject_id'];
            $stmt = $pdo->prepare("SELECT s.* FROM staff s 
                                  JOIN subjects sub ON s.id = sub.teacher_id 
                                  WHERE sub.id = ?");
            $stmt->execute([$subjectId]);
            $teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $response = ['success' => true, 'data' => $teachers];
            break;
            
        case 'get_student_marks':
            if (!isset($_GET['student_id'])) {
                throw new Exception('Student ID not provided');
            }
            
            $studentId = (int)$_GET['student_id'];
            $term = isset($_GET['term']) ? $_GET['term'] : null;
            
            $query = "SELECT m.*, s.name as subject_name FROM marks m 
                     JOIN subjects s ON m.subject_id = s.id 
                     WHERE m.student_id = ?";
            $params = [$studentId];
            
            if ($term) {
                $query .= " AND m.term = ?";
                $params[] = $term;
            }
            
            $query .= " ORDER BY m.term, m.sequence";
            
            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            $marks = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $response = ['success' => true, 'data' => $marks];
            break;
            
        case 'verify_payment':
            // This would integrate with the mobile money API in production
            if (!isset($_GET['transaction_id'])) {
                throw new Exception('Transaction ID not provided');
            }
            
            // Simulate API response
            $response = [
                'success' => true,
                'verified' => true,
                'amount' => 200000,
                'currency' => 'XAF',
                'timestamp' => date('Y-m-d H:i:s')
            ];
            break;
            
        default:
            $response['message'] = 'Unknown action';
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>