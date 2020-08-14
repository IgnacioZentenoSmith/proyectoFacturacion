document.getElementById('logout').addEventListener("click", logout, false);

function logout() {
    event.preventDefault();
    document.getElementById('logout-form').submit();
}
