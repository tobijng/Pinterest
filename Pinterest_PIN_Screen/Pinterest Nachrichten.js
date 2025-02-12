//User und pin-section
const userID = "123" //Platzhalter für DB
const pinID = "bild-123" //Platzhalter für DB


//Uploader-section

const uploaderData = {
    username: "Artur Kostbar", //Platzhalter für DB
    profilePic: "Test.jpg"
};

const uploaderName = document.querySelector('.uploader-username');
const uploaderPic = document.querySelector('.uploader-profile-pic');

uploaderName.textContent = uploaderData.username;
uploaderPic.src = uploaderData.profilePic;

//merken-section

const merkenButton = document.querySelector("merken-button");

document.querySelectorAll(".merken-button").forEach(button => {
    button.addEventListener("click", function() {
        fetch("/api/merken", {
            method: "POST",
            headers: {"Content-Type": "application/json" },
            body: JSON.stringify({ userID, pinID })
        })
        .then(response => response.json())
        .then(data => {
            if (data.gemerkt) {
                button.textContent = "Gemerkt";
                button.style.backgroundColor = "darkred";
            } else {
                button.textContent = "Merken";
                button.style.backgroundColor = "red";
            }
        })
        .catch(error => console.error("Fehler:", error));
    });
});


//comment-section

const submitButton = document.querySelector('.submit');
const inputField = document.querySelector('.typedMessage');
inputField.addEventListener('input', function() {
    if (inputField.value.trim() !== '') {
        submitButton.style.display = 'inline-block';
    } else {
        submitButton.style.display = 'none';
    }
});

document.querySelector('.comment').addEventListener('submit', function(event) {
    event.preventDefault();

    
    const commentContainer = document.querySelector('.comments');

    if (inputField.value.trim() !== '') {
        const newComment = document.createElement('div');
        newComment.classList.add('comment-item');
        
        const commentHeader = document.createElement('div');
        commentHeader.classList.add('comment-header');

        const profilePic = document.createElement('img');
        profilePic.classList.add('comment-profile-pic');
        profilePic.src = "default-profile.png"; //Platzhalter für DB

        const username = document.createElement('span');
        username.classList.add('comment-username');
        username.textContent = "Benutzername"; // Platzhalter für DB

        commentHeader.appendChild(profilePic);
        commentHeader.appendChild(username);

        const commentText = document.createElement('span');
        commentText.classList.add('comment-text');
        commentText.textContent = inputField.value;
        
        const likeContainer = document.createElement('div');
        likeContainer.classList.add('comment-like-container');

        const likeButton = document.createElement('button');
        likeButton.classList.add('comment-likeButton');
        likeButton.innerHTML = `
        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 21C12 21 4 13.5 4 8.5C4 5.5 6.5 3 9 3C10.5 3 12 4 12 4C12 4 13.5 3 15 3C17.5 3 20 5.5 20 8.5C20 13.5 12 21 12 21Z"/>
        </svg>
    `;

        const likeCount = document.createElement('span');
        likeCount.textContent = '0';
        likeCount.classList.add('comment-like-count');

        let hasLiked = false;
        likeButton.addEventListener('click', function () {
            let currentLikes = parseInt(likeCount.textContent);
            if (hasLiked) {
                likeCount.textContent = currentLikes - 1;
                likeButton.classList.remove('liked');
            } else {
                likeCount.textContent = currentLikes + 1;
                likeButton.classList.add('liked');
            }
            hasLiked = !hasLiked;
        });

        likeContainer.appendChild(likeButton);
        likeContainer.appendChild(likeCount);

        newComment.appendChild(commentHeader);
        newComment.appendChild(commentText);
        newComment.appendChild(likeContainer);
        commentContainer.appendChild(newComment);

        inputField.value = '';
        document.querySelector('.submit').style.display = 'none';
    
    }
});
//Kommentare ein- und ausblenden
const commentArrow = document.querySelector('.comment-arrow');
        const commentsDiv = document.querySelector('.comments');

        commentArrow.addEventListener('click', function() {
            if (commentsDiv.style.display === 'none' || commentsDiv.style.display === '') {
                commentsDiv.style.display = 'block';
                commentArrow.classList.add('expanded');
            } else {
                commentsDiv.style.display = 'none';
                commentArrow.classList.remove('expanded');
            }
        });

       
        

//like-section

let likeCount = localStorage.getItem("likes") ? parseInt(localStorage.getItem("likes")) : 0;
let hasLiked = localStorage.getItem("hasLiked") === "true";
        const likeButton = document.getElementById("likeButton");
        const likeCountDisplay = document.getElementById("likeCount");
        const heartPath = likeButton.querySelector("path");

        likeCountDisplay.textContent = `${likeCount}`;

        if (hasLiked) {
            likeButton.classList.add("liked");
        }

        likeButton.addEventListener("click", function() {
            if (hasLiked) {
                likeCount--;
                localStorage.setItem("hasLiked", "false");
                likeButton.classList.remove("liked");
            } else {
            likeCount++;
            localStorage.setItem("hasLiked","true");
            likeButton.classList.add("liked");
            }

            hasLiked = !hasLiked;
            likeCountDisplay.textContent = `${likeCount}`;
            localStorage.setItem("likes", likeCount);
        });

//share-section

document.addEventListener("DOMContentLoaded", function () {
    const shareBtn = document.querySelector(".share-button");
    const shareContainer = document.querySelector(".share-container");

    shareBtn.addEventListener("click", function() {
        shareContainer.classList.toggle("active");
    });

    document.addEventListener("click", function (event) {
        if(!shareContainer.contains(event.target)) {
            shareContainer.classList.remove("active");
        }
    });
});

function shareOn(platform) {
    const pageUrl = encodeURIComponent(window.location.href);
    let shareUrl = "";

    switch (platform) {
        case "facebook":
            shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${pageUrl}`;
            break;
        case "x":
            shareUrl = `https://twitter.com/intent/tweet?url=${pageUrl}`;
            break;
        case "whatsapp":
            shareUrl = `https://api.whatsapp.com/send?text=${pageUrl}`;
            break;
        default:
            return;
    }

    window.open(shareUrl, "_blank");
}
        //dots-section

        document.querySelector('.dots-btn').addEventListener('click', function(event) {
            event.stopPropagation();
            const menu = document.querySelector('.dots-menu');

            if (menu.style.display == 'block') {
                menu.style.display = 'none';
            } else {
                menu.style.display = 'block';
            }
        });

        document.addEventListener('click', function(event) {
            const menu = document.querySelector('.dots-menu');
            const dotsButton = document.querySelector('.dots-btn');

            if (!dotsButton.contains(event.target) && !menu.contains(event.target)) {
                menu.style.display = 'none';
            }
        });

        const dotsBtn = document.querySelector(".dots-btn");

        dotsBtn.addEventListener("click", function () {
            dotsBtn.classList.toggle("active");
        });


        document.getElementById('download-button').addEventListener('click', function() {
            alert('Bild wird heruntergeladen...');
            window.location.href = "download.php";
            // Hier könnte ein echter Download-Link eingefügt werden
        });
        
        document.getElementById('hide-pin-button').addEventListener('click', function() {
            alert('Pin wird ausgeblendet...');
            // Hier könnte der Pin per JavaScript versteckt werden
        });
        
        document.getElementById('report-pin-button').addEventListener('click', function() {
            alert('Pin wurde gemeldet!');
            // Hier könnte eine Meldefunktion implementiert werden
        });

        
