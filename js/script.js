document.addEventListener("DOMContentLoaded", function() {
    // Obtener el mensaje desde el atributo 'data-mensaje'
    const mensajeElemento = document.getElementById('mensaje');
    const mensaje = mensajeElemento ? mensajeElemento.getAttribute('data-mensaje') : '';

    if (mensaje) {
        alert(mensaje);
    }

    // Lógica para realizar alguna acción al hacer clic en completar o eliminar tarea
    const completarButtons = document.querySelectorAll('.completar-tarea');
    completarButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            const tareaId = this.dataset.id;
            if (confirm("¿Estás seguro de completar esta tarea?")) {
                // Aquí va el formulario para completar la tarea
                this.closest('form').submit();
            }
        });
    });

    const eliminarButtons = document.querySelectorAll('.eliminar-tarea');
    eliminarButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            const tareaId = this.dataset.id;
            if (confirm("¿Estás seguro de eliminar esta tarea?")) {
                // Aquí va el formulario para eliminar la tarea
                this.closest('form').submit();
            }
        });
    });
});
