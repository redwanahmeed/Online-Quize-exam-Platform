// AJAX helper function
function ajaxRequest(url, method, callback, data = null) {
    var xhr = new XMLHttpRequest();
    xhr.open(method, url, true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    
    if (data && method === 'POST') {
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    }
    
    xhr.onload = function() {
        if (xhr.status === 200) {
            var response = JSON.parse(xhr.responseText);
            callback(response);
        }
    };
    
    xhr.send(data);
}

// Confirm delete
function confirmDelete(message) {
    return confirm(message || 'Are you sure you want to delete this item?');
}

// Auto-hide flash messages
setTimeout(function() {
    var flashMessages = document.querySelectorAll('.success, .error');
    flashMessages.forEach(function(msg) {
        msg.style.display = 'none';
    });
}, 3000);