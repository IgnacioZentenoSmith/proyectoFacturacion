//Inicializa la tabla "detalles" del dashboard
$('#tablaTributaryDetails').bootstrapTable({
    pageSize: 100,
    exportDataType: 'all',
});

$('#tablaPaymentDetails').bootstrapTable({
    pageSize: 100,
    exportDataType: 'all',
});

const tributaryDetailsTableLength = $('#tablaTributaryDetails').bootstrapTable('getData').length;
document.getElementById('tributaryDetailsTableLength').value = tributaryDetailsTableLength;

const contractPaymentDetailsTableLength = $('#contractPaymentDetailsTableLength').bootstrapTable('getData').length;
document.getElementById('contractPaymentDetailsTableLength').value = contractPaymentDetailsTableLength;

function appendFunctions() {
    //tributarydetails_paymentTotalValue[] getMontoTotal(this)
    //tributarydetails_discount[] getDiscount(this)
    //tributarydetails_paymentValue[] getValue(this)
    //tributarydetails_paymentPercentage[] getPercentage(this)

    document.getElementsByName('tributarydetails_paymentPercentage[]').forEach(input => {
        document.getElementById(input.id).addEventListener("change", getPercentage, false);
    });
    document.getElementsByName('tributarydetails_paymentValue[]').forEach(input => {
        document.getElementById(input.id).addEventListener("change", getValue, false);
    });
    document.getElementsByName('tributarydetails_discount[]').forEach(input => {
        document.getElementById(input.id).addEventListener("change", getDiscount, false);
    });
    document.getElementsByName('tributarydetails_paymentTotalValue[]').forEach(input => {
        document.getElementById(input.id).addEventListener("change", getMontoTotal, false);
    });

    document.getElementById('montoTotal').addEventListener("change", reDistributeMontoTotal, false);

}
appendFunctions();

//this -> input
function getPercentage() {
    //Entradas no validas
    if (parseFloat(this.value) > 100) {
        this.value = parseFloat(0);
    }
    else if (parseFloat(this.value) < 0) {
        this.value = parseFloat(0);
    }
    //Entradas validas
    else {
        let inputId = getInputId(this);
        //Input del valor asociado al porcentaje
        let valueInput = document.getElementById('tributarydetails_paymentValue[' + inputId + ']');
        let montoTotalInput = document.getElementById('tributarydetails_paymentTotalValue[' + inputId + ']');
        let discountInput = document.getElementById('tributarydetails_discount[' + inputId + ']');
        //Si el total supera 100
        if (getTotalPorcentaje() > 100) {
            this.value = parseFloat(0);
            valueInput.value = parseFloat(0);
            montoTotalInput.value = parseFloat(0);
        }
        //Si el total es menor a 0
        else if (getTotalPorcentaje() < 0) {
            this.value = parseFloat(0);
            valueInput.value = parseFloat(0);
            montoTotalInput.value = parseFloat(0);
        }
        else if (this.value == '') {
            this.value = parseFloat(0);
            valueInput.value = parseFloat(0);
            montoTotalInput.value = parseFloat(0);
        }
        //Si esta todo OK
        else {
            let montoTotal = document.getElementById('montoTotal').value;
            let value = parseFloat(this.value)/100 * parseFloat(montoTotal);
            valueInput.value = parseFloat(value).toFixed(3);
            montoTotalInput.value = (parseFloat(valueInput.value) * (100 - parseFloat(discountInput.value)) / 100).toFixed(3);
        }
    }
    getTotalPorcentaje();
    getTotalValue();
}

//this -> input
function getValue() {
    //Entradas no validas
    let montoTotal = document.getElementById('montoTotal').value;
    if (parseFloat(this.value) > parseFloat(montoTotal)) {
        this.value = parseFloat(0);
    }
    else if (parseFloat(this.value) < 0) {
        this.value = parseFloat(0);
    }
    //Entradas validas
    else {
        let inputId = getInputId(this);
        //Input del porcentaje asociado al valor
        let percentageInput = document.getElementById('tributarydetails_paymentPercentage[' + inputId + ']');
        let montoTotalInput = document.getElementById('tributarydetails_paymentTotalValue[' + inputId + ']');
        let discountInput = document.getElementById('tributarydetails_discount[' + inputId + ']');
        //Verificar el total
        //Si el total supera el monto total
        if (getTotalValue() > parseFloat(montoTotal)) {
            percentageInput.value = parseFloat(0);
            this.value = parseFloat(0);
            montoTotalInput.value = parseFloat(0);
        }
        //Si el total es menor a 0
        else if (getTotalValue() < 0) {
            percentageInput.value = parseFloat(0);
            this.value = parseFloat(0);
            montoTotalInput.value = parseFloat(0);
        }
        else if (this.value == '') {
            percentageInput.value = parseFloat(0);
            this.value = parseFloat(0);
            montoTotalInput.value = parseFloat(0);
        }
        //Si esta todo OK
        else {
            let percentage = parseFloat(this.value) * 100 / parseFloat(montoTotal);
            percentageInput.value = parseFloat(percentage).toFixed(2);
            montoTotalInput.value = (parseFloat(this.value) * (100 - parseFloat(discountInput.value)) / 100).toFixed(3);
        }
    }
    getTotalPorcentaje();
    getTotalValue();
}

function getTotalPorcentaje() {
    let totalPercentage = 0;
    document.getElementsByName('tributarydetails_paymentPercentage[]').forEach(input => {
            totalPercentage += parseFloat(input.value);
        });
    document.getElementById('porcentajeActual').value = parseFloat(totalPercentage).toFixed(2);
    return totalPercentage;
}

//this -> input
function getMontoTotal(montoTotalInput) {
    let inputId = getInputId(this);
    let valueInput = document.getElementById('tributarydetails_paymentValue[' + inputId + ']');
    let discountInput = document.getElementById('tributarydetails_discount[' + inputId + ']');
    let percentageInput = document.getElementById('tributarydetails_paymentPercentage[' + inputId + ']');
    let montoTotal = document.getElementById('montoTotal').value;
    //Si pone valor 0 o elimina el valor
    if (parseFloat(this.value) == 0 || this.value == '') {
        percentageInput.value = parseFloat(0);
        valueInput.value = parseFloat(0);
        this.value = parseFloat(0);
    }
    //Entradas sin descuento
    else if (parseFloat(discountInput.value) == 0) {
        //Si es mayor al subtotal o si es menor a 0 - NO validas
        if (parseFloat(this.value) > parseFloat(valueInput.value) || parseFloat(this.value) < 0) {
            this.value = parseFloat(valueInput.value);
        }
        //Entrada valida
        else {
            //total y subtotal son iguales
            valueInput.value = parseFloat(this.value);
            let percentage = parseFloat(valueInput.value) * 100 / parseFloat(montoTotal);
            percentageInput.value = parseFloat(percentage).toFixed(2);
        }
    }
    //Entradas con descuento
    else {
        let montoTotalValue = parseFloat(valueInput.value) * (100 - parseFloat(discountInput.value)) / 100;
        //Si es mayor al subtotal o si es menor a 0 - NO validas
        if (parseFloat(this.value) > parseFloat(montoTotalValue) || parseFloat(this.value) < 0) {
            this.value = parseFloat(montoTotalValue).toFixed(3);
        }
        //Entrada valida
        else {
            //total y subtotal NO son iguales
            let valueNoDiscount = (parseFloat(this.value) * 100 / (100 - parseFloat(discountInput.value))).toFixed(3);
            valueInput.value = parseFloat(valueNoDiscount);
            let percentage = parseFloat(valueInput.value) * 100 / parseFloat(montoTotal);
            percentageInput.value = parseFloat(percentage).toFixed(2);
        }
    }
    getTotalPorcentaje();
    getTotalValue();
}

function getTotalValue() {
    let totalValue = 0;
    document.getElementsByName('tributarydetails_paymentValue[]').forEach(input => {
            totalValue += parseFloat(input.value);
        });
    document.getElementById('montoActual').value = parseFloat(totalValue).toFixed(2);
    return totalValue;
}

//this -> input
function getDiscount() {
    if (parseFloat(this.value) > 100) {
        this.value = parseFloat(0);
    }
    else if (parseFloat(this.value) < 0) {
        this.value = parseFloat(0);
    }
    else if (this.value == '') {
        this.value = parseFloat(0);
    }
    //Valores validos
    else {
        let inputId = getInputId(this);
        let valueInput = document.getElementById('tributarydetails_paymentValue[' + inputId + ']');
        let montoTotal = document.getElementById('tributarydetails_paymentTotalValue[' + inputId + ']');
        //Le quita el descuento, subtotal es el mismo q el total
        if (parseFloat(this.value) == 0) {
            montoTotal.value = parseFloat(valueInput.value);
        //Si agrega descuento, calcular
        } else {
            montoTotal.value = (parseFloat(valueInput.value) * (100 - parseFloat(this.value)) / 100).toFixed(3);
        }
    }
}


function getInputId(inputElement) {
    //Regex
    let inputId = inputElement.id.match(/(\d+)/);
    return inputId[0];
}


function reDistributeMontoTotal() {
    document.getElementsByName('tributarydetails_paymentPercentage[]').forEach(input => {
        input.value = parseFloat(0);
    });
    document.getElementsByName('tributarydetails_paymentValue[]').forEach(input => {
        input.value = parseFloat(0);
    });
    document.getElementsByName('tributarydetails_paymentTotalValue[]').forEach(input => {
        input.value = parseFloat(0);
    });
    getTotalPorcentaje();
    getTotalValue();

    if (parseFloat(this.value) < 0) {
        this.value = parseFloat(0);
    }
    else if (this.value == '') {
        this.value = parseFloat(0);
    }
}


getTotalPorcentaje();
getTotalValue();
