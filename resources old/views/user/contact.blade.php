@extends('user.layout.app')
@section('page_name', 'Contact')
@section('content')

<section class="banner-section" id="about-banner">
    <div class="container">
        <div class="text-center banner-centent">
            <h2 class="text-white">Contact Us</h2>
            <p class="text-white">Follow your passion, and success will follow you</p>
        </div>
    </div>
</section>

<section class="contact_section small-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-sm-6 mb-2">
                <div class="contact_wrap">
                    <div class="title_bar">
                        <i class="fas fa-map-marker-alt"></i>
                        <h4>Address</h4>
                    </div>
                    <div class="contact_content">
                        <p class="mb-0 text-center">Piazza Medaglia d'Oro Porcelli 88046 <br>
                            Lamezia Terme (CZ), Italy</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-2">
                <div class="contact_wrap">
                    <div class="title_bar">
                        <i class="fas fa-envelope"></i>
                        <h4>email address</h4>
                    </div>
                    <div class="contact_content">
                        <ul class="text-center">
                            <li><a href="mailto:support@siopay.eu">support@siostore.eu</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-2">
                <div class="contact_wrap">
                    <div class="title_bar">
                        <i class="fas fa-phone-alt"></i>
                        <h4>phone</h4>
                    </div>
                    <div class="contact_content">
                        <ul class="text-center">
                            <li><a href="tel:+39 0968 191 6024"> +39 0968 191 6024</a></li>
                            {{-- <li><a href="tel:+39 3770 993 615"> +39 3770 993 615</a></li> --}}
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-2">
                <div class="contact_wrap">
                    <div class="title_bar">
                        <i class="fas fa-fax"></i>
                        <h4>fax</h4>
                    </div>
                    <div class="contact_content">
                        <ul class="text-center">
                            <li><a href="tel:+39 0968 191 6024">+39 0968 191 6024</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="small-section mb-3 overflow-unset">
    <div class="container">
        <div class="row ">
            <div class="col-md-6">
                <div class="get-in-touch">
                    <h3>get in touch</h3>
                    @if(session()->has('success'))
                        <div class="alert alert-success">
                         {{session('success')}}
                        </div>
                    @endif
                    <form action="{{route('get-in-touch')}}" method="post">
                    @csrf
                        <div class="row mb-3">
                            <div class="form-group col-md-6">
                                <input type="text" class="form-control" id="name" name="first_name" placeholder="first_name" required="">
                            </div>
                            <div class="form-group col-md-6">
                                <input type="text" class="form-control" id="last-name" name="last_name" placeholder="last_name" required="">
                            </div>
                            <div class="form-group col-lg-6">
                                <input type="number" class="form-control" id="review" name="phone_number" placeholder="phone_number" required="">
                            </div>
                            <div class="form-group col-lg-6">
                                <input type="text" class="form-control" id="email" name="email" placeholder="email" required="">
                            </div>
                            <div class="form-group col-md-12">
                                <textarea class="form-control" name="message" placeholder="Write Your Message" id="exampleFormControlTextarea1" rows="6"></textarea>
                            </div>
                            <div class="col-md-12 submit-btn mt-2">
                                <button class="btn btn-solid" type="submit">Send Your Message</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-6">
                <div class="contact-map h-100">
                    <iframe class="w-100 h-100" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d193595.1583091352!2d-74.11976373946229!3d40.69766374859258!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c24fa5d33f083b%3A0xc80b8f06e177fe62!2sNew+York%2C+NY%2C+USA!5e0!3m2!1sen!2sin!4v1563449626439!5m2!1sen!2sin" allowfullscreen=""></iframe>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
@section('scripts')

@endsection