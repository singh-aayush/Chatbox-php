function loadContacts() {
    const xhr = new XMLHttpRequest();
    xhr.open('GET', 'contact-list.php?ajax=1', true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            document.getElementById('content-scrollable').innerHTML = xhr.responseText;
            // Update the page title
            document.title = 'Contacts';
            // Update the header title
            document.querySelector('.layout-header h1').textContent = 'Contacts';
            // Update active page styling
            document.querySelectorAll('.nav-item').forEach(item => item.classList.remove('active'));
            document.querySelector('.create-group-btn .nav-item').classList.add('active');
        } else {
            document.getElementById('content-scrollable').innerHTML = '<p>Error loading contacts: Status ' + xhr.status + '</p>';
        }
    };
    xhr.onerror = function() {
        document.getElementById('content-scrollable').innerHTML = '<p>Error loading contacts: Network error</p>';
    };
    xhr.send();
}

function updateActiveNav(activePage) {
    // Remove active class from all nav items
    document.querySelectorAll('.nav-item').forEach(item => {
        item.classList.remove('active');
    });
    // Add active class to the clicked nav item
    document.querySelector(`.nav-item[aria-label="${activePage}"]`)?.classList.add('active');
}

function goBack() {
    window.history.back();
}