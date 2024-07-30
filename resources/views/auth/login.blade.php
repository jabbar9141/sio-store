<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    @include('backend.includes.favicon')
    @include('backend.includes.css')
    <title>Sign in</title>
    <link href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <style>
        * {
            font-family: "Jost", sans-serif;
        }

        .logo {
            width: 200px;
        }

        .devider-wraps {
            height: 0.5px;
            width: 100%;
            border: 1px dashed #c3c8cb;
            border-radius: 50px;
        }

        .devider-text {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            background: #fff;
            padding: 0 10px;
            text-wrap: nowrap;

        }

        .square--60 {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .social-login ul {
            list-style-type: none;
        }

        .social-login ul li {
            font-size: 36px;
        }

        .form-control:focus {
            box-shadow: unset !important;
            border: 0 !important;
        }

        .shadow-sm {
            box-shadow: rgba(0, 0, 0, 0.05) 0px 6px 24px 0px, rgba(0, 0, 0, 0.08) 0px 0px 0px 1px !important;
        }

        .shadow {
            box-shadow: rgba(0, 0, 0, 0.02) 0px 1px 3px 0px, rgba(27, 31, 35, 0.15) 0px 0px 0px 1px !important;
        }
    </style>
</head>

<body class="bg-login">


    <section class="py-5">
        <div class="container">

            <div class="row justify-content-center align-items-center m-auto">
                <div class="col-12">
                    <div class="bg-mode shadow rounded-3 overflow-hidden">
                        <div class="row g-0">
                            <!-- Vector Image -->
                            <div class="col-lg-6   d-flex align-items-center order-2 order-lg-1">
                                <div class=" d-lg-block d-md-none d-sm-none d-none">
                                    <img src="{{ asset('backend_assets') }}/images/login.png" class="img-fluid"
                                        alt="">
                                </div>
                                <!-- Divider -->
                                <div class="vr opacity-1 d-none d-lg-block"></div>
                            </div>

                            <!-- Information -->
                            <div class="col-lg-6 order-1">
                                <div class="p-3 p-sm-4 p-md-5">
                                    <!-- Logo -->
                                    <a href="{{ url('/') }}">
                                        <img class="img-fluid logo mb-4"
                                            src="{{ asset('backend_assets') }}/images/siostore_logo.png" width="70"
                                            alt="logo">
                                    </a>
                                    <!-- Title -->
                                    <h1 class="mb-2 fs-2">Sign in</h1>
                                    <p class="mb-4">Are you new here?<a href="/register"
                                            class="fw-medium text-primary"> Sign up here</a></p>

                                    <!-- Form START -->
                                    <form id="login_form" class="row g-3" method="POST" action="{{ route('login') }}">
                                        @csrf

                                        <div class="col-sm-12 mb-2">
                                            <label for="inputUserName" class="form-label">Username</label>
                                            <input name="username" type="text" class="form-control"
                                                id="inputUserName" placeholder="Username" autocomplete="username"
                                                autofocus required>
                                            <small style="color: #e20000" class="error"
                                                id="username-error">{{ isset($errors->get('username')[0]) ? $errors->get('username')[0] : null }}</small>
                                        </div>

                                        <div class="col-12 mb-4">
                                            <label for="inputChoosePassword" class="form-label">Enter Password</label>
                                            <div class="input-group" id="show_hide_password">
                                                <input name="password" autocomplete="current-password" type="password"
                                                    class="form-control border-end-0" id="inputChoosePassword"
                                                    placeholder="Enter Password" required> <a href="javascript:;"
                                                    class="input-group-text bg-transparent"><i
                                                        class='bx bx-hide'></i></a>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check form-switch">
                                                <input name="remember" class="form-check-input" type="checkbox"
                                                    id="flexSwitchCheckChecked" checked>
                                                <label class="form-check-label" for="flexSwitchCheckChecked">Remember
                                                    Me</label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="d-grid">
                                                <button type="submit" class="btn btn-primary"><i
                                                        class="bx bxs-lock-open"></i>Sign in</button>
                                            </div>
                                        </div>

                                        <div
                                            class="modal-flex-item d-flex align-items-center justify-content-between mb-3">
                                            <div class="modal-flex-first">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" id="savepassword"
                                                        value="option1">
                                                    <label class="form-check-label" for="savepassword">Save
                                                        Password</label>
                                                </div>
                                            </div>
                                            <div class="modal-flex-last">
                                                <a href="{{url('forgot-password')}}" class="text-primary fw-medium">Forget
                                                    Password?</a>
                                            </div>
                                        </div>
                                </div>

                                <!-- Divider -->
                                <div class="prixer px-3">
                                    <div class="devider-wraps position-relative">
                                        <div class="devider-text text-muted-2 text-md">Sign In with Socials</div>
                                    </div>
                                </div>

                                <!-- Google and facebook button -->
                                <div class="social-login py-4 px-md-2">
                                    <div class="d-grid">
                                        <a class="btn my-4 shadow-sm" href="social_auth/google"> <span
                                                class="d-flex
                                    justify-content-center align-items-center">
                                                <img class="me-2"
                                                    src="{{ asset('backend_assets') }}/images/icons/search.svg"
                                                    width="16" alt="Image Description">
                                                <span>Sign in with Google</span>
                                            </span>
                                        </a>
                                    </div>
                                </div>
                                </form>
                                <!-- Form END -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        </div>
    </section>
    <!--wrapper-->


    <!--end wrapper-->
    @include('backend.includes.js')

    <!--Password show & hide js -->
    <script>
        $(document).ready(function() {
            $("#show_hide_password a").on('click', function(event) {
                event.preventDefault();
                if ($('#show_hide_password input').attr("type") == "text") {
                    $('#show_hide_password input').attr('type', 'password');
                    $('#show_hide_password i').addClass("bx-hide");
                    $('#show_hide_password i').removeClass("bx-show");
                } else if ($('#show_hide_password input').attr("type") == "password") {
                    $('#show_hide_password input').attr('type', 'text');
                    $('#show_hide_password i').removeClass("bx-hide");
                    $('#show_hide_password i').addClass("bx-show");
                }
            });
        });
    </script>
</body>

</html>
