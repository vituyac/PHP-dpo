document.getElementById("geo-form").addEventListener("submit", function (event) {
    event.preventDefault();

    const address = document.getElementById("address").value.trim();
    const resultDiv = document.getElementById("result");

    if (!address) {
        resultDiv.innerHTML = "<p style='color:red;'>Введите адрес</p>";
        return;
    }

    resultDiv.innerHTML = "Поиск...";

    fetch("form.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams({ address: address })
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            resultDiv.innerHTML = `<p style="color:red;">Ошибка: ${data.error}</p>`;
        } else {
            resultDiv.innerHTML = `
                <p><strong>Адрес:</strong> ${data.formatted}</p>
                <p><strong>Координаты:</strong> ${data.coords}</p>
                <p><strong>Ближайшее метро:</strong> ${data.metro}</p>
            `;
        }
    })
    .catch(error => {
        resultDiv.innerHTML = `<p style="color:red;">Ошибка запроса: ${error}</p>`;
    });
});