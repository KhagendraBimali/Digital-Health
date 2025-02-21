




document.addEventListener('DOMContentLoaded', function () {
    
    let currentSlide = 1; 
    showSlide(currentSlide);

    
    setInterval(function () {
        
        currentSlide++;

        
        if (currentSlide > 3) {
            currentSlide = 1;
        }

        
        showSlide(currentSlide);
    }, 5000); 

    
    function showSlide(slideIndex) {
        
        let slides = document.querySelectorAll('.slide');
        slides.forEach(function (slide) {
            slide.classList.remove('slide-is-active');
        });

        
        let selectedSlide = document.querySelector('.slide' + slideIndex);
        selectedSlide.classList.add('slide-is-active');
    }
});
