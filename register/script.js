const params = new URLSearchParams(window.location.search);
let error = params.get('error');

switch (error) {
    case "empty_form":
        errorTip("Please fill out the form.")
        break;

    case "user_exist":
        errorTip("User already exists.");
        break;
    case "database_error":
        errorTip("Database error occurred.");
        break;

    default:
        break;
}

function errorTip(text) {
    let c = document.getElementById('error')
    c.innerText = text;
    c.style.display = 'block';
}