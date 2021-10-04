const netoFacturar = parseFloat(document.getElementById('netoFacturar').value);
const totalFacturar = parseFloat(document.getElementById('totalFacturar').value);
var counter = 0;

document.getElementById('agregarFactura').addEventListener("click", addRow, false);


function createRowColumn(row) {
  let column = document.createElement("td");
  row.appendChild(column);
  return column;
}

let selectData = [contractPaymentDetails, paymentUnits, modules, razonesSociales];

let tableInput = [
    {
        name: 'monto',
        type: 'numeric',
        id: 'monto_new_',
    },
    {
        name: 'porcentaje',
        type: 'numeric',
        id: 'porcentaje_new_',
    },
    {
        name: 'descuento',
        type: 'numeric',
        id: 'descuento_new_',
    },
    {
        name: 'neto',
        type: 'numeric',
        id: 'neto_new_',
    },
    {
        name: 'total',
        type: 'numeric',
        id: 'total_new_',
    },
    {
        name: 'grupo',
        type: 'numeric',
        id: null,
    },

    {
        name: 'numeroOC',
        type: 'text',
        id: null,
    },
    {
        name: 'fechaOC',
        type: 'date',
        id: null,
    },
    {
        name: 'vigenciaOC',
        type: 'date',
        id: null,
    },

    {
        name: 'numeroHES',
        type: 'text',
        id: null,
    },
    {
        name: 'fechaHES',
        type: 'date',
        id: null,
    },
    {
        name: 'vigenciaHES',
        type: 'date',
        id: null,
    },

    {
        name: 'eliminar',
        type: 'button',
        id: null,
    },
]

function addRow() {
  let newrow = document.createElement("tr");

  // Input Select
  let razonesSociales_column = createRowColumn(newrow);
  let razonesSociales_select = document.createElement('select');
  razonesSociales_select.setAttribute("name", 'razonesSociales[]');
  // Opciones del select razonesSociales
  razonesSociales.forEach(razonSocial => {
    let opt = document.createElement('option');
    opt.value = razonSocial.id;
    opt.innerHTML = razonSocial.clientRazonSocial;
    razonesSociales_select.appendChild(opt);
  });
  razonesSociales_column.appendChild(razonesSociales_select);


  // Input Select
  let modules_column = createRowColumn(newrow);
  let modules_select = document.createElement('select');
  modules_select.setAttribute("name", 'modules[]');
  // Opciones del select modules
  modules.forEach(module => {
    let opt = document.createElement('option');
    opt.value = module.id;
    opt.innerHTML = module.moduleName;
    modules_select.appendChild(opt);
  });
  modules_column.appendChild(modules_select);


  // Input Select
  let paymentUnits_column = createRowColumn(newrow);
  let paymentUnits_select = document.createElement('select');
  paymentUnits_select.setAttribute("name", 'paymentUnits[]');
  // Opciones del select paymentUnits
  paymentUnits.forEach(paymentUnit => {
    let opt = document.createElement('option');
    opt.value = paymentUnit.id;
    opt.innerHTML = paymentUnit.payment_units;
    paymentUnits_select.appendChild(opt);
  });
  paymentUnits_column.appendChild(paymentUnits_select);


  // Input Select
  let contractPaymentDetails_column = createRowColumn(newrow);
  let contractPaymentDetails_select = document.createElement('select');
  contractPaymentDetails_select.setAttribute("name", 'contractPaymentDetails[]');
  // Opciones del select contractPaymentDetails
  contractPaymentDetails.forEach(contractPaymentDetail => {
    let opt = document.createElement('option');
    opt.value = contractPaymentDetail.id;
    opt.innerHTML = contractPaymentDetail.contractPaymentDetails_description;
    contractPaymentDetails_select.appendChild(opt);
  });
  contractPaymentDetails_column.appendChild(contractPaymentDetails_select);



  tableInput.forEach(input => {
    let col = createRowColumn(newrow);
    let inputCol = createInputColumn(input.type, input.name, input.id);
    col.appendChild(inputCol);
    if (input.type == 'button') {
        let inputIdCol = createInputColumn('hidden', 'id');
        col.appendChild(inputIdCol);
    }
  });
  counter = counter + 1;

  let table = document.getElementById('tablaFacturas');
  let tbody = table.querySelector('tbody') || table;
  tbody.appendChild(newrow);

  document.getElementById('largoTabla').value = table.tBodies[0].rows.length;
}

// types: numeric, date, checkbox, text, button, select
function createInputColumn(type, name, id) {
    let inputColumn = document.createElement("input");
    inputColumn.setAttribute("type", type);
    inputColumn.setAttribute("name", name + '[]');
    if (id != null) {
        inputColumn.setAttribute("id", id + counter);
    }
    if (name == 'id') {
        inputColumn.setAttribute("value", '0');
    } else if (name == 'monto' || name == 'porcentaje' || name == 'descuento') {
        inputColumn.setAttribute("onchange", "validateFactura(this)");
    } else if (name == 'neto' || name == 'total') {
        inputColumn.setAttribute("readonly", true);
    }

    if (type == 'button') {
        inputColumn.setAttribute("value", 'Eliminar fila');
        inputColumn.setAttribute("onClick", "deleteRow(this)");
    }
    return inputColumn;
}


function deleteRow(button) {
    var row = button.parentNode.parentNode;
    var tbody = row.parentNode;
    tbody.removeChild(row);

    let table = document.getElementById('tablaFacturas');
    document.getElementById('largoTabla').value = table.tBodies[0].rows.length;
  }


document.getElementById('largoTabla').value = document.getElementById('tablaFacturas').tBodies[0].rows.length;

function validateFactura(inputElement) {
    let monto, porcentaje, descuento, neto, total;
    let elementType = inputElement.id.match(/([^_]+)/g)[0];
    let elementAge = inputElement.id.match(/([^_]+)/g)[1];
    let elementId = inputElement.id.match(/([^_]+)/g)[2];

    //monto
    if (elementType == 'monto') {
        // get values
        monto = validateValue(0, netoFacturar, inputElement);
        porcentaje = monto * 100 / netoFacturar;
        descuento = document.getElementById('descuento_' + elementAge + '_' + elementId).value;
        neto = monto * (100 - descuento) / 100;
        total = neto * 1.19;
        // set values
        document.getElementById('porcentaje_' + elementAge + '_' + elementId).value = porcentaje.toFixed(2);
        document.getElementById('neto_' + elementAge + '_' + elementId).value = neto.toFixed(2);
        document.getElementById('total_' + elementAge + '_' + elementId).value = total.toFixed(2);
        updateMontoFacturado();
    }
    //porcentaje
    else if (elementType == 'porcentaje') {
        // get values
        porcentaje = validateValue(0, 100.01, inputElement);
        monto = porcentaje * netoFacturar / 100;
        descuento = document.getElementById('descuento_' + elementAge + '_' + elementId).value;
        neto = monto * (100 - descuento) / 100;
        total = neto * 1.19;
        // set values
        document.getElementById('monto_' + elementAge + '_' + elementId).value = monto.toFixed(0);
        document.getElementById('neto_' + elementAge + '_' + elementId).value = neto.toFixed(2);
        document.getElementById('total_' + elementAge + '_' + elementId).value = total.toFixed(2);
        updateMontoFacturado();
    }
    //descuento
    else if (elementType == 'descuento') {
        // get values
        descuento = validateValue(0, 100.01, inputElement);
        monto = document.getElementById('monto_' + elementAge + '_' + elementId).value;
        neto = monto * (100 - descuento) / 100;
        total = neto * 1.19;
        // set values
        document.getElementById('neto_' + elementAge + '_' + elementId).value = neto.toFixed(2);
        document.getElementById('total_' + elementAge + '_' + elementId).value = total.toFixed(2);
    }
}

function validateValue(minValue, maxValue, inputElement) {
    if (inputElement.value > maxValue) {
        inputElement.value = 0;
        return 0;
    } else if (inputElement.value < minValue) {
        inputElement.value = 0;
        return 0;
    }
    return inputElement.value;
}


function updateMontoFacturado() {
    // Monto: columna 4
    let table = document.getElementById('tablaFacturas');
    let numRows = document.getElementById('largoTabla').value = table.tBodies[0].rows.length;
    let montoFacturadoActual = 0;
    for (let i = 0; i < numRows; i++) {
        // tBodies = cuerpo tabla
        // rows = fila
        // children[] = columna
        // children[] = hijos de la columna, input en este caso

        idColumnaMonto = table.tBodies[0].rows[i].children[4].children[0].id;
        valueColumnaMonto = parseFloat(table.tBodies[0].rows[i].children[4].children[0].value);
        montoFacturadoActual += valueColumnaMonto;
    }
    document.getElementById('montoFacturado').value = montoFacturadoActual.toFixed(2);
}
updateMontoFacturado();


function validateMyForm(form) {
    let montoFacturado = document.getElementById('montoFacturado').value;
    if (montoFacturado == netoFacturar && checkGroupNullValues()) {
        //form.submit();
    } else if (montoFacturado > netoFacturar) {
        alert('No se puede facturar más del monto total.');
        return false;
    } else if (montoFacturado < netoFacturar) {
        alert('No se puede facturar menos del monto total.');
        return false;
    } else {
        alert('El valor de Grupo no puede estar vacío.');
        return false;
    }
    if (!areGroupsValid()) {
        alert('Los grupos no pueden tener razones sociales distintas.');
        return false;
    }
    return true;
}

function checkGroupNullValues() {
    // Grupo: columna 9
    let table = document.getElementById('tablaFacturas');
    let numRows = document.getElementById('largoTabla').value = table.tBodies[0].rows.length;
    for (let i = 0; i < numRows; i++) {
        valorGrupo = table.tBodies[0].rows[i].children[9].children[0].value;
        // tBodies = cuerpo tabla
        // rows = fila
        // children[] = columna
        // children[] = hijos de la columna, input en este caso
        if (valorGrupo == "") {
            return false;
        }
    }
    return true;
}



function areGroupsValid() {
    // Monto: columna 4
    let table = document.getElementById('tablaFacturas');
    let numRows = document.getElementById('largoTabla').value = table.tBodies[0].rows.length;

    for (let i = 0; i < numRows; i++) {
        valorGrupo_I = table.tBodies[0].rows[i].children[9].children[0].value;
        idRazonSocial_I = table.tBodies[0].rows[i].children[0].children[0].value;
        for (let j = 0; j < numRows; j++) {
            valorGrupo_J = table.tBodies[0].rows[j].children[9].children[0].value;
            idRazonSocial_J = table.tBodies[0].rows[j].children[0].children[0].value;
            // Si son el mismo grupo, verificar que tienen la misma razon social
            if (valorGrupo_I == valorGrupo_J) {
                // Si tienen distinta razon social, mencionar
                if (idRazonSocial_I != idRazonSocial_J) {
                    console.log('El id: ', i, ', tiene distinta razon social q el id: ', j);
                    return false;
                }
            }
        }
    }
    return true;
}
