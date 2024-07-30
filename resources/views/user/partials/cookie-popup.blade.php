@if (!isset($_COOKIE['cookies_accepted']))
    <div id="cookie-popup" class="cookie-popup bod">
        <p>This website uses cookies to ensure you get the best experience on our website.</p>
        <button id="accept-cookies" class="acc btn">Accept</button>
    </div>
@endif
<style>
    .acc {
        background-color: #1575b8;
        color: white;
    }

    .acc:hover {
        background-color: lightblue;
    }

    .cookie-popup {
        position: fixed;
        /* Fixed positioning keeps it in place even when scrolling */
        bottom: 5px;
        /* Adjust as needed */
        left: 50%;
        /* Center horizontally */
        transform: translateX(-50%);
        /* Center horizontally */
        background-color: #f0f0f0;
        border: 1px solid #ccc;
        border-radius: 5px;
        padding: 15px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        z-index: 9999;
        /* Ensure it's above other content */
        width: 90%;
        /* Adjust the width as needed */
        max-width: 400px;
        /* Limit the width for smaller screens */
        text-align: center;
    }

    .cookie-popup p {
        margin-bottom: 10px;
    }

    .cookie-popup button {
        cursor: pointer;
    }
</style>
