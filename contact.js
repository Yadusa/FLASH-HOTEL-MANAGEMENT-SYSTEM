document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById("feedbackForm");
    const popup = document.getElementById("popup");

    if (form) {
        form.addEventListener("submit", function (e) {
            e.preventDefault(); // Prevents the page from reloading
            
            // Show the popup by changing display to flex
            popup.style.display = "flex";
            
            // Reset the form fields
            form.reset();
        });
    }
});

// Function for the Close button inside the popup
function closePopup() {
    window.location.href = 'hotel.php';
}