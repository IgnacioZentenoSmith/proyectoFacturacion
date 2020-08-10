document.getElementById('inputPeriodo').addEventListener("change", getCurrentDate, false);
function getCurrentDate() {
    //Saca el valor del formulario de la fecha
    let formAction = document.getElementById('inputPeriodoForm').action;
    //Elimina su fecha inicial
    formAction = formAction.slice(0, -7);
    //Agrega la fecha del input
    formAction = formAction + this.value;
    document.getElementById('inputPeriodoForm').action = formAction;
}
