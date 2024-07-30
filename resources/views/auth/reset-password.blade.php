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
                                    <h1 class="mb-2 fs-2">Forgot Password</h1>
                                    <div class="mb-4 text-sm text-gray-600">
                                        Forgot your password? No problem. Just let us know your email address and we
                                        will email you a password reset link that will allow you to choose a new one.
                                    </div>

                                    <!-- Session Status -->
                                    <!-- Replace with actual session status if applicable -->
                                    @if (session('status'))
                                        <div class="mb-4">
                                            {{ session('status') }}
                                        </div>
                                    @endif

                                    <!-- Form START -->
                                    <form method="POST" action="{{ route('password.store') }}">
                                        @csrf
                                        <input type="hidden" name="token" value="{{ $request->route('token') }}">

                                        <!-- Email Address -->
                                        <div>
                                            <label for="email">Email</label>

                                            <input id="email" class="block mt-1 w-full form-control" type="email"
                                                name="email" value="{{ old('email', $request->email) }}" required
                                                autofocus autocomplete="username" />
                                            @error('email')
                                                <div class="mt-2 text-sm text-red-600" style="color:red">
                                                    @foreach ($errors->get('email') as $message)
                                                        <div>{{ $message }}</div>
                                                    @endforeach
                                                </div>
                                            @enderror
                                        </div>

                                        <!-- Password -->
                                        <div class="mt-4">
                                            <label for="password" :value="__('Password')">Password</label>
                                            <input id="password" class="block mt-1 w-full form-control" type="password"
                                                name="password" required autocomplete="new-password" />
                                            @error('password')
                                                <div class="mt-2 text-sm text-red-600" style="color:red">
                                                    @foreach ($errors->get('password') as $message)
                                                        <div>{{ $message }}</div>
                                                    @endforeach
                                                </div>
                                            @enderror
                                        </div>

                                        <!-- Confirm Password -->
                                        <div class="mt-4">
                                            <label for="password_confirmation" :value="__('Confirm Password')">Confirm
                                                Password</label>

                                            <input id="password_confirmation" class="block mt-1 w-full form-control"
                                                type="password" name="password_confirmation" required
                                                autocomplete="new-password" />


                                            @error('password_confirmation')
                                                <div class="mt-2 text-sm text-red-600" style="color:red">
                                                    @foreach ($errors->get('password_confirmation') as $message)
                                                        <div>{{ $message }}</div>
                                                    @endforeach
                                                </div>
                                            @enderror

                                        </div>

                                        <div class="flex items-center justify-end mt-4">
                                            <button type="submit" class="btn btn-primary"><i
                                                    class="bx bxs-lock-open"></i> Reset Password</button>

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

</body>

</html>
