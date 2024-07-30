$(document).ready(function () {
    // Check if the user has already accepted cookies
    if (!getCookie('cookies_accepted')) {
        $('#cookie-popup').show();
    }

    // When the user clicks the accept button, set a cookie and hide the pop-up
    $('#accept-cookies').click(function () {
        setCookie('cookies_accepted', 'true', 365); // Cookie expiration set to 365 days
        $('#cookie-popup').hide();
    });

    // Function to set a cookie
    function setCookie(name, value, days) {
        var expires = '';
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = '; expires=' + date.toUTCString();
        }
        document.cookie = name + '=' + value + expires + '; path=/';
    }

    // Function to get a cookie
    function getCookie(name) {
        var nameEQ = name + '=';
        var cookies = document.cookie.split(';');
        for (var i = 0; i < cookies.length; i++) {
            var cookie = cookies[i];
            while (cookie.charAt(0) === ' ') {
                cookie = cookie.substring(1, cookie.length);
            }
            if (cookie.indexOf(nameEQ) === 0) {
                return cookie.substring(nameEQ.length, cookie.length);
            }
        }
        return null;
    }
});
