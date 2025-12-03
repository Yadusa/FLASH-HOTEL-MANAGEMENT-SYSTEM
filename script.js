const buttons = document.querySelectorAll(".tab-button");
const contents = document.querySelectorAll(".tab-content");

buttons.forEach(btn => {
    btn.addEventListener("click", () => {
        buttons.forEach(b => b.classList.remove("active"));
        btn.classList.add("active");

        contents.forEach(c => c.classList.remove("active"));
        document.getElementById(btn.dataset.target).classList.add("active");
    });
});



// Fill year dropdown
let yearSelect = document.getElementById("year");
for (let y = 2025; y <= 2045; y++) {
    let option = document.createElement("option");
    option.text = y;
    yearSelect.add(option);
}

function goQR(wallet) {
    if (wallet === "boost") window.location.href = "boost_qr.html";
    if (wallet === "grabpay") window.location.href = "grabpay_qr.html";
    if (wallet === "shopeepay") window.location.href = "shopee_qr.html";
    if (wallet === "tng") window.location.href = "tng_qr.html";
}

// CREDIT CARD FORM VALIDATION
document.getElementById("creditForm").addEventListener("submit", function(event) {
    event.preventDefault(); // stop default submit

    // Get all input values
    let name = document.getElementById("cardName").value.trim();
    let number = document.getElementById("cardNumber").value.trim();
    let cvv = document.getElementById("cvv").value.trim();
    let month = document.getElementById("month").value;
    let year = document.getElementById("year").value;
    let country = document.getElementById("country").value;

    // Reset previous field borders
    document.querySelectorAll("#creditForm input, #creditForm select").forEach(el => {
        el.style.borderColor = "";
    });

    // Validate each field individually
    if (!name) {
        alert("❗ Please enter Cardholder Name.");
        document.getElementById("cardName").style.borderColor = "red";
        document.getElementById("cardName").focus();
        return;
    }

    if (!number) {
        alert("❗ Please enter Credit Card Number.");
        document.getElementById("cardNumber").style.borderColor = "red";
        document.getElementById("cardNumber").focus();
        return;
    }

    if (number.length !== 16 || isNaN(number)) {
        alert("❗ Credit card number must be 16 digits.");
        document.getElementById("cardNumber").style.borderColor = "red";
        document.getElementById("cardNumber").focus();
        return;
    }

    if (!cvv) {
        alert("❗ Please enter CVV.");
        document.getElementById("cvv").style.borderColor = "red";
        document.getElementById("cvv").focus();
        return;
    }

    if (cvv.length < 3 || cvv.length > 4 || isNaN(cvv)) {
        alert("❗ CVV must be 3–4 digits.");
        document.getElementById("cvv").style.borderColor = "red";
        document.getElementById("cvv").focus();
        return;
    }

    if (!month) {
        alert("❗ Please select Expiry Month.");
        document.getElementById("month").style.borderColor = "red";
        document.getElementById("month").focus();
        return;
    }

    if (!year) {
        alert("❗ Please select Expiry Year.");
        document.getElementById("year").style.borderColor = "red";
        document.getElementById("year").focus();
        return;
    }

    if (!country) {
        alert("❗ Please select Card Issuing Country.");
        document.getElementById("country").style.borderColor = "red";
        document.getElementById("country").focus();
        return;
    }

    // All validation passed → redirect
    window.location.href = "payment_success.html";
});

