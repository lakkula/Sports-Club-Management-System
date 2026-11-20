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