const memberForm = document.getElementById("memberForm");
const membersTableBody = document.getElementById("membersTableBody");
const searchInput = document.getElementById("searchInput");


// =========================
// LOAD MEMBERS
// =========================

function loadMembers(search = "") {

    fetch(`php/members.php?action=get&search=${encodeURIComponent(search)}`)
        .then(response => response.json())
        .then(data => {

            membersTableBody.innerHTML = "";

            data.forEach(member => {

                const row = document.createElement("tr");

                row.innerHTML = `
                    <td>${member.member_id}</td>
                    <td>${member.full_name}</td>
                    <td>${member.gender}</td>
                    <td>${member.phone}</td>
                    <td>${member.email || ""}</td>
                    <td>${member.plan_name || "No Plan"}</td>
                    <td>${member.trainer_name || "No Trainer"}</td>
                    <td>${member.join_date}</td>

                    <td>
                        <button class="edit-btn" onclick="editMember(${member.member_id})">
                            Edit
                        </button>

                        <button class="delete-btn" onclick="deleteMember(${member.member_id})">
                            Delete
                        </button>
                    </td>
                `;

                membersTableBody.appendChild(row);
            });
        })
        .catch(error => {
            console.error("Error loading members:", error);
        });
}


// =========================
// LOAD PLANS
// =========================

function loadPlans() {

    fetch("php/members.php?action=plans")
        .then(response => response.json())
        .then(plans => {

            const planSelect = document.getElementById("plan_id");

            planSelect.innerHTML =
                `<option value="">Select Membership Plan</option>`;

            plans.forEach(plan => {

                planSelect.innerHTML += `
                    <option value="${plan.plan_id}">
                        ${plan.plan_name}
                    </option>
                `;
            });
        });
}


// =========================
// LOAD TRAINERS
// =========================

function loadTrainers() {

    fetch("php/members.php?action=trainers")
        .then(response => response.json())
        .then(trainers => {

            const trainerSelect = document.getElementById("trainer_id");

            trainerSelect.innerHTML =
                `<option value="">Select Trainer</option>`;

            trainers.forEach(trainer => {

                trainerSelect.innerHTML += `
                    <option value="${trainer.trainer_id}">
                        ${trainer.trainer_name}
                    </option>
                `;
            });
        });
}


// =========================
// ADD / UPDATE MEMBER
// =========================

memberForm.addEventListener("submit", function(event) {

    event.preventDefault();

    const memberId = document.getElementById("member_id").value;

    const memberData = {

        member_id: memberId,

        full_name: document.getElementById("full_name").value,

        gender: document.getElementById("gender").value,

        date_of_birth:
            document.getElementById("date_of_birth").value,

        phone:
            document.getElementById("phone").value,

        email:
            document.getElementById("email").value,

        address:
            document.getElementById("address").value,

        join_date:
            document.getElementById("join_date").value,

        plan_id:
            document.getElementById("plan_id").value,

        trainer_id:
            document.getElementById("trainer_id").value
    };


    const action = memberId ? "update" : "add";


    fetch(`php/members.php?action=${action}`, {

        method: "POST",

        headers: {
            "Content-Type": "application/json"
        },

        body: JSON.stringify(memberData)

    })

    .then(response => response.json())

    .then(data => {

        alert(data.message);

        if (data.success) {

            memberForm.reset();

            document.getElementById("member_id").value = "";

            loadMembers();

        }

    })

    .catch(error => {

        console.error("Error:", error);

        alert("Something went wrong.");

    });

});


// =========================
// EDIT MEMBER
// =========================

function editMember(id) {

    fetch(`php/members.php?action=get`)
        .then(response => response.json())
        .then(members => {

            const member = members.find(
                item => item.member_id == id
            );

            if (!member) {
                alert("Member not found.");
                return;
            }


            document.getElementById("member_id").value =
                member.member_id;

            document.getElementById("full_name").value =
                member.full_name;

            document.getElementById("gender").value =
                member.gender;

            document.getElementById("date_of_birth").value =
                member.date_of_birth;

            document.getElementById("phone").value =
                member.phone;

            document.getElementById("email").value =
                member.email || "";

            document.getElementById("address").value =
                member.address || "";

            document.getElementById("join_date").value =
                member.join_date;

            document.getElementById("plan_id").value =
                member.plan_id || "";

            document.getElementById("trainer_id").value =
                member.trainer_id || "";

            window.scrollTo({
                top: 0,
                behavior: "smooth"
            });
        });
}


// =========================
// DELETE MEMBER
// =========================

function deleteMember(id) {

    const confirmDelete = confirm(
        "Are you sure you want to delete this member?"
    );

    if (!confirmDelete) {
        return;
    }


    fetch(`php/members.php?action=delete&id=${id}`)

        .then(response => response.json())

        .then(data => {

            alert(data.message);

            if (data.success) {
                loadMembers();
            }

        })

        .catch(error => {

            console.error("Delete error:", error);

            alert("Something went wrong.");

        });
}


// =========================
// SEARCH
// =========================

searchInput.addEventListener("input", function() {

    loadMembers(this.value);

});


// =========================
// CANCEL BUTTON
// =========================

document.getElementById("cancelBtn")
    .addEventListener("click", function() {

        memberForm.reset();

        document.getElementById("member_id").value = "";

    });


// =========================
// INITIAL LOAD
// =========================

loadPlans();

loadTrainers();

loadMembers();