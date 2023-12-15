function handleSubmit(event) {
    event.preventDefault();

    // Get form values
    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    const message = document.getElementById('message').value;

    // Create JSON object
    const formData = {
        name: name,
        email: email,
        message: message
    };

    // Convert JSON to string
    const jsonFormData = JSON.stringify(formData);

    // Simulate submitting to server (you would replace this with actual server interaction)
    // Display JSON data on the submitted page
    window.location.href = `submitted.html?data=${encodeURIComponent(jsonFormData)}`;
}

document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const jsonData = urlParams.get('data');
    const jsonDataElement = document.getElementById('json-data');

    if (jsonData) {
        jsonDataElement.textContent = jsonData;
    }
});
