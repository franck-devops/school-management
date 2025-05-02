<?php
require_once 'db.php';

// Basic sanitization
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Get user role name
function getRoleName($role) {
    $roles = [
        'principal' => 'Principal',
        'bourser' => 'Bourser',
        'vice_principal' => 'Vice Principal',
        'teacher' => 'Teacher',
        'superuser' => 'Super User'
    ];
    return $roles[$role] ?? $role;
}

// Get all classes
function getClasses() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM classes ORDER BY name");
    return $stmt->fetchAll();
}

// Get all subjects
function getSubjects() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM subjects ORDER BY name");
    return $stmt->fetchAll();
}

// Get all staff
function getStaff() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM staff ORDER BY last_name, first_name");
    return $stmt->fetchAll();
}

// Get all students
function getStudents() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM students ORDER BY last_name, first_name");
    return $stmt->fetchAll();
}

// Get system setting
function getSetting($key) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT setting_value FROM system_settings WHERE setting_key = ?");
    $stmt->execute([$key]);
    $result = $stmt->fetch();
    return $result ? $result['setting_value'] : null;
}

// Set system setting
function setSetting($key, $value) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO system_settings (setting_key, setting_value) VALUES (?, ?) 
                          ON DUPLICATE KEY UPDATE setting_value = ?");
    return $stmt->execute([$key, $value, $value]);
}

// Check if system is licensed
function isLicensed() {
    $licensed = getSetting('system_licensed');
    return $licensed === '1' || $licensed === 'true';
}

// Import data from Excel (simplified)
function importFromExcel($file, $table) {
    // This is a placeholder - in production you would use PHPExcel or similar
    // For now, we'll just simulate the import
    return ['success' => true, 'imported' => 10];
}

// Add these functions to the existing functions.php file

function getAllUsers() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM users ORDER BY role, username");
    return $stmt->fetchAll();
}

function getStudentById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function getStaffById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM staff WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function getClassById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM classes WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function getSubjectById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM subjects WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function getStudentsByClass($classId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM students WHERE class_id = ? ORDER BY last_name, first_name");
    $stmt->execute([$classId]);
    return $stmt->fetchAll();
}

function getSubjectsByClass($classId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM subjects WHERE class_id = ? ORDER BY name");
    $stmt->execute([$classId]);
    return $stmt->fetchAll();
}

function getTeacherSubjects($teacherId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM subjects WHERE teacher_id = ? ORDER BY name");
    $stmt->execute([$teacherId]);
    return $stmt->fetchAll();
}

function calculateStudentAverage($studentId, $term = null) {
    global $pdo;
    
    $query = "SELECT AVG(mark) as average FROM marks WHERE student_id = ?";
    $params = [$studentId];
    
    if ($term) {
        $query .= " AND term = ?";
        $params[] = $term;
    }
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $result = $stmt->fetch();
    
    return $result ? round($result['average'], 2) : null;
}

function promoteStudents($currentClassId, $nextClassId) {
    global $pdo;
    
    // Get all students in current class
    $students = getStudentsByClass($currentClassId);
    $promoted = 0;
    
    foreach ($students as $student) {
        $average = calculateStudentAverage($student['id']);
        
        if ($average >= 10) {
            $stmt = $pdo->prepare("UPDATE students SET class_id = ? WHERE id = ?");
            $stmt->execute([$nextClassId, $student['id']]);
            $promoted++;
        }
    }
    
    return $promoted;
}
?>