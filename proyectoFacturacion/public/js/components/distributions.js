//Inicializa la tabla "detalles" del dashboard
$('#tablaDistributions').bootstrapTable({
    pageSize: 100,
    exportDataType: 'all',
});
const distributionsTableLength = $('#tablaDistributions').bootstrapTable('getData').length;
document.getElementById('distributionsTableLength').value = distributionsTableLength;


function appendFunctions() {
    //tributarydetails_paymentTotalValue[] getMontoTotal(this)
    //tributarydetails_discount[] getDiscount(this)
    //tributarydetails_paymentValue[] getValue(this)
    //tributarydetails_paymentPercentage[] getPercentage(this)
    document.getElementById('contractDistribution_massAssign').addEventListener("change", getMassAssign, false);

    document.getElementsByName('contractDistribution_percentage[]').forEach(input => {
        document.getElementById(input.id).addEventListener("change", getPercentage, false);
    });
    document.getElementsByName('contractDistribution_discount[]').forEach(input => {
        document.getElementById(input.id).addEventListener("change", getDiscount, false);
    });
}
appendFunctions();


function getMassAssign() {
    let massValue = this.value;
    //NO ASIGNADO
    if (massValue === "No asignado") {
        //Asignar a los tipos
        document.getElementsByName('contractDistribution_type[]').forEach(input => {
            input.value = "No asignado";
        });
        //Asignar a los porcentajes
        MassAssignPercentage(true, 0);
        getTotalPorcentaje();
        toggleTotalPercentage("none");
        document.getElementById("distributionsType").value = "No asignado";
    //PORCENTAJE
    } else if (massValue === "Porcentaje") {
        //Asignar a los tipos
        document.getElementsByName('contractDistribution_type[]').forEach(input => {
            input.value = "Porcentaje";
        });
        //Asignar a los porcentajes
        const equalPercentage = Math.floor(10000/distributionsTableLength)/100;
        MassAssignPercentage(false, equalPercentage);
        getTotalPorcentaje();
        toggleTotalPercentage("block");
        document.getElementById("distributionsType").value = "Porcentaje";
    //UNIDAD DE COBRO
    } else if (massValue === "Unidad de cobro") {
        //Asignar a los tipos
        document.getElementsByName('contractDistribution_type[]').forEach(input => {
            input.value = "Unidad de cobro";
        });
        //Asignar a los porcentajes
        MassAssignPercentage(true, 0);
        getTotalPorcentaje();
        toggleTotalPercentage("none");
        document.getElementById("distributionsType").value = "Unidad de cobro";
    }
}

function getPercentage() {
    //Entradas no validas
    toggleTotalPercentage("block");
    if (parseFloat(this.value) > 100) {
        this.value = parseFloat(0);
    }
    else if (parseFloat(this.value) < 0) {
        this.value = parseFloat(0);
    }
    else if (parseFloat(this.value) == '') {
        this.value = parseFloat(0);
    }
    //Entradas validas
    else {
        if (getTotalPorcentaje() > 100) {
            this.value = parseFloat(0);
            getTotalPorcentaje();
        }
        else if (getTotalPorcentaje() < 0) {
            this.value = parseFloat(0);
            getTotalPorcentaje();
        }
    }
}

function MassAssignPercentage(booleanValue, percentageValue) {
    document.getElementsByName('contractDistribution_percentage[]').forEach(input => {
            input.readOnly = booleanValue;
            input.value = percentageValue;
        });
}
function getTotalPorcentaje() {
    let totalPercentage = 0;
    document.getElementsByName('contractDistribution_percentage[]').forEach(input => {
            totalPercentage += parseFloat(input.value);
        });
    document.getElementById('contractDistribution_totalPercentage').value = parseFloat(totalPercentage).toFixed(2);
    return totalPercentage;
}

function toggleTotalPercentage(displayValue) {
    //"block", "none"
    var toggleElement = document.getElementById("totalPercentage");
    toggleElement.style.display = displayValue;
}

//Evitar que ponga valores incorrectos
function getDiscount() {
    if (parseFloat(this.value) > 100) {
        this.value = parseFloat(0);
    }
    else if (parseFloat(this.value) < 0) {
        this.value = parseFloat(0);
    }
    else if (parseFloat(this.value) == '') {
        this.value = parseFloat(0);
    }
}
getTotalPorcentaje();

//Asignar el valor de los tipos al selector
document.getElementsByName('contractDistribution_type[]').forEach(input => {
    document.getElementById('contractDistribution_massAssign').value = input.value;
    document.getElementById("distributionsType").value = input.value;
});

if (document.getElementById("distributionsType").value == "Porcentaje") {
    toggleTotalPercentage("block");
}
