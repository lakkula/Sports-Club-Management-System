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