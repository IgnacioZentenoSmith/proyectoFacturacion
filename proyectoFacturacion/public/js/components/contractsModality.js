//Append function to html element
document.getElementById('contractsConditions_Modalidad').addEventListener("change", getCurrentModalidad, false);

function getCurrentModalidad() {
    if (this.value == 'Fijo' || this.value == 'Descuento') {
        document.getElementById('contractsConditions_Cantidad').value = 1;
        document.getElementById('contractsConditions_Cantidad').readOnly = true;
        //Cambiar nombre del label del precio a dcto si es dcto
        if (this.value == 'Descuento') {
            document.getElementById('labelPrecio').innerHTML = 'Porcentaje de descuento';
        } else {
            document.getElementById('labelPrecio').innerHTML = 'Precio';
        }
    } else {
        document.getElementById('contractsConditions_Cantidad').readOnly = false;
        document.getElementById('labelPrecio').innerHTML = 'Precio';
    }
}
