const API = "./backend/api/comments.php"; // adapte le chemin si nécessaire
const form = document.getElementById("form");
const commentsDiv = document.getElementById("comments");
const commentIdInput = document.getElementById("commentId");

// ----------------------------------
// 1️⃣ Charger et afficher les commentaires
// ----------------------------------
function chargerCommentaires() {
    fetch(API)
        .then(res => res.json())
        .then(data => {
            commentsDiv.innerHTML = "";
            data.forEach(c => {
                const div = document.createElement("div");

                const contenuDiv = document.createElement("span");
                contenuDiv.textContent = `${c.auteur} : ${c.contenu} (${c.date_creation})`;

                const btnEdit = document.createElement("button");
                btnEdit.textContent = "Modifier";
                btnEdit.addEventListener("click", () => {
                    commentIdInput.value = c.id;
                    document.getElementById("nom").value = c.auteur;
                    document.getElementById("message").value = c.contenu;
                });

                const btnDelete = document.createElement("button");
                btnDelete.textContent = "Supprimer";
                btnDelete.addEventListener("click", () => {
                    if(confirm("Supprimer ce commentaire ?")) {
                        fetch(API, {
                            method: "DELETE",
                            headers: { "Content-Type": "application/json" },
                            body: JSON.stringify({ id: c.id })
                        }).then(() => chargerCommentaires());
                    }
                });

                div.appendChild(contenuDiv);
                div.appendChild(document.createElement("br"));
                div.appendChild(btnEdit);
                div.appendChild(btnDelete);

                commentsDiv.appendChild(div);
            });
        })
        .catch(err => console.error("Erreur fetch:", err));
}

// ----------------------------------
// 2️⃣ Gestion du formulaire (création ou modification)
// ----------------------------------
form.addEventListener("submit", e => {
    e.preventDefault();
    const auteur = document.getElementById("nom").value;
    const contenu = document.getElementById("message").value;
    const id = commentIdInput.value;

    if(id) {
        // Modification
        fetch(API, {
            method: "PUT",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id, contenu })
        }).then(() => {
            form.reset();
            commentIdInput.value = "";
            chargerCommentaires();
        });
    } else {
        // Nouveau commentaire
        fetch(API, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ auteur, contenu })
        }).then(() => {
            form.reset();
            chargerCommentaires();
        });
    }
});

// ----------------------------------
// 3️⃣ Charger les commentaires dès le chargement
// ----------------------------------
chargerCommentaires();