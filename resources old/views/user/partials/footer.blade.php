<footer class="footer-container pt-5">
    <div>
        <div class="container-fluid px-3 px-md-5">
            <div class="row">

                <div class="col-lg-3 col-md-4">
                    <div class="footer-widget">
                        <div class="d-flex align-items-start flex-column mb-3">
                            <div class="d-inline-block">
                              <a href="{{ url('/') }}"> <img src="{{ asset('backend_assets') }}/images/siostore_logo.png" width="180" alt="" /> </a>
                            </div>
                        </div>
                        <div class="footer-add pe-xl-3">
                            <p>SIOSTORE is an online store that allows you to buy and shop for your numerous demands at
                                your convenience.</p>
                        </div>
                        <div class="foot-socials">
                            <ul class="d-flex">
                                <li><a href="JavaScript:Void(0);"><i class="fab fa-facebook-f"></i></a></li>
                                <li><a href="JavaScript:Void(0);"><i class="fab fa-linkedin-in"></i></a></li>
                                <li><a href="JavaScript:Void(0);"><i class="fab fa-instagram"></i></a></li>
                                <li><a href="JavaScript:Void(0);"><i class="fab fa-twitter"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4">
                    <div class="footer-widget">
                        <h4 class="widget-title">Quick Shop</h4>
                        <ul class="footer-menu">
                            <li><a href="{{ url('/') }}">Home</a></li>
                            <li><a href="{{ url('contact') }}">Contact Us</a></li>
                            <li><a href="{{ url('/term-condition') }}">Terms & Conditions</a></li>
                            <li><a href="{{ url('/disclaimer') }}">Disclaimer</a></li>
                            <li><a href="{{ url('/privacy-policy') }}">Privacy Policy</a></li>
                            {{-- <li><a href="{{ url('/refund') }}">Refund Policy</a></li> --}}
                            <li><a href="{{ url('/licence') }}">license & Agreements</a></li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-2 col-md-4">
                    <div class="footer-widget">
                        <h4 class="widget-title">My Account</h4>
                        <ul class="footer-menu">
                            <li><a href="{{ url('login') }}">Login</a></li>
                            <li><a href="{{ url('signup') }}">Sign Up</a></li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 ">
                    <div class="footer-widget">
                        <h4 class="widget-title">More links</h4>
                        <div class="d-flex flex-column">
                            <a class="text-white mb-2" target="_blank" href="https://siopay.eu">Payment & Digital Services</a>
                            <a class="text-white mb-2" target="_blank" href="https://sioshipping.eu">Shipping & Courier services</a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6 px-md-0">
                    <div class="footer-widget">
                        <h4 class="widget-title">Newsletter</h4>
                        <div class="pmt-wrap">
                            <p>Subscribe to our newsletter to get updates on our products and services</p>
                        </div>
                        <form action="{{ route('subscriber') }}" method="POST">
                            @csrf
                            <div class="input-group">
                                <input type="email" class="form-control" name="email" placeholder="Your Email Address">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary">Subscribe</button>
                                </div>
                            </div>
                        </form>


                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="footer-bottom py-3">
        <div class="container-fluid px-sm-5">
            <div class="row align-items-center justify-content-between text-sm-start text-center">
                <?php
                $currentYear = date("Y");
                ?>
                <div class="col-xl-6 col-lg-6 col-md-6">
                    <p class="mb-0 text-left"><a class="text-primary" href="#"> &copy; <?= $currentYear; ?> SioStore</a> | All Rights
                        Reserved</p>
                </div>

                <div class="col-xl-6 col-lg-6 col-md-6">
                    <ul class="p-0 d-flex justify-content-sm-start justify-content-md-end text-start text-md-end m-0 justify-content-center payment-img">
                        <img class="img-fluid" src="{{asset('user_assets/img/payments.png')}}" alt="">
                    </ul>
                </div>

            </div>
        </div>
    </div>
</footer>


<!-- <div class="container-fluid bg-dark text-secondary mt-5 pt-5">
    <div class="row px-xl-5 pt-5">
        <div class="col-lg-4 col-md-12 mb-5 pr-3 pr-xl-5">
            <h5 class="text-secondary text-uppercase mb-4">Get In Touch</h5>
            {{-- <p class="mb-4">No dolore ipsum accusam no lorem. Invidunt sed clita kasd clita et et dolor sed dolor. Rebum tempor no vero est magna amet no</p> --}}
            <p class="mb-2"><i class="fa fa-map-marker-alt text-primary mr-2"></i>Piazza Medaglia d'Oro Porcelli 88046 Lamezia Terme (CZ), Italy</p>
            <p class="mb-2"><i class="fa fa-envelope text-primary mr-2"></i>support@siopay.eu</p>
            <p class="mb-0"><i class="fa fa-phone-alt text-primary mr-2"></i>+39 0968 191 6024 (Office)</p>
            <p class="mb-0"><i class="fa fa-phone-alt text-primary mr-2"></i>+39 3770 993 615 (Mobile)</p>
            <p class="mb-0"><i class="fa fa-print text-primary mr-2"></i>+39 0968 191 6024 (Fax)</p>
            <p class="mb-0"><i class="fa fa-phone-alt text-primary mr-2"></i>03940320793 (P. IVA)</p>
        </div>
        <div class="col-lg-8 col-md-12">
            <div class="row">
                <div class="col-md-4 mb-5">
                    <h5 class="text-secondary text-uppercase mb-4">Quick Shop</h5>
                    <div class="d-flex flex-column justify-content-start">
                        <a class="text-secondary mb-2" href="/"><i class="fa fa-angle-right mr-2"></i>Home</a>
                        <a class="text-secondary" href="#"><i class="fa fa-angle-right mr-2"></i>Contact Us</a>
                    </div>
                </div>
                <div class="col-md-4 mb-5">
                    <h5 class="text-secondary text-uppercase mb-4">My Account</h5>
                    <div class="d-flex flex-column justify-content-start">
                        <a class="text-secondary mb-2" href="#"><i class="fa fa-angle-right mr-2"></i>Login</a>
                        <a class="text-secondary mb-2" href="#"><i class="fa fa-angle-right mr-2"></i>Sign In</a>
                    </div>
                </div>
                <div class="col-md-4 mb-5">
                    <h5 class="text-secondary text-uppercase mb-4">Newsletter</h5>
                    <p>Subscribe to our newsletter to get updates on our products and services</p>
                    <form action="">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Your Email Address">
                            <div class="input-group-append">
                                <button class="btn btn-primary">Sign Up</button>
                            </div>
                        </div>
                    </form>
                    <h6 class="text-secondary text-uppercase mt-4 mb-3">Follow Us</h6>
                    <div class="d-flex">
                        <a class="btn btn-primary btn-square mr-2" href="#"><i class="fab fa-twitter"></i></a>
                        <a class="btn btn-primary btn-square mr-2" href="#"><i class="fab fa-facebook-f"></i></a>
                        <a class="btn btn-primary btn-square mr-2" href="#"><i class="fab fa-linkedin-in"></i></a>
                        <a class="btn btn-primary btn-square" href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row border-top mx-xl-5 py-4" style="border-color: rgba(256, 256, 256, .1) !important;">
        <div class="col-md-6 px-xl-0">
            <p class="mb-md-0 text-center text-md-left text-secondary">
                &copy; <a class="text-primary" href="#">SioStore</a>. All Rights Reserved.
            </p>
        </div>
        <div class="col-md-6 px-xl-0 text-center text-md-right">
            <img class="img-fluid" src="{{asset('user_assets/img/payments.png')}}" alt="">
        </div>
    </div>
</div> -->
