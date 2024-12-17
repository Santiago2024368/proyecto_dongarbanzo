document.getElementById('newsletterForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = e.target;
    const formStatus = document.getElementById('formStatus');
    const submitButton = form.querySelector('button[type="submit"]');
    
    // Deshabilitar botón durante el envío
    submitButton.disabled = true;
    submitButton.classList.add('opacity-50', 'cursor-not-allowed');
    
    // Enviar formulario
    fetch(form.action, {
        method: 'POST',
        body: new FormData(form),
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => {
        // Crear un mensaje de éxito más moderno
        formStatus.innerHTML = `
            <div class="modern-success-message">
                <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                    <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
                    <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
                </svg>
                <h3>¡Gracias por tu opinión!</h3>
                <p>Tomaremos en cuenta tu mensaje.</p>
            </div>
        `;
        
        formStatus.classList.remove('hidden');
        formStatus.classList.add('modern-success-container');
        
        // Resetear formulario
        form.reset();
        
        // Ocultar mensaje después de 3 segundos
        setTimeout(() => {
            formStatus.classList.add('hidden');
            formStatus.classList.remove('modern-success-container');
            submitButton.disabled = false;
            submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
        }, 3000);
    })
    .catch(error => {
        // Mostrar mensaje de error
        formStatus.textContent = 'Hubo un problema al enviar tu mensaje. Por favor, inténtalo de nuevo.';
        formStatus.classList.remove('hidden');
        formStatus.classList.add('error-message');
        
        // Habilitar botón nuevamente
        submitButton.disabled = false;
        submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
        
        // Ocultar mensaje de error después de 3 segundos
        setTimeout(() => {
            formStatus.classList.add('hidden');
            formStatus.classList.remove('error-message');
        }, 3000);
    });
});