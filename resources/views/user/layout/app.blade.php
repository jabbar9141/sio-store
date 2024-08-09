<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>{{ env('APP_NAME') }} - @yield('page_name')</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="description"
        content="Discover high-quality products at Siostore, your trusted online store in Italy. Shop now for the best deals on fashion, electronics, home goods, and more.">
    <meta name="keywords"
        content="Siostore, online store Italy, Italian online shop, Siopay Mutiservisi SRL, fashion, electronics, home goods, Italy shopping">

    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">

    <!-- Libraries Stylesheetssss -->
    <link href="{{ asset('user_assets/lib/animate/animate.min.css') }}" rel="stylesheet">
    <link href="{{ asset('user_assets/lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link href="{{ asset('user_assets/css/main.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/3.4.1/css/swiper.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" />
    <!-- Include SweetAlert CSS and JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">

    <style>
        body {
            font-family: "Poppins", sans-serif;
        }

        /* Topbar search for shipping locations */
        #location_suggestions {
            position: absolute;
            background-color: #f1f1f1;
            /* Light gray background color */
            border: 1px solid #ccc;
            /* Optional: Add a border */
            z-index: 1000;
            /* Ensure suggestions appear above other content */
            max-height: 200px;
            /* Optional: Limit height of suggestions */
            overflow-y: auto;
            /* Optional: Add scrollbar if suggestions overflow */
        }

        .location_suggestion {
            padding: 8px;
            cursor: pointer;
        }

        .location_suggestion:hover {
            background-color: #e5e5e5;
            /* Light gray background color on hover */
        }

        /* Topbar search for shipping locations mobile*/
        #location_suggestions_m {
            position: absolute;
            background-color: #f1f1f1;
            /* Light gray background color */
            border: 1px solid #ccc;
            /* Optional: Add a border */
            z-index: 1000;
            max-height: 200px;
            overflow-y: auto;
        }

        /* product cards */
        .card {
            border: none;
        }

        .card-img-top {
            max-height: 200px !important;
            object-fit: cover;
        }

        .product-img {
            height: 145px !important;
            object-fit: cover;
        }

        /* product card names */
        a {
            word-wrap: break-word !important;
        }
    </style>

</head>

<body>
    {{-- alerts --}}
    @include('user.partials.notification')
    {{-- /alerts --}}

    <!-- Topbar Start -->
    @include('user.partials.topbar')
    <!-- Topbar End -->


    <!-- Navbar Start -->
    @include('user.partials.navbar')
    <!-- Navbar End -->


    <!-- Carousel Start -->
    @yield('content')


    <!-- Footer Start -->
    @include('user.partials.footer')
    <!-- Footer End -->

    @include('user.partials.cookie-popup')


    <!-- Back to Top -->
    <a href="#" class="btn btn-primary back-to-top"><i class="fa fa-angle-double-up"></i></a>


    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <script src="{{ asset('user_assets/lib/easing/easing.min.js') }}"></script>
    <script src="{{ asset('user_assets/lib/owlcarousel/owl.carousel.min.js') }}"></script>

    <!-- Contact Javascript File -->
    <script src="{{ asset('user_assets/mail/jqBootstrapValidation.min.js') }}"></script>
    <script src="{{ 'user_assets/mail/contact.js' }}"></script>
    <!-- Include your JavaScript file -->
    <script src="{{ asset('user_assets/js/cookie.js') }}"></script>

    <!-- Template Javascript -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/3.4.1/js/swiper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('user_assets/js/main.js') }}"></script>
    <script src="{{ asset('user_assets/js/app.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });

        @if (session('country_id'))
            getCities({{ (int) session('country_id') }})
        @endif

        $('.delivery_country').on('change', function() {
            if (this.id == 'delivery_country') {
                $('#m_countries').val(this.value).trigger('change');
            } else {
                $('#delivery_country').val(this.value).trigger('change');
            }
            $('#delivery_city').empty();
            $('#delivery_city_m').empty();
            if (this.value) {
                getCities(this.value)
            }
        });

        $('.delivery_city').on('change', function() {
            if (this.id == 'delivery_city_m') {
                $('#delivery_city').val(this.value).trigger('change');
            } else {
                $('#delivery_city_m').val(this.value).trigger('change');
            }
        });

        function getCities(country_id) {
            let options = [];
            $('#delivery_city').append(new Option('Select City', '', true, false));
            $('#delivery_city_m').append(new Option('Select City', '', true, false));
            // options.push(new Option('Select City', '', true, false));
            $.ajax({
                type: "get",
                url: "{{ route('delivery-city', ['country_id' => ':id']) }}".replace(':id', country_id),
                data: "",
                dataType: "json",
                success: function(response) {
                    let cities = response.cities;
                    if (cities.length > 0) {
                        cities.forEach(city => {
                            $('#delivery_city').append(new Option(city.name, city.id, false, false));
                            $('#delivery_city_m').append(new Option(city.name, city.id, false, false));
                        });
                    }
                    @if (session('country_id'))
                        let session_city_id = '{{ session('city_id') }}'
                        $('#delivery_city').val(session_city_id).prop('selected', true).trigger(
                            'change');
                        $('#delivery_city_m').val(session_city_id).prop('selected', true).trigger(
                            'change');
                    @endif
                }
            });
        }

        $(document).ready(function() {
            function fetchLocations(query, suggestionsContainer) {
                if (query !== '') {
                    $.ajax({
                        url: '/search-locations',
                        method: 'GET',
                        data: {
                            query: query
                        },
                        success: function(response) {
                            $(suggestionsContainer).empty();
                            response.forEach(function(location) {
                                $(suggestionsContainer).append(
                                    '<a href="/set/ship/loc/' + location.id +
                                    '" class="location_suggestion text-gray">' +
                                    location.name + ', ' + location.country_code + '</a>'
                                );
                            });
                        }
                    });
                }
            }

            // Attach keyup event for desktop
            $('#search_location').keyup(function() {
                fetchLocations($(this).val(), '#location_suggestions');
            });

            // Attach keyup event for mobile
            $('#search_location_m').keyup(function() {
                fetchLocations($(this).val(), '#location_suggestions_m');
            });
        });
    </script>
    <script>
        $(window).load(function() {
            $(".goog-logo-link").empty();
            $('.goog-te-gadget').html($('.goog-te-gadget').children());
            $('.goog-te-gadget').css('padding', '0px');
            $('.goog-te-gadget').css('font-size', '0px');
        })
    </script>
    <script type="text/javascript" src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit">
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <script>
        jQuery(document).ready(function($) {
            var $slickElement = $('.slideshow');

            $slickElement.slick({
                autoplay: true,
                dots: false,
            });

        });
    </script>
    <script type="text/javascript">
        function googleTranslateElementInit(lang) {
            new google.translate.TranslateElement({
                pageLanguage: lang,
                includedLanguages: 'en,it,fr,nl,pt',
                layout: google.translate.TranslateElement.InlineLayout.SIMPLE
            }, 'google_translate_element');
        }
    </script>
    <script>
        $('.currencyLocalization').click(function() {
            var currency = $(this).attr('data-currency');
            $.ajax({
                url: '{{ url('post-currency') }}' + '/' + currency,
                method: 'GET',
                beforeSend: function() {

                },
                success: function(response) {
                    location.reload();
                },
                error: function() {

                }
            });
        });
    </script>
    @yield('scripts')
</body>

</html>
