
document.getElementById("feedbackForm").addEventListener("submit", function (event) {
    event.preventDefault();

    document.querySelectorAll(".form-line input, .form-text textarea").forEach(input => {
        input.style.border = "";
    });

    let name = document.getElementById("name").value.trim();
    let email = document.getElementById("email").value.trim();
    let phone = document.getElementById("phone").value.trim();
    let comment = document.getElementById("comment").value.trim();

    let valid = true;
    if (name === "") {
        document.getElementById("name").style.border = "2px solid red";
        valid = false;
    }
    if (!/^\S+@\S+\.\S+$/.test(email)) {
        document.getElementById("email").style.border = "2px solid red";
        valid = false;
    }
    if (!/^\+?\d{10,15}$/.test(phone)) {
        document.getElementById("phone").style.border = "2px solid red";
        valid = false;
    }
    if (comment === "") {
        document.getElementById("comment").style.border = "2px solid red";
        valid = false;
    }
    if (!valid) return;

    fetch("form.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams({
            name: name,
            email: email,
            phone: phone,
            comment: comment
        })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            document.querySelector(".form-container").style.display = "none";
            document.querySelector(".container-info").style.display = "block";
            document.getElementById("info-name").textContent = result.name;
            document.getElementById("info-email").textContent = result.email;
            document.getElementById("info-phone").textContent = result.phone;
            document.getElementById("info-time").textContent = result.contact_time;
        } else {
            alert(result.message);
        }
    })
});
