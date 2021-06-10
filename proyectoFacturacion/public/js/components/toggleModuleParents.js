//Append function to html element
document.getElementById('hasParent').addEventListener("change", toggleModuleParents, false);

function toggleModuleParents() {
    if (this.checked) {
        document.getElementById('hasParentLabel').innerHTML = "Si";
        document.getElementById('parentModule').style.visibility = 'visible';
        this.value = "si";
    } else {
        document.getElementById('hasParentLabel').innerHTML = "No";
        document.getElementById('parentModule').style.visibility = 'hidden';
        this.value = "no";
    }
    console.log(this.value);
}
