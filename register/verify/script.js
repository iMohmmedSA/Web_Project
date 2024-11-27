const params = new URLSearchParams(window.location.search);
let id = params.get('id');
let error = params.get('error');

document.getElementById('userID').value = id;

switch (error) {
    case "empty_form":
        errorTip("Please fill out the form.")
        break;

    case "activation_failed":
        errorTip("Activation failed.");
        break;
    case "invalid_code":
        errorTip("Invalid code.");
        break;
    default:
        break;
}

function errorTip(text) {
    let c = document.getElementById('error')
    c.innerText = text;
    c.style.display = 'block';
}