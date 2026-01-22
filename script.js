// Confirm deletion
document.addEventListener("DOMContentLoaded", () => {
    const deleteLinks = document.querySelectorAll("a.delete");
    deleteLinks.forEach(link => {
        link.addEventListener("click", (e) => {
            if (!confirm("Are you sure you want to delete this item?")) {
                e.preventDefault();
            }
        });
    });
});

// Optional: highlight selected row
const tableRows = document.querySelectorAll("table tbody tr");
tableRows.forEach(row => {
    row.addEventListener("mouseover", () => row.style.backgroundColor = "#d1ecf1");
    row.addEventListener("mouseout", () => row.style.backgroundColor = "");
});

// Optional: form validation
const forms = document.querySelectorAll("form");
forms.forEach(form => {
    form.addEventListener("submit", (e) => {
        const requiredInputs = form.querySelectorAll("input[required], select[required]");
        let valid = true;
        requiredInputs.forEach(input => {
            if (!input.value.trim()) valid = false;
        });
        if (!valid) {
            e.preventDefault();
            alert("Please fill in all required fields.");
        }
    });
});

