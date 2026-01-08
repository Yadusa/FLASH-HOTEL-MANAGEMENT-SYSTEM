document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById("feedbackForm");
    const popup = document.getElementById("popup");

    form.addEventListener("submit", function (e) {
        e.preventDefault(); // Prevents page from refreshing
        
        // Show the popup
        popup.style.display = "flex";
        
        // Clear the input fields
        form.reset();
    });
});

function closePopup() {
    // Redirect to main page as per your current logic
    window.location.href = 'hotel.php';
}