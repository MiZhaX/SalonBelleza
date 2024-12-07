document.addEventListener('DOMContentLoaded', function () {
    const servicioSelect = document.getElementById('id_servicio');
    const empleadoSelect = document.getElementById('id_empleado');

    servicioSelect.addEventListener('change', function () {
        const idServicio = this.value;

        if (idServicio) {
            fetch(BASE_URL + 'Empleado/obtenerEmpleadosPorEspecialidad?id_servicio=' + idServicio)
                .then(response => response.json())
                .then(data => {
                    // Limpiar el select de empleados
                    empleadoSelect.innerHTML = '<option value="">Selecciona un empleado</option>';

                    // Habilitar el select
                    empleadoSelect.disabled = false;

                    // Rellenar el select con los empleados obtenidos
                    data.forEach(empleado => {
                        const option = document.createElement('option');
                        option.value = empleado.id;
                        option.textContent = empleado.nombre;
                        empleadoSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error al obtener los empleados:', error);
                    empleadoSelect.innerHTML = '<option value="">Error al cargar empleados</option>';
                    empleadoSelect.disabled = true;
                });
        } else {
            // Si no hay servicio seleccionado, deshabilitar el select de empleados
            empleadoSelect.innerHTML = '<option value="">Selecciona un servicio primero</option>';
            empleadoSelect.disabled = true;
        }
    });
});
