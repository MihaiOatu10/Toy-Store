document.getElementById('disableFilter').addEventListener('click', function() {
    var filterBox = document.getElementById('filterBox');
    var catalogProd = document.getElementById('catalogProd');
    var filterText = document.querySelector('.catalog_prod .filter-text');

    if (!filterBox.classList.contains('hidden')) {
        filterBox.classList.add('hidden');
        catalogProd.classList.add('expanded');
        filterText.classList.add('show');
    }
});

document.getElementById('toggleFilter').addEventListener('click', function() {
    var filterBox = document.getElementById('filterBox');
    var catalogProd = document.getElementById('catalogProd');
    var filterText = document.querySelector('.catalog_prod .filter-text');

    if (filterBox.classList.contains('hidden')) {
        filterBox.classList.remove('hidden');
        catalogProd.classList.remove('expanded');
        filterText.classList.remove('show');
    }
});