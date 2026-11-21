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