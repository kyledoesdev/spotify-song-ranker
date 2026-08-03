<x-error-page
    :code="$exception->getStatusCode()"
    heading="Something went wrong 🫤"
    message="Something went wrong on our end — not yours. Kyle has been notified and is looking into it."
    :show-support="true"
/>

