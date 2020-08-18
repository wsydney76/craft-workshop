<?php

return [
    'A user must be assigned for status At Work' => 'Ein Benutzer muss bei diesem Status zugewiesen werden',
    'Accept' => 'Akzeptieren',
    'Accepted' => 'Akzeptiert',
    'Actions' => 'Aktionen',
    'All Suggestions' => 'Alle Vorschläge',
    'Are you sure you want to delete this suggestion?' => 'Sind Sie sicher, dass Sie diesen Vorschlag löschen wollen?',
    'Assign to me' => 'Mir zuweisen',
    'Assigned User' => 'Zugeordneter Benutzer',
    'At Work' => 'In Arbeit',
    'By Status' => 'Nach Status',
    'Create PDF file' => 'PDF-Datei erstellen',
    'Could not save status update' => 'Status-Update konnte nicht gespeichert werden',
    'Created from site' => 'Erstellt auf Site',
    'Date' => 'Datum',
    'Delete' => 'Löschen',
    'Delete permanently' => 'Endgültig löschen',
    'Delete suggestion' => 'Vorschlag löschen',
    'deleted' => 'gelöscht',
    'Deleted (max {days} days)' => 'Gelöscht (max. {days} Tage)',
    'Description' => 'Beschreibung',
    'Editor' => 'Bearbeiter',
    'Email' => 'E-Mail',
    'Entry' => 'Eintrag',
    'Film Title' => 'Filmtitel',
    'HTML Tags are not allowed here' => 'HTML Tags sind nicht erlaubt.',
    'My Work' => 'Meine Aufgaben',
    'Notes' => 'Anmerkungen',
    'Nothing has changed' => 'Es hat sich nichts geändert',
    'Nothing found' => 'Nichts gefunden',
    'on' => 'am',
    'Open' => 'Offen',
    'Page' => 'Seite',
    'Reject' => 'Ablehnen',
    'Rejected' => 'Abgelehnt',
    'Restore' => 'Wiederherstellen',
    'Search in Title, Name, Description, Notes' => 'In Titel, Name, Beschreibung und Anmerkungen suchen',
    'Status History' => 'Statushistorie',
    'Status Page' => 'Status-Seite',
    'Status update saved' => 'Status-Update gespeichert',
    'Suggested by' => 'Vorgeschlagen von',
    'Suggestion' => 'Vorschlag',
    'Suggestions' => 'Vorschläge',
    'This user does not have sufficient permissions' => 'Dieser Benutzer hat keine ausreichenden Rechte',
    'Title' => 'Titel',
    'Thank you for your suggestion.' => 'Vielen Dank für Ihren Vorschlag',
    'Trash' => 'Papierkorb',
    'Update Status' => 'Status aktualisieren',
    'User' => 'Benutzer',
    'We could not save your suggestion.' => 'Wir konnten Ihren Vorschlag nicht speichern.',

    'If a new suggestion is created' => 'Wenn ein neuer Vorschlag erstellt wird',
    'If the status of a suggestion changes' => 'Wenn sich der Status eines Vorschlags ändert',
    'New Suggestion: {{ suggestion.title }}' => 'Neuer Vorschlag: {{ suggestion.title }}',
    'New Suggestion Status: {{ suggestion.title }} {{ statusLabel }}' => 'Neuer Vorschlags-Status: {{ suggestion.title }} {{ statusLabel }}',
    'suggestion_new_body' => 'Hallo {{suggestion.name}},
   
Wir haben Ihren Vorschlag erhalten:

ID: {{ suggestion.id }}

Titel: {{ suggestion.title }}

Beschreibung:
{{ suggestion.description }}


<a href="{{ url }}">Zur Status-Seite</a>

   ',

    'suggestion_updatestatus_body' => 'Hello {{suggestion.name}},
   
Der Status Ihres Vorschlags hat sich auf {{ statusLabel }} geändert.

ID: {{ suggestion.id }}

Titel: {{ suggestion.title }}

Beschreibung:

{{ suggestion.description }}

Anmerkungen:

{{ suggestion.notes }}
   
Status Page
<a href="{{ url }}">Zur Status-Seite</a>   
   
{% if entryTitle %}
<a href="{{ entryUrl }}">{{ entryTitle }}</a>
{% endif %}   
   
   '
];
