// Featured Items Carousel JS
const featuredItems = [
    {
        img: '../images/user/tile1.jpg',
        title: 'Long-Length 2.0',
        price: '$22.00',
    },
    {
        img: '../images/user/tile1.jpg',
        title: 'Speed 500 Ignite',
        price: '$120.00',
    },
    {
        img: '../images/user/tile2.jpg',
        title: 'Jordan Hyper Grip Ot',
        price: '$50.00',
    },
    {
        img: '../images/user/tile3.jpg',
        title: 'Swimming Cap Slin',
        price: '$22.00',
    },
    {
        img: '../images/user/tile4.jpg',
        title: 'Soccer Ball Club America',
        price: '$30.00',
    },
    {
        img: '../images/user/tile5.jpg',
        title: 'Hyperadapt Shield Lite Half-Zip',
        price: '$110.00',
    },
];

const itemsPerPage = () => {
    if (window.innerWidth <= 500) return 1;
    if (window.innerWidth <= 800) return 2;
    if (window.innerWidth <= 1100) return 3;
    return 5;
};


let currentPage = 0;
let lastPage = 0;
let animating = false;


function renderFeaturedItems(direction = 0) {
    const container = document.querySelector('.featured-items');
    if (!container) return;
    const perPage = itemsPerPage();
    const pageCount = Math.ceil(featuredItems.length / perPage);
    // Clamp currentPage to valid range
    if (currentPage < 0) currentPage = 0;
    if (currentPage >= pageCount) currentPage = pageCount - 1;
    const start = currentPage * perPage;
    const end = start + perPage;

    // Animation: slide out old items
    if (direction !== 0) {
        if (animating) return;
        animating = true;
        container.classList.add(direction > 0 ? 'slide-left-out' : 'slide-right-out');
        setTimeout(() => {
            container.classList.remove('slide-left-out', 'slide-right-out');
            updateFeaturedItems(container, start, end);
            container.classList.add(direction > 0 ? 'slide-left-in' : 'slide-right-in');
            setTimeout(() => {
                container.classList.remove('slide-left-in', 'slide-right-in');
                animating = false;
            }, 350);
        }, 350);
    } else {
        updateFeaturedItems(container, start, end);
    }
    renderPagination();
}

function updateFeaturedItems(container, start, end) {
    container.innerHTML = '';
    featuredItems.slice(start, end).forEach(item => {
        const div = document.createElement('div');
        div.className = 'featured-item';
        div.innerHTML = `
            <div class="featured-img-wrap">
                <img src="${item.img}" alt="${item.title}">
            </div>
            <div class="item-title">${item.title}</div>
            <div class="item-price">${item.price}</div>
            <button class="add-to-cart"><i class="fa fa-lock"></i> ADD TO CART</button>
        `;
        container.appendChild(div);
    });
}

function renderPagination() {
    const perPage = itemsPerPage();
    const pageCount = Math.ceil(featuredItems.length / perPage);
    const pagination = document.querySelector('.featured-pagination');
    if (!pagination) return;
    pagination.innerHTML = '';
    for (let i = 0; i < pageCount; i++) {
        const dot = document.createElement('span');
        dot.className = 'featured-dot' + (i === currentPage ? ' active' : '');
        dot.title = `Show items ${i * perPage + 1} - ${Math.min((i + 1) * perPage, featuredItems.length)}`;
        dot.onclick = () => {
            if (animating || i === currentPage) return;
            const direction = i > currentPage ? 1 : -1;
            currentPage = i;
            renderFeaturedItems(direction);
        };
        pagination.appendChild(dot);
    }
}


function nextFeatured() {
    if (animating) return;
    const perPage = itemsPerPage();
    const pageCount = Math.ceil(featuredItems.length / perPage);
    lastPage = currentPage;
    currentPage = (currentPage + 1) % pageCount;
    renderFeaturedItems(1);
}

function prevFeatured() {
    if (animating) return;
    const perPage = itemsPerPage();
    const pageCount = Math.ceil(featuredItems.length / perPage);
    lastPage = currentPage;
    currentPage = (currentPage - 1 + pageCount) % pageCount;
    renderFeaturedItems(-1);
}



window.addEventListener('resize', () => {
    currentPage = 0;
    renderFeaturedItems();
});



document.addEventListener('DOMContentLoaded', () => {
    currentPage = 0;
    const nextBtn = document.querySelector('.featured-arrow.next');
    const prevBtn = document.querySelector('.featured-arrow.prev');
    if (nextBtn) nextBtn.onclick = nextFeatured;
    if (prevBtn) prevBtn.onclick = prevFeatured;
    renderFeaturedItems();
});
