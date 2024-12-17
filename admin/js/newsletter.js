document.getElementById('newsletterForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = e.target;
    const formData = new FormData(form);
    const statusDiv = document.getElementById('formStatus');

    // Validar email
    const email = formData.get('email');
    const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    
    if (!emailRegex.test(email)) {
        statusDiv.innerHTML = 'Por favor, ingresa un correo electrónico válido.';
        statusDiv.style.color = 'red';
        return;
    }

    // Enviar datos del formulario
    fetch('php/send-email.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            statusDiv.innerHTML = '¡Gracias por suscribirte a nuestro boletín!';
            statusDiv.style.color = 'green';
            form.reset(); // Limpiar formulario
        } else {
            statusDiv.innerHTML = 'Hubo un error. Por favor, intenta nuevamente.';
            statusDiv.style.color = 'red';
        }
    })
    .catch(error => {
        statusDiv.innerHTML = 'Error de conexión. Verifica tu internet.';
        statusDiv.style.color = 'red';
        console.error('Error:', error);
    });
});