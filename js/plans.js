const form = document.getElementById("planForm");
const tableBody = document.getElementById("plansTableBody");
const searchInput = document.getElementById("searchInput");
const cancelBtn = document.getElementById("cancelBtn");


// Load Plans
function loadPlans() {

    const search = searchInput.value;

    fetch(`php/plans.php?action=get&search=${encodeURIComponent(search)}`)
        .then(response => response.json())
        .then(plans => {

            tableBody.innerHTML = "";

            plans.forEach(plan => {

                const row = document.createElement("tr");

                row.innerHTML = `
                    <td>${plan.plan_id}</td>
                    <td>${plan.plan_name}</td>
                    <td>${plan.duration_months} month(s)</td>
                    <td>Rs. ${parseFloat(plan.price).toFixed(2)}</td>
                    <td>${plan.description || ""}</td>
                    <td>
                        <button class="edit-btn" onclick="editPlan(${plan.plan_id})">
                            Edit
                        </button>

                        <button class="delete-btn" onclick="deletePlan(${plan.plan_id})">
                            Delete
                        </button>
                    </td>
                `;

                tableBody.appendChild(row);
            });

        })
        .catch(error => {
            console.error("Error:", error);
            alert("Failed to load plans.");
        });
}


// Add / Update Plan
form.addEventListener("submit", function(event) {

    event.preventDefault();

    const planId = document.getElementById("plan_id").value;

    const data = {
        plan_id: planId,
        plan_name: document.getElementById("plan_name").value.trim(),
        duration_months: document.getElementById("duration_months").value,
        price: document.getElementById("price").value,
        description: document.getElementById("description").value.trim()
    };

    const action = planId ? "update" : "add";

    fetch(`php/plans.php?action=${action}`, {
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
            loadPlans();
        }

    })
    .catch(error => {
        console.error("Error:", error);
        alert("Something went wrong.");
    });
});


// Edit Plan
function editPlan(id) {

    fetch(`php/plans.php?action=get`)
        .then(response => response.json())
        .then(plans => {

            const plan = plans.find(p => p.plan_id == id);

            if (!plan) {
                alert("Plan not found.");
                return;
            }

            document.getElementById("plan_id").value = plan.plan_id;
            document.getElementById("plan_name").value = plan.plan_name;
            document.getElementById("duration_months").value = plan.duration_months;
            document.getElementById("price").value = plan.price;
            document.getElementById("description").value = plan.description || "";

            document.querySelector(".form-section h3").textContent =
                "Edit Membership Plan";
        });
}


// Delete Plan
function deletePlan(id) {

    if (!confirm("Are you sure you want to delete this plan?")) {
        return;
    }

    fetch(`php/plans.php?action=delete&id=${id}`)
        .then(response => response.json())
        .then(result => {

            alert(result.message);

            if (result.success) {
                loadPlans();
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

    document.getElementById("plan_id").value = "";

    document.querySelector(".form-section h3").textContent =
        "Add Membership Plan";
}


// Search
searchInput.addEventListener("input", function() {
    loadPlans();
});


// Initial Load
loadPlans();