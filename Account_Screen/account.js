document.addEventListener("DOMContentLoaded", function () {
    const fileInput = document.getElementById("file-input");
    const profileContainer = document.getElementById("profile-container");
    const profilePicture = document.getElementById("profile-picture");

    profileContainer.addEventListener("click", () => {
        fileInput.click();
    });

    fileInput.addEventListener("change", function (event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                profilePicture.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
});
