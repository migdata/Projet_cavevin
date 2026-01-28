<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des Stocks - CaveVin20</title>

    <!-- Bootstrap 5.3.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- FullCalendar 6 -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>

    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }
        .container {
            max-width: 1200px;
            padding: 2rem 1rem;
        }
        #calendar {
            background: white;
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
        }
        .fc .fc-toolbar-title {
            font-size: 1.5rem;
            font-weight: 600;
        }
        .fc-event {
            border: none;
            padding: 2px 6px;
            font-size: 0.9rem;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .fc-event:hover {
            opacity: 0.92;
            transform: translateY(-1px);
        }
        .fc-event-stock-add {
            background-color: #d4edda !important;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        .fc-event-stock-remove {
            background-color: #f8d7da !important;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        .fc-event-title {
            white-space: normal;
        }
        .legend {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
        }
        @media (max-width: 768px) {
            .fc .fc-toolbar {
                flex-direction: column;
                gap: 0.75rem;
            }
            .fc .fc-toolbar-chunk {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <!-- Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
            <h1 class="h3 mb-0 fw-bold text-primary">
                Historique des Stocks
            </h1>
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Retour au stock
            </a>
        </div>

        <!-- Légende -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <div class="legend">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-success-subtle text-success px-3 py-2">+ Ajout de stock</span>
                        <span class="badge bg-danger-subtle text-danger px-3 py-2">- Retrait de stock</span>
                    </div>
                    <div class="text-muted small">
                        Cliquez sur un événement pour voir les détails
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendrier -->
        <div class="card shadow border-0">
            <div class="card-body p-0">
                <div id="calendar"></div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const calendarEl = document.getElementById('calendar');
            const eventsData = @json($events ?? []);

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'fr',
                timeZone: 'Europe/Paris',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,listMonth'
                },
                buttonText: {
                    today: "Aujourd'hui",
                    month: 'Mois',
                    week: 'Semaine',
                    list: 'Liste'
                },
                events: eventsData.map(event => {
                    // On enrichit les événements avec des classes CSS personnalisées
                    let classNames = [];
                    let title = event.title || 'Mouvement de stock';

                    if (event.extendedProps?.delta > 0) {
                        classNames.push('fc-event-stock-add');
                        title = `+${event.extendedProps.delta} • ${title}`;
                    } else if (event.extendedProps?.delta < 0) {
                        classNames.push('fc-event-stock-remove');
                        title = `${event.extendedProps.delta} • ${title}`;
                    }

                    return {
                        ...event,
                        title,
                        classNames,
                        extendedProps: {
                            ...event.extendedProps,
                            originalTitle: event.title
                        }
                    };
                }),
                eventDidMount: function(info) {
                    // Tooltip Bootstrap sur chaque événement
                    const tooltip = new bootstrap.Tooltip(info.el, {
                        title: info.event.extendedProps?.details || info.event.title,
                        placement: 'top',
                        trigger: 'hover',
                        container: 'body'
                    });
                },
                eventClick: function(info) {
                    // Optionnel : ouvrir une modal avec plus de détails
                    alert(
                        `Produit: ${info.event.title}\n` +
                        `Date: ${info.event.start.toLocaleString('fr-FR')}\n` +
                        `Variation: ${info.event.extendedProps?.delta || '?'} unités\n` +
                        (info.event.extendedProps?.user ? `Par: ${info.event.extendedProps.user}\n` : '') +
                        (info.event.extendedProps?.details ? `Détails: ${info.event.extendedProps.details}` : '')
                    );
                    // Tu peux remplacer par une vraie modal Bootstrap si tu veux
                },
                eventTimeFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                },
                height: 'auto',
                contentHeight: 'auto',
                aspectRatio: 1.35,
                editable: false,
                selectable: false
            });

            calendar.render();
        });
    </script>

    <!-- Bootstrap JS (pour les tooltips) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>
</html>