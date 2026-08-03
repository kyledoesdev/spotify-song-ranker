<x-error-page
    code="503"
    heading="Currently undergoing maintenance"
    message="{{ config('app.name') }} is down for maintenance. We'll be back shortly. Your rankings are safe and sound."
    :show-support="true"
/>
