function getCurrentDate(inputDate) {
    //Saca el valor del formulario de la fecha
    let formAction = document.getElementById('inputPeriodoForm').action;
    //Elimina su fecha inicial
    formAction = formAction.slice(0, -7);
    //Agrega la fecha del input
    formAction = formAction + inputDate.value;
    document.getElementById('inputPeriodoForm').action = formAction;
}
