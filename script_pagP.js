document.addEventListener('DOMContentLoaded', () => {
    const searchLink = document.querySelector('#search-link');
    const searchBox = document.getElementById('search-box');

    searchLink.addEventListener('click', (e) => {
        e.preventDefault();
        if (searchBox.style.display === 'none' || searchBox.style.display === '') {
            searchBox.style.display = 'block';
            setTimeout(() => {
                searchBox.classList.add('open');
            }, 10);
        } else {
            searchBox.classList.remove('open');
            setTimeout(() => {
                searchBox.style.display = 'none';
            }, 500); 
        }
    });
    
    document.addEventListener('click', (e) => {
        if (!searchBox.contains(e.target) && !searchLink.contains(e.target)) {
            searchBox.classList.remove('open');
            setTimeout(() => {
                searchBox.style.display = 'none';
            }, 500);
        }
    });
});
