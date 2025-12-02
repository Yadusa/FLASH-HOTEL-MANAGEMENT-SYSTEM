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

// Fill month dropdown
let monthSelect = document.getElementById("month");
for (let m = 1; m <= 12; m++) {
    let option = document.createElement("option");
    option.text = m;
    monthSelect.add(option);
}

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
