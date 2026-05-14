document.addEventListener('DOMContentLoaded', function () {
    const packageContainer = document.getElementById('package-container');
    if (!packageContainer) return;

    const cards = Array.from(packageContainer.querySelectorAll('[data-category]'));
    const searchInput = document.querySelector('.search-bar');
    const searchButtons = document.querySelectorAll('.search-btn');
    const categoryButtons = document.querySelectorAll('#categoryTabs [data-category], #categoryTabsSticky [data-category]');
    const stickyWrapper = document.querySelector('.category-sticky-wrapper');
    let activeCategory = 'all';

    function cardMatches(card, searchTerm) {
        const category = (card.dataset.category || 'all').toLowerCase();
        const categoryMatch = activeCategory === 'all' || category === activeCategory;
        const textMatch = !searchTerm || card.innerText.toLowerCase().includes(searchTerm);
        return categoryMatch && textMatch;
    }

    function applyFilters() {
        const searchTerm = (searchInput?.value || '').trim().toLowerCase();

        cards.forEach(card => {
            card.style.display = cardMatches(card, searchTerm) ? '' : 'none';
        });
    }

    function goToPackages() {
        packageContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function syncActiveTabs(category) {
        categoryButtons.forEach(button => {
            const selected = button.dataset.category === category;
            button.classList.toggle('active', selected);
        });
    }

    categoryButtons.forEach(button => {
        button.addEventListener('click', () => {
            activeCategory = button.dataset.category || 'all';
            syncActiveTabs(activeCategory);
            applyFilters();
            goToPackages();
        });
    });

    if (searchInput) {
        searchInput.addEventListener('input', applyFilters);
        searchInput.addEventListener('keydown', event => {
            if (event.key === 'Enter') {
                event.preventDefault();
                applyFilters();
                goToPackages();
            }
        });
    }

    searchButtons.forEach(button => {
        button.addEventListener('click', event => {
            event.preventDefault();
            applyFilters();
            goToPackages();
        });
    });

    if (stickyWrapper) {
        window.addEventListener('scroll', () => {
            stickyWrapper.classList.toggle('scrolled', window.scrollY > 220);
        });
    }
});
