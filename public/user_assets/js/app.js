// Swiper initialized
var swiper = new Swiper(".top-deals-slider", {
	slidesPerView: 6,
	spaceBetween: 30,
	breakpoints: {
	
		700: {
			slidesPerView: 2,
			spaceBetweenSlides: 30
		},

		999: {
			slidesPerView: 4,
			spaceBetweenSlides: 40
		},
		1200: {
            slidesPerView: 6,
            spaceBetween: 60,
        },
	},
	navigation: {
		nextEl: ".swiper-button-next",
		prevEl: ".swiper-button-prev",
	},
});
var swiper = new Swiper(".recent-products", {
    slidesPerView: 6,
    spaceBetween: 30,

    navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
    },

    breakpoints: {
   
        320: {
            slidesPerView: 1,
            spaceBetween: 10,
        },

        480: {
            slidesPerView: 2,
            spaceBetween: 20,
        },
        640: {
            slidesPerView: 3,
            spaceBetween: 30,
        },

        768: {
            slidesPerView: 4,
            spaceBetween: 40,
        },

    
        1200: {
            slidesPerView: 6,
            spaceBetween: 60,
        },
    },
});

var swiper = new Swiper(".categories-slider", {
	slidesPerView: 6,
	spaceBetween: 30,
	breakpoints: {
	
		700: {
			slidesPerView: 2,
			spaceBetweenSlides: 30
		},

		999: {
			slidesPerView: 4,
			spaceBetweenSlides: 40
		},
		1200: {
            slidesPerView: 6,
            spaceBetween: 60,
        },
	},
	navigation: {
		nextEl: ".swiper-button-next",
		prevEl: ".swiper-button-prev",
	},
});
var swiper = new Swiper(".top-deals-slider", {
	slidesPerView: 6,
	spaceBetween: 30,
	breakpoints: {
	
		700: {
			slidesPerView: 2,
			spaceBetweenSlides: 30
		},

		999: {
			slidesPerView: 4,
			spaceBetweenSlides: 40
		},
		1200: {
            slidesPerView: 6,
            spaceBetween: 60,
        },
	},
	navigation: {
		nextEl: ".swiper-button-next",
		prevEl: ".swiper-button-prev",
	},
});

var swiper = new Swiper(".home-banner-slider", {
	navigation: {
		nextEl: ".swiper-button-next",
		prevEl: ".swiper-button-prev",
	},
});

var swiper = new Swiper(".teams-slider", {
slidesPerView: 4,
spaceBetween: 30,
pagination: {
  el: ".swiper-pagination",
  clickable: true,
},
});


// Swiper initialized
// Offcanvas 
document.addEventListener('DOMContentLoaded', function () {
	const openButton = document.getElementById('openButton');
	const closeButton = document.getElementById('closeButton');
	const offcanvas = document.querySelector('.overlay-offcanvas');
	const offCanvas = document.querySelector('.offcanvas');
	openButton.addEventListener('click', function () {
		offcanvas.classList.remove('close');
		offcanvas.classList.add('active');
		document.body.style.overflow = "hidden";
	});

	closeButton.addEventListener('click', function () {
		offcanvas.classList.add('close');
		document.body.style.overflow = "";
	});
	offcanvas.addEventListener('click', () => {
		offcanvas.classList.remove('active');
		offcanvas.classList.add('close');
		document.body.style.overflow = "";
	})
	offCanvas.addEventListener('click', (e) => {
		e.stopPropagation()
        
	})
});
// Offcanvas