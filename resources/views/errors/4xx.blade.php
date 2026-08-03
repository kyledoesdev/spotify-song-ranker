<x-error-page
    :code="$exception->getStatusCode()"
    icon="fa-solid fa-circle-question"
    heading="Something went wrong 🫤"
    message="Something about that request didn't quite work. Try again, or head back somewhere familiar."
/>
