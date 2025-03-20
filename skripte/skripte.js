
function toggleDescription(id) {
    var description = document.getElementById(id);
    if (description.style.display === "none" || description.style.display === "") {
        description.style.display = "block";
    } else {
        description.style.display = "none";
    }
}

function openModal(articleId) {
    var modal = document.getElementById('myModal' + articleId);
    modal.style.display = "block";
}

function closeModal(articleId) {
    var modal = document.getElementById('myModal' + articleId);
    modal.style.display = "none";
    }

// Schließen des Modalfensters, wenn der Benutzer außerhalb des Modalfensters klickt
window.onclick = function(event) {
    var modals = document.getElementsByClassName('modal');
    for (var i = 0; i < modals.length; i++) {
        if (event.target == modals[i]) {
            modals[i].style.display = "none";
        }
    }
}