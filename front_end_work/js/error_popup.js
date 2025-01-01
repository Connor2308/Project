console.log("JavaScript file loaded");

function showErrorMessage(errorMessage) {
    const errorBanner = document.getElementById('errorBanner');
    const errorText = document.getElementById('errorText');

    if (errorMessage) {
        console.log("Error Message: ", errorMessage); 
        errorBanner.style.display = 'block';
        errorText.innerText = errorMessage;
    }
}
