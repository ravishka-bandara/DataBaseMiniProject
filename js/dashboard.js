function loadDashboard() {

    fetch("php/dashboard.php")
        .then(response => response.json())
        .then(data => {

            document.getElementById("memberCount").textContent =
                data.members;

            document.getElementById("planCount").textContent =
                data.plans;

            document.getElementById("trainerCount").textContent =
                data.trainers;

            document.getElementById("paymentCount").textContent =
                data.payments;

        })
        .catch(error => {
            console.error("Error:", error);
        });
}


// Load dashboard when page opens
loadDashboard();