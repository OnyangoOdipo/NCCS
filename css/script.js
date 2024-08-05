document.addEventListener('DOMContentLoaded', function() {
    let slideIndex = 0;
    let newsIndex = 0;
    const slides = document.querySelectorAll('.slide');
    const newsItems = document.querySelectorAll('.news-item');

    function showSlide(index) {
        slides.forEach((slide, i) => {
            slide.style.display = i === index ? 'block' : 'none';
        });
    }

    function showNews(index) {
        newsItems.forEach((news, i) => {
            news.style.display = i === index ? 'block' : 'none';
        });
    }

    document.querySelector('.prev-slide').addEventListener('click', function() {
        slideIndex = (slideIndex > 0) ? slideIndex - 1 : slides.length - 1;
        showSlide(slideIndex);
    });

    document.querySelector('.next-slide').addEventListener('click', function() {
        slideIndex = (slideIndex < slides.length - 1) ? slideIndex + 1 : 0;
        showSlide(slideIndex);
    });

    document.querySelector('.prev-news').addEventListener('click', function() {
        newsIndex = (newsIndex > 0) ? newsIndex - 1 : newsItems.length - 1;
        showNews(newsIndex);
    });

    document.querySelector('.next-news').addEventListener('click', function() {
        newsIndex = (newsIndex < newsItems.length - 1) ? newsIndex + 1 : 0;
        showNews(newsIndex);
    });

    showSlide(slideIndex);
    showNews(newsIndex);
});
