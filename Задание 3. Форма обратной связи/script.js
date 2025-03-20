
document.getElementById("feedback-form").addEventListener("submit", function (event) {
    event.preventDefault();

    let name = document.getElementById("name").value.trim();
    let email = document.getElementById("email").value.trim();
    let phone = document.getElementById("phone").value.trim();
    let comment = document.getElementById("comment").value.trim();

    console.log(name);
    console.log(phone);

    let valid = true;

    if (!/^\S+@\S+\.\S+$/.test(email)) {
        document.getElementById("email").style.border = "2px solid red";
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

document.getElementById("phone").addEventListener("input", function () {
    let value = this.value.replace(/\D/g, "");

    if (value.startsWith("8")) {
        value = "7" + value.slice(1);
    } else if (!value.startsWith("7")) {
        value = "7" + value;
    }

    value = value.slice(0, 11);

    let formattedNumber = `+7`;
    if (value.length > 1) formattedNumber += ` (${value.slice(1, 4)}`;
    if (value.length > 4) formattedNumber += `) ${value.slice(4, 7)}`;
    if (value.length > 7) formattedNumber += `-${value.slice(7, 9)}`;
    if (value.length > 9) formattedNumber += `-${value.slice(9, 11)}`;

    this.value = formattedNumber;
});

document.getElementById("name").addEventListener("input", function () {
    this.value = this.value.replace(/[^a-zA-Zа-яА-ЯёЁ\s]/g, "");
    if (this.value.length > 20) {
        this.value = this.value.slice(0, 20);
    }
});