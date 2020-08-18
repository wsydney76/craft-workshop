<?php
return [
    'suggestion_new_body' => 'Hello {{suggestion.name}},
   
We received your suggestion:

ID: {{ suggestion.id }}

Title: {{ suggestion.title }}

Description

{{ suggestion.description }}
   
<a href="{{ url }}">Follow Status</a>   
   
   ',

    'suggestion_updatestatus_body' => 'Hello {{suggestion.name}},
   
The status of your suggestions has changed to {{ statusLabel }}

ID: {{ suggestion.id }}

Title: {{ suggestion.title }}

Description:

{{ suggestion.description }}

Notes:

{{ suggestion.notes }}
   
Status Page
<a href="{{ url }}">Follow Status</a>   
   
{% if entryTitle %}
<a href="{{ entryUrl }}">{{ entryTitle }}</a>
{% endif %}   
   
   '


];
