document.addEventListener('DOMContentLoaded', function() {
    let slideIndex = 0;
    let newsIndex = 0;
    const slides = document.querySelectorAll('.slide');
    const newsItems = document.querySelectorAll('.news-item');
    const slideInterval = 5000; // 5 seconds
    const newsInterval = 5000;  // 5 seconds
    const slidesContainer = document.querySelector('.slides');
    const newsContainer = document.querySelector('.news-slider');

    function showSlide(index) {
        const offset = -index * 100; // Calculate the offset for the slide
        slidesContainer.style.transform = `translateX(${offset}%)`;
    }

    // function showNews(index) {
    //     const offset = -index * 100; // Calculate the offset for the news
    //     newsContainer.style.transform = `translateX(${offset}%)`;
    // }

    // function nextSlide() {
    //     slideIndex = (slideIndex + 1) % slides.length;
    //     showSlide(slideIndex);
    // }

    // function prevSlide() {
    //     slideIndex = (slideIndex - 1 + slides.length) % slides.length;
    //     showSlide(slideIndex);
    // }

    // function nextNews() {
    //     newsIndex = (newsIndex + 1) % newsItems.length;
    //     showNews(newsIndex);
    // }

    // function prevNews() {
    //     newsIndex = (newsIndex - 1 + newsItems.length) % newsItems.length;
    //     showNews(newsIndex);
    // }

    // Set up automatic scrolling
    setInterval(nextSlide, slideInterval);
    setInterval(nextNews, newsInterval);

    // Event listeners for manual control
    document.querySelector('.prev-slide').addEventListener('click', prevSlide);
    document.querySelector('.next-slide').addEventListener('click', nextSlide);
    document.querySelector('.prev-news').addEventListener('click', prevNews);
    document.querySelector('.next-news').addEventListener('click', nextNews);

    // Initial display setup
    showSlide(slideIndex);
    showNews(newsIndex);
});
