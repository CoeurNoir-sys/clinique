document.addEventListener('DOMContentLoaded', function () {

    // --- Sélection d'un créneau horaire (page prendre-rdv.php) ---
    const creneauBtns = document.querySelectorAll('.creneau-btn');
    const heureInput = document.getElementById('heure_rdv');

    creneauBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            creneauBtns.forEach(b => b.classList.remove('selected'));
            this.classList.add('selected');
            if (heureInput) heureInput.value = this.dataset.heure;
            const boutonValider = document.getElementById('btn-valider-rdv');
            if (boutonValider) boutonValider.disabled = false;
        });
    });

    // --- Rechargement des créneaux quand médecin ou date change ---
    const selectMedecin = document.getElementById('medecin_id');
    const inputDate = document.getElementById('date_rdv');
    const zoneCreneaux = document.getElementById('zone-creneaux');

    function chargerCreneaux() {
        if (!selectMedecin || !inputDate) return;
        const medecinId = selectMedecin.value;
        const date = inputDate.value;
        if (!medecinId || !date) return;

        zoneCreneaux.innerHTML = '<p class="text-muted">Chargement des créneaux...</p>';

        fetch('ajax_creneaux.php?medecin_id=' + encodeURIComponent(medecinId) + '&date=' + encodeURIComponent(date))
            .then(res => res.json())
            .then(data => {
                zoneCreneaux.innerHTML = '';
                if (data.creneaux.length === 0) {
                    zoneCreneaux.innerHTML = '<p class="text-danger">Aucun créneau disponible pour cette date.</p>';
                    return;
                }
                data.creneaux.forEach(c => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'btn btn-outline-secondary creneau-btn';
                    btn.dataset.heure = c;
                    btn.textContent = c.substring(0, 5);
                    btn.addEventListener('click', function () {
                        document.querySelectorAll('.creneau-btn').forEach(b => b.classList.remove('selected'));
                        this.classList.add('selected');
                        if (heureInput) heureInput.value = this.dataset.heure;
                        const boutonValider = document.getElementById('btn-valider-rdv');
                        if (boutonValider) boutonValider.disabled = false;
                    });
                    zoneCreneaux.appendChild(btn);
                });
            })
            .catch(() => {
                zoneCreneaux.innerHTML = '<p class="text-danger">Erreur lors du chargement des créneaux.</p>';
            });
    }

    if (selectMedecin) selectMedecin.addEventListener('change', chargerCreneaux);
    if (inputDate) inputDate.addEventListener('change', chargerCreneaux);

    // --- Validation simple des mots de passe (inscription) ---
    const formInscription = document.getElementById('form-inscription');
    if (formInscription) {
        formInscription.addEventListener('submit', function (e) {
            const mdp = document.getElementById('mot_de_passe').value;
            const confirmation = document.getElementById('confirmation').value;
            if (mdp !== confirmation) {
                e.preventDefault();
                alert('Les mots de passe ne correspondent pas.');
            }
            if (mdp.length < 6) {
                e.preventDefault();
                alert('Le mot de passe doit contenir au moins 6 caractères.');
            }
        });
    }

    // --- Confirmation avant annulation d'un rendez-vous ---
    document.querySelectorAll('.btn-annuler-rdv').forEach(btn => {
        btn.addEventListener('click', function (e) {
            if (!confirm('Voulez-vous vraiment annuler ce rendez-vous ?')) {
                e.preventDefault();
            }
        });
    });

});
