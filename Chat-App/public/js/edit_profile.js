function goBack() {
    window.history.back();
}

function enableEditMode() {
    // Hide display fields and show edit fields
    document.querySelectorAll('.display-field').forEach(field => {
        field.style.display = 'none';
    });
    document.querySelectorAll('.edit-field').forEach(field => {
        field.style.display = 'table-row'; // Use table-row for table elements
    });
    document.querySelector('.form-actions').style.display = 'flex'; // Show save/cancel buttons
    document.getElementById('editButton').style.display = 'none'; // Hide edit button
    document.querySelector('.edit-img-btn').style.display = 'block'; // Show pencil icon for image
}

function cancelEdit() {
    // Show display fields and hide edit fields
    document.querySelectorAll('.display-field').forEach(field => {
        field.style.display = 'block';
    });
    document.querySelectorAll('.edit-field').forEach(field => {
        field.style.display = 'none';
    });
    document.querySelector('.form-actions').style.display = 'none'; // Hide save/cancel buttons
    document.getElementById('editButton').style.display = 'flex'; // Show edit button
    document.querySelector('.edit-img-btn').style.display = 'none'; // Hide pencil icon for image
}

function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function() {
        const output = document.getElementById('profileImg');
        output.src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
}

setTimeout(() => {
    const msg = document.getElementById('flashMessage');
    if (msg) {
      msg.style.display = 'none';
    }
  }, 2000);