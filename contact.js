document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById("feedbackForm");
    const popup = document.getElementById("popup");

    if (form) {
        form.addEventListener("submit", function (e) {
            e.preventDefault(); // Prevents page reload
            
            // Show the popup
            if (popup) {
                popup.style.display = "flex";
            }
            
            // Clear inputs
            form.reset();
        });
    }
});

function closePopup() {
    // Redirects user back to main page
   window.location.href = '../hotel.php';
}