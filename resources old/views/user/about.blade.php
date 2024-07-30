@extends('user.layout.app')
@section('page_name', 'About')
@section('content')


    <section class="banner-section" id="about-banner">
        <div class="container">
            <div class="text-center banner-centent">
                <h2 class="text-white">About Us</h2>
                <p class="text-white">Follow your passion, and success will follow you</p>
            </div>
        </div>
    </section>
    <section class="about-section py-5 mb-0">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="about-left-img">
                        <img alt="about-img"
                            src="https://admin.qlinebiotech.com/uploads/about/49de0fd4a7b704240d78267f81eef258.jpg"
                            class="about-img w-100">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="about-right brder-none mt-4 mt-sm-0" data-aos="fade-right">
                        <span>About Our Store</span>
                        <h3 class="mt-2 mb-3">Welcome to SIOSTORE</h3>
                        <p>SIOSTORE is an online store that allows you to buy and shop for your numerous demands at your
                            convenience. It is a globally recognize online market where all your humanitarian necessities
                            are adequately and viably meant.</p>
                        <p>Buy & sell electronics, cars, clothes, foods, collectibles & more on SIOSTORE, the world's best
                            online marketplace. Top brands, low prices & free shipping on many items at your convenient is
                            our mission.</p>
                        <a href="{{ url('/contact') }}" class="d-inline-block mt-3">Contact Us</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
@section('scripts')

@endsection
