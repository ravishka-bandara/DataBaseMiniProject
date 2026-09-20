const form = document.getElementById("trainerForm");
const tableBody = document.getElementById("trainersTableBody");
const searchInput = document.getElementById("searchInput");
const cancelBtn = document.getElementById("cancelBtn");


// Load Trainers
function loadTrainers() {

    const search = searchInput.value;

    fetch(`php/trainers.php?action=get&search=${encodeURIComponent(search)}`)
        .then(response => response.json())
        .then(trainers => {

            tableBody.innerHTML = "";

            trainers.forEach(trainer => {

                const row = document.createElement("tr");

                row.innerHTML = `
                    <td>${trainer.trainer_id}</td>
                    <td>${trainer.trainer_name}</td>
                    <td>${trainer.phone || ""}</td>
                    <td>${trainer.email || ""}</td>
                    <td>${trainer.specialization || ""}</td>
                    <td>
                        <button class="edit-btn" onclick="editTrainer(${trainer.trainer_id})">
                            Edit
                        </button>

                        <button class="delete-btn" onclick="deleteTrainer(${trainer.trainer_id})">
                            Delete
                        </button>
                    </td>
                `;

                tableBody.appendChild(row);
            });

        })
        .catch(error => {
            console.error("Error:", error);
            alert("Failed to load trainers.");
        });
}


// Add / Update Trainer
form.addEventListener("submit", function(event) {

    event.preventDefault();

    const trainerId = document.getElementById("trainer_id").value;

    const data = {
        trainer_id: trainerId,
        trainer_name: document.getElementById("trainer_name").value.trim(),
        phone: document.getElementById("phone").value.trim(),
        email: document.getElementById("email").value.trim(),
        specialization: document.getElementById("specialization").value.trim()
    };

    const action = trainerId ? "update" : "add";

    fetch(`php/trainers.php?action=${action}`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {

        alert(result.message);

        if (result.success) {
            resetForm();
            loadTrainers();
        }

    })
    .catch(error => {
        console.error("Error:", error);
        alert("Something went wrong.");
    });
});


// Edit Trainer
function editTrainer(id) {

    fetch(`php/trainers.php?action=get`)
        .then(response => response.json())
        .then(trainers => {

            const trainer = trainers.find(
                t => t.trainer_id == id
            );

            if (!trainer) {
                alert("Trainer not found.");
                return;
            }

            document.getElementById("trainer_id").value =
                trainer.trainer_id;

            document.getElementById("trainer_name").value =
                trainer.trainer_name;

            document.getElementById("phone").value =
                trainer.phone || "";

            document.getElementById("email").value =
                trainer.email || "";

            document.getElementById("specialization").value =
                trainer.specialization || "";

            document.querySelector(".form-section h3").textContent =
                "Edit Trainer";
        });
}


// Delete Trainer
function deleteTrainer(id) {

    if (!confirm("Are you sure you want to delete this trainer?")) {
        return;
    }

    fetch(`php/trainers.php?action=delete&id=${id}`)
        .then(response => response.json())
        .then(result => {

            alert(result.message);

            if (result.success) {
                loadTrainers();
            }

        })
        .catch(error => {
            console.error("Error:", error);
            alert("Something went wrong.");
        });
}


// Cancel / Reset
cancelBtn.addEventListener("click", function() {
    resetForm();
});


function resetForm() {

    form.reset();

    document.getElementById("trainer_id").value = "";

    document.querySelector(".form-section h3").textContent =
        "Add Trainer";
}


// Search
searchInput.addEventListener("input", function() {
    loadTrainers();
});


// Initial Load
loadTrainers();