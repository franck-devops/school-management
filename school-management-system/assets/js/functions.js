// Utility functions for the school management system

function formatDate(dateString) {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
}

function formatDateTime(dateString) {
    const options = { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    };
    return new Date(dateString).toLocaleDateString(undefined, options);
}

function showLoading(selector = 'body') {
    $(selector).append('<div class="loading-overlay"><div class="spinner"></div></div>');
}

function hideLoading() {
    $('.loading-overlay').remove();
}

function showAlert(message, type = 'success', selector = 'body') {
    const alertClass = type === 'error' ? 'alert-error' : 'alert-success';
    $(selector).prepend('<div class="alert ' + alertClass + '">' + message + '</div>');
    setTimeout(() => $('.alert').fadeOut(), 5000);
}

function fetchClassStudents(classId, callback) {
    $.getJSON('includes/api.php?action=get_class_students&class_id=' + classId, callback);
}

function fetchSubjectTeachers(subjectId, callback) {
    $.getJSON('includes/api.php?action=get_subject_teachers&subject_id=' + subjectId, callback);
}

function fetchStudentMarks(studentId, term = null, callback) {
    let url = 'includes/api.php?action=get_student_marks&student_id=' + studentId;
    if (term) url += '&term=' + term;
    $.getJSON(url, callback);
}

function exportToExcel(tableId, fileName = 'export') {
    const table = document.getElementById(tableId);
    const html = table.outerHTML;
    const url = 'data:application/vnd.ms-excel,' + encodeURIComponent(html);
    const link = document.createElement('a');
    link.download = fileName + '.xls';
    link.href = url;
    link.click();
}