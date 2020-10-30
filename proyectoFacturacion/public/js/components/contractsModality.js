//Append function to html element
document.getElementById('contractsConditions_Modalidad').addEventListener("change", getCurrentModalidad, false);
document.getElementById('idPaymentUnit').addEventListener("change", getCurrentPaymentUnit, false);

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

function getCurrentPaymentUnit() {
    if (this.value == 5) {
        //Set values
        document.getElementById('contractsConditions_Modalidad').options[1].selected = true;
        document.getElementById('idModule').options[1].selected = true;
        document.getElementById('contractsConditions_Cantidad').value = 1;
        //Set readOnly
        toggleDisableOptions('contractsConditions_Modalidad', 1, true);
        toggleDisableOptions('idModule', 1, true);
        document.getElementById('contractsConditions_Cantidad').readOnly = true;
        //Texto
        document.getElementById('labelPrecio').innerHTML = 'Porcentaje de descuento';
    } else {
        //Set values
        document.getElementById('contractsConditions_Modalidad').options[0].selected = true;
        document.getElementById('idModule').options[0].selected = true;
        document.getElementById('contractsConditions_Cantidad').value = 0;
        //Set readOnly
        toggleDisableOptions('contractsConditions_Modalidad', 0, false);
        toggleDisableOptions('idModule', 0, false);
        document.getElementById('contractsConditions_Cantidad').readOnly = false;
        //Texto
        document.getElementById('labelPrecio').innerHTML = 'Precio';
    }
}

function toggleDisableOptions(idSelectBox, selectedNumber, disabledState) {
    let largo = document.getElementById(idSelectBox).options.length;
    let i;
    for (i = 0; i < largo; i++) {
        if (i != selectedNumber) {
            document.getElementById(idSelectBox).options[i].disabled = disabledState;
        }
    }
}
