@extends('user.layout.app')
@section('page_name', 'FAQ')
@section('content')
<div class="container">
    <div class="faq-section new-faq-section">
        <h2 class="text-center">Frequently Asked Questions</h2>
        <p class="text-center">Perceived end knowledge certainly day sweetness why cordially</p>
        <div class="accordion" id="faqAccordion">
            <div class="faq-item">
                <div class="card">
                    <div class="card-header" id="headingOne">
                        <h3 class="mb-0">
                            <button class="btn text-blue-400 accordion-btn" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                What are your shipping options?
                                <i class="fas fa-chevron-down mr-2"></i>
                            </button>
                        </h3>
                    </div>
                    <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#faqAccordion">
                        <div class="card-body">
                            We offer standard and expedited shipping options. Standard shipping typically takes 3-5 business
                            days, while expedited shipping delivers within 1-2 business days.
                        </div>
                    </div>
                </div>
            </div>
            <div class="faq-item">
                <div class="card">
                    <div class="card-header" id="headingTwo">
                        <h3 class="mb-0">
                            <button class="btn text-blue-400 collapsed accordion-btn" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">

                                Do you offer international shipping?
                                <i class="fas fa-chevron-down mr-2"></i>
                            </button>
                        </h3>
                    </div>
                    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#faqAccordion">
                        <div class="card-body">
                            Yes, we offer international shipping to most countries. Shipping times and costs may vary
                            depending on the destination.
                        </div>
                    </div>
                </div>
            </div>
            <div class="faq-item">
                <div class="card">
                    <div class="card-header" id="headingThree">
                        <h3 class="mb-0">
                            <button class="btn text-blue-400 collapsed accordion-btn" type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">

                                What is your return policy?
                                <i class="fas fa-chevron-down mr-2"></i>
                            </button>
                        </h3>
                    </div>
                    <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#faqAccordion">
                        <div class="card-body">
                            We accept returns within 30 days of purchase. Items must be unused and in their original
                            packaging for a full refund.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .product-img-wrapper {
        height: 250px;
        /* Set a fixed height for the wrapper */
        overflow: hidden;
        /* Ensure any overflow is hidden */
    }

    .product-img-wrapper img {
        width: 100%;
        /* Allow the image to fill the width of the wrapper */
        height: auto;
        /* Maintain aspect ratio */
    }

    .product-image {
        height: 250px;
    }

    .text-blue-400 {
        color: #1575b8;
    }

    .text-blue-400:hover {
        color: lightblue;
    }

    .bg-blue {
        color: white;
        border-radius: 5px;
        background-color: #1575b8;
    }

    .bg-blue:hover {
        color: white;
        border-radius: 5px;
        background-color: lightblue;
    }
</style>
@endsection
@section('scripts')

@endsection