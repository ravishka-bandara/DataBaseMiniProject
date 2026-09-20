const form = document.getElementById("paymentForm");
const tableBody = document.getElementById("paymentsTableBody");
const searchInput = document.getElementById("searchInput");
const cancelBtn = document.getElementById("cancelBtn");
const memberSelect = document.getElementById("member_id");


// Load Members
function loadMembers() {

    fetch("php/payments.php?action=members")
        .then(response => response.json())
        .then(members => {

            memberSelect.innerHTML =
                '<option value="">Select Member</option>';

            members.forEach(member => {

                const option = document.createElement("option");

                option.value = member.member_id;
                option.textContent = member.full_name;

                memberSelect.appendChild(option);
            });

        })
        .catch(error => {
            console.error("Error:", error);
            alert("Failed to load members.");
        });
}


// Load Payments
function loadPayments() {

    const search = searchInput.value;

    fetch(`php/payments.php?action=get&search=${encodeURIComponent(search)}`)
        .then(response => response.json())
        .then(payments => {

            tableBody.innerHTML = "";

            payments.forEach(payment => {

                const row = document.createElement("tr");

                row.innerHTML = `
                    <td>${payment.payment_id}</td>
                    <td>${payment.full_name}</td>
                    <td>${payment.payment_date}</td>
                    <td>Rs. ${parseFloat(payment.amount).toFixed(2)}</td>
                    <td>${payment.payment_method}</td>
                    <td>${payment.description || ""}</td>
                    <td>
                            <button class="edit-btn" onclick="editPayment(${payment.payment_id})">
                                Edit
                            </button>

                            <button class="delete-btn" onclick="deletePayment(${payment.payment_id})">
                                Delete
                            </button>
                    </td>
                `;

                tableBody.appendChild(row);
            });

        })
        .catch(error => {
            console.error("Error:", error);
            alert("Failed to load payments.");
        });
}


// Add / Update Payment
form.addEventListener("submit", function(event) {

    event.preventDefault();

    const paymentId =
        document.getElementById("payment_id").value;

    const data = {
        payment_id: paymentId,
        member_id: memberSelect.value,
        payment_date:
            document.getElementById("payment_date").value,
        amount:
            document.getElementById("amount").value,
        payment_method:
            document.getElementById("payment_method").value,
        description:
            document.getElementById("description").value.trim()
    };

    const action = paymentId ? "update" : "add";

    fetch(`php/payments.php?action=${action}`, {
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
            loadPayments();
        }

    })
    .catch(error => {
        console.error("Error:", error);
        alert("Something went wrong.");
    });
});


// Edit Payment
function editPayment(id) {

    fetch("php/payments.php?action=get")
        .then(response => response.json())
        .then(payments => {

            const payment = payments.find(
                p => p.payment_id == id
            );

            if (!payment) {
                alert("Payment not found.");
                return;
            }

            document.getElementById("payment_id").value =
                payment.payment_id;

            memberSelect.value =
                payment.member_id;

            document.getElementById("payment_date").value =
                payment.payment_date;

            document.getElementById("amount").value =
                payment.amount;

            document.getElementById("payment_method").value =
                payment.payment_method;

            document.getElementById("description").value =
                payment.description || "";

            document.querySelector(".form-section h3").textContent =
                "Edit Payment";
        });
}


// Delete Payment
function deletePayment(id) {

    if (!confirm("Are you sure you want to delete this payment?")) {
        return;
    }

    fetch(`php/payments.php?action=delete&id=${id}`)
        .then(response => response.json())
        .then(result => {

            alert(result.message);

            if (result.success) {
                loadPayments();
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

    document.getElementById("payment_id").value = "";

    document.querySelector(".form-section h3").textContent =
        "Add Payment";
}


// Search
searchInput.addEventListener("input", function() {
    loadPayments();
});


// Initial Load
loadMembers();
loadPayments();