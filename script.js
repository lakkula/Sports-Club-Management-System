function togglePasswordVisibility(inputId, buttonId) {
    var passwordInput = document.getElementById(inputId);
    var toggleBtn = document.getElementById(buttonId);

    if (!passwordInput || !toggleBtn) {
        return;
    }

    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        toggleBtn.textContent = "Hide";
    } else {
        passwordInput.type = "password";
        toggleBtn.textContent = "Show";
    }
}

function showValidationError(formElement, message) {
    if (!formElement) {
        return;
    }

    var existingAlert = document.getElementById("js-validation-alert");
    if (existingAlert) {
        existingAlert.remove();
    }

    var alertDiv = document.createElement("div");
    alertDiv.id = "js-validation-alert";
    alertDiv.className = "alert alert-error";
    alertDiv.textContent = message;

    formElement.parentNode.insertBefore(alertDiv, formElement);
    alertDiv.scrollIntoView({ behavior: "smooth", block: "nearest" });
}

function validateLoginForm() {
    var form = document.getElementById("loginForm");
    var username = document.getElementById("username").value.trim();
    var password = document.getElementById("password").value;

    if (username === "" || password === "") {
        showValidationError(form, "Please enter both your username and password.");
        return false;
    }
    return true;
}

// ADDED: client-side validation for register.php
function validateRegisterForm() {
    var form = document.getElementById("registerForm");
    var username = document.getElementById("username").value.trim();
    var password = document.getElementById("password").value;
    var confirmPassword = document.getElementById("confirm_password").value;

    if (username === "" || password === "" || confirmPassword === "") {
        showValidationError(form, "Please fill in all fields.");
        return false;
    }

    if (password.length < 6) {
        showValidationError(form, "Password must be at least 6 characters long.");
        return false;
    }

    if (password !== confirmPassword) {
        showValidationError(form, "Passwords do not match.");
        return false;
    }

    return true;
}

function toggleSidebar() {
    var sidebar = document.getElementById("sidebar");
    if (sidebar) {
        sidebar.classList.toggle("open");
    }
}

function validatePlayerForm() {
    var form = document.getElementById("playerForm");
    var fullName = document.getElementById("full_name").value.trim();
    var dob = document.getElementById("dob").value;
    var gender = document.getElementById("gender").value;
    var contact = document.getElementById("contact").value.trim();
    var email = document.getElementById("email").value.trim();
    var sport = document.getElementById("sport").value.trim();
    var joinDate = document.getElementById("join_date").value;

    if (fullName === "" || dob === "" || gender === "" || contact === "" || email === "" || sport === "" || joinDate === "") {
        showValidationError(form, "Please complete all required player fields marked with *.");
        return false;
    }

    if (contact.length < 7) {
        showValidationError(form, "Please enter a valid contact number (at least 7 characters).");
        return false;
    }

    if (email.indexOf("@") === -1 || email.indexOf(".") === -1) {
        showValidationError(form, "Please enter a valid email address.");
        return false;
    }

    return true;
}

function confirmDelete(recordType, recordName) {
    var message = "Are you sure you want to delete this " + recordType + ": \"" + recordName + "\"? This action cannot be undone.";
    return confirm(message);
}
function validateEventForm() {
    var form = document.getElementById("eventForm");
    var eventName = document.getElementById("event_name").value.trim();
    var sport = document.getElementById("sport").value.trim();
    var eventDate = document.getElementById("event_date").value;
    var startTime = document.getElementById("start_time").value;
    var location = document.getElementById("location").value.trim();
    var status = document.getElementById("status").value;

    if (eventName === "" || sport === "" || eventDate === "" || startTime === "" || location === "" || status === "") {
        showValidationError(form, "Please complete all required event fields marked with *.");
        return false;
    }

    return true;
}

function validateTeamForm() {
    var form = document.getElementById("teamForm");
    var teamName = document.getElementById("team_name").value.trim();
    var sport = document.getElementById("sport").value.trim();
    var category = document.getElementById("category").value;
    var coachName = document.getElementById("coach_name").value.trim();

    if (teamName === "" || sport === "" || category === "" || coachName === "") {
        showValidationError(form, "Please complete all required team fields marked with *.");
        return false;
    }

    return true;
}

document.addEventListener("DOMContentLoaded", function () {
    var successAlerts = document.querySelectorAll(".alert-success");
    if (successAlerts.length > 0) {
        setTimeout(function () {
            for (var i = 0; i < successAlerts.length; i++) {
                successAlerts[i].style.transition = "opacity 0.5s ease";
                successAlerts[i].style.opacity = "0";
                (function (el) {
                    setTimeout(function () {
                        el.style.display = "none";
                    }, 500);
                })(successAlerts[i]);
            }
        }, 4000);
    }
});