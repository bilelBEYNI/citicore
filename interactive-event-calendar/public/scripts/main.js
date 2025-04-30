document.addEventListener('DOMContentLoaded', () => {
    const app = document.getElementById('app');
    let currentDate = new Date();
    let events = [];

    // Fonction pour récupérer les événements depuis l'API
    async function fetchEvents() {
        try {
            const response = await fetch('/api/events');
            events = await response.json();
            renderCalendar();
        } catch (error) {
            console.error('Erreur lors de la récupération des événements:', error);
            // Données de test en cas d'erreur
            events = [
                {
                    id: 1,
                    titre: 'Match de Football',
                    date: '2025-05-14',
                    heure: '15:00',
                    lieu: 'Stade Municipal',
                    categorie: 'sport',
                    description: 'Match de championnat local'
                },
                {
                    id: 2,
                    titre: 'Concert de Jazz',
                    date: '2025-05-21',
                    heure: '20:00',
                    lieu: 'Salle de Concert',
                    categorie: 'culture',
                    description: 'Soirée jazz avec artistes locaux'
                },
                {
                    id: 3,
                    titre: 'Conférence Business',
                    date: '2025-05-28',
                    heure: '09:00',
                    lieu: 'Centre des Congrès',
                    categorie: 'business',
                    description: 'Conférence sur l\'innovation'
                }
            ];
            renderCalendar();
        }
    }

    // Fonction pour générer le calendrier
    function renderCalendar() {
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();
        
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const daysInMonth = lastDay.getDate();
        const startingDay = firstDay.getDay();
        
        const monthNames = ['JANVIER', 'FÉVRIER', 'MARS', 'AVRIL', 'MAI', 'JUIN', 'JUILLET', 'AOÛT', 'SEPTEMBRE', 'OCTOBRE', 'NOVEMBRE', 'DÉCEMBRE'];
        const weekDays = ['LUNDI', 'MARDI', 'MERCREDI', 'JEUDI', 'VENDREDI', 'SAMEDI', 'DIMANCHE'];

        // Créer la structure du calendrier
        const calendar = document.createElement('div');
        calendar.className = 'calendar';

        // En-tête du calendrier
        const header = document.createElement('div');
        header.className = 'calendar-header';
        header.innerHTML = `
            <h2>${monthNames[month]} ${year}</h2>
        `;

        // Grille des jours de la semaine
        const weekdayHeader = document.createElement('div');
        weekdayHeader.className = 'weekday-header';
        weekDays.forEach(day => {
            const weekday = document.createElement('div');
            weekday.className = 'weekday';
            weekday.textContent = day;
            weekdayHeader.appendChild(weekday);
        });

        // Grille des jours
        const grid = document.createElement('div');
        grid.className = 'calendar-grid';

        // Ajuster le jour de début (lundi = 0)
        const adjustedStartingDay = startingDay === 0 ? 6 : startingDay - 1;

        // Jours du mois précédent
        const prevMonth = new Date(year, month, 0);
        const prevMonthLastDay = prevMonth.getDate();
        for (let i = adjustedStartingDay - 1; i >= 0; i--) {
            const day = document.createElement('div');
            day.className = 'day other-month';
            const dayNumber = prevMonthLastDay - i;
            day.innerHTML = `<div class="day-number">${dayNumber}</div>`;
            grid.appendChild(day);
        }

        // Jours du mois actuel
        for (let i = 1; i <= daysInMonth; i++) {
            const day = document.createElement('div');
            day.className = 'day';
            
            // Vérifier si c'est aujourd'hui
            const currentDay = new Date();
            if (i === currentDay.getDate() && month === currentDay.getMonth() && year === currentDay.getFullYear()) {
                day.classList.add('today');
            }

            // Ajouter le numéro du jour
            day.innerHTML = `<div class="day-number">${i}</div>`;

            // Vérifier s'il y a des événements ce jour-là
            const dayEvents = events.filter(event => {
                const eventDate = new Date(event.date);
                return eventDate.getDate() === i && eventDate.getMonth() === month && eventDate.getFullYear() === year;
            });

            if (dayEvents.length > 0) {
                const eventList = document.createElement('div');
                eventList.className = 'event-list';
                
                dayEvents.forEach(event => {
                    const eventDiv = document.createElement('div');
                    eventDiv.className = `calendar-event ${event.categorie}`;
                    eventDiv.innerHTML = `
                        <span class="event-time">${event.heure}</span>
                        ${event.titre}
                    `;
                    eventDiv.addEventListener('click', () => showEventDetails(event));
                    eventList.appendChild(eventDiv);
                });
                
                day.appendChild(eventList);
            }

            grid.appendChild(day);
        }

        // Calculer les jours du mois suivant
        const totalDays = adjustedStartingDay + daysInMonth;
        const remainingDays = 42 - totalDays; // 6 semaines * 7 jours = 42

        for (let i = 1; i <= remainingDays; i++) {
            const day = document.createElement('div');
            day.className = 'day other-month';
            day.innerHTML = `<div class="day-number">${i}</div>`;
            grid.appendChild(day);
        }

        // Assembler le calendrier
        calendar.appendChild(header);
        calendar.appendChild(weekdayHeader);
        calendar.appendChild(grid);

        // Vider et remplir le conteneur
        app.innerHTML = '';
        app.appendChild(calendar);
    }

    // Fonction pour afficher les détails d'un événement
    function showEventDetails(event) {
        const modal = document.createElement('div');
        modal.className = 'event-modal';
        modal.style.display = 'block';

        const dateStr = new Date(event.date).toLocaleDateString('fr-FR', { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        });

        modal.innerHTML = `
            <div class="modal-content">
                <h3>${event.titre}</h3>
                <p><strong>Date:</strong> ${dateStr}</p>
                <p><strong>Heure:</strong> ${event.heure}</p>
                <p><strong>Lieu:</strong> ${event.lieu}</p>
                <p><strong>Catégorie:</strong> ${event.categorie}</p>
                <p>${event.description}</p>
                <button class="btn-close">×</button>
            </div>
        `;

        const overlay = document.createElement('div');
        overlay.className = 'modal-overlay';
        overlay.style.display = 'block';

        document.body.appendChild(overlay);
        document.body.appendChild(modal);

        const closeModal = () => {
            modal.remove();
            overlay.remove();
        };

        modal.querySelector('.btn-close').addEventListener('click', closeModal);
        overlay.addEventListener('click', closeModal);
    }

    // Initialiser le calendrier
    fetchEvents();
});