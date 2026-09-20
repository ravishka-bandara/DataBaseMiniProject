const form = document.getElementById("attendanceForm");
const tableBody = document.getElementById("attendanceTableBody");
const searchInput = document.getElementById("searchInput");
const cancelBtn = document.getElementById("cancelBtn");
const memberSelect = document.getElementById("member_id");


// Load Members
function loadMembers() {

    fetch("php/attendance.php?action=members")
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


// Load Attendance
function loadAttendance() {

    const search = searchInput.value;

    fetch(`php/attendance.php?action=get&search=${encodeURIComponent(search)}`)
        .then(response => response.json())
        .then(records => {

            tableBody.innerHTML = "";

            records.forEach(record => {

                const row = document.createElement("tr");

                row.innerHTML = `
                    <td>${record.attendance_id}</td>
                    <td>${record.full_name}</td>
                    <td>${record.attendance_date}</td>
                    <td>${record.check_in_time}</td>
                    <td>${record.check_out_time || ""}</td>
                    <td>
                        <button class="edit-btn" onclick="editAttendance(${record.attendance_id})">
                            Edit
                        </button>

                        <button class="delete-btn" onclick="deleteAttendance(${record.attendance_id})">
                            Delete
                        </button>
                    </td>
                `;

                tableBody.appendChild(row);
            });

        })
        .catch(error => {
            console.error("Error:", error);
            alert("Failed to load attendance.");
        });
}


// Add / Update Attendance
form.addEventListener("submit", function(event) {

    event.preventDefault();

    const attendanceId =
        document.getElementById("attendance_id").value;

    const data = {
        attendance_id: attendanceId,
        member_id: memberSelect.value,
        attendance_date:
            document.getElementById("attendance_date").value,
        check_in_time:
            document.getElementById("check_in_time").value,
        check_out_time:
            document.getElementById("check_out_time").value
    };

    const action = attendanceId ? "update" : "add";

    fetch(`php/attendance.php?action=${action}`, {
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
            loadAttendance();
        }

    })
    .catch(error => {
        console.error("Error:", error);
        alert("Something went wrong.");
    });
});


// Edit Attendance
function editAttendance(id) {

    fetch("php/attendance.php?action=get")
        .then(response => response.json())
        .then(records => {

            const record = records.find(
                r => r.attendance_id == id
            );

            if (!record) {
                alert("Attendance record not found.");
                return;
            }

            document.getElementById("attendance_id").value =
                record.attendance_id;

            memberSelect.value = record.member_id;

            document.getElementById("attendance_date").value =
                record.attendance_date;

            document.getElementById("check_in_time").value =
                record.check_in_time;

            document.getElementById("check_out_time").value =
                record.check_out_time || "";

            document.querySelector(".form-section h3").textContent =
                "Edit Attendance";
        });
}


// Delete Attendance
function deleteAttendance(id) {

    if (!confirm("Are you sure you want to delete this attendance record?")) {
        return;
    }

    fetch(`php/attendance.php?action=delete&id=${id}`)
        .then(response => response.json())
        .then(result => {

            alert(result.message);

            if (result.success) {
                loadAttendance();
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

    document.getElementById("attendance_id").value = "";

    document.querySelector(".form-section h3").textContent =
        "Record Attendance";
}


// Search
searchInput.addEventListener("input", function() {
    loadAttendance();
});


// Initial Load
loadMembers();
loadAttendance();