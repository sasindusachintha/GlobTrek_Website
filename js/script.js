document.addEventListener('DOMContentLoaded', function () {
    const travelPackages = [
        { 
            id: 0,
            name: "Santorini Breeze", 
            rating: "4.8 (120 reviews)", 
            price: "LKR 12,000", 
            images: ["images/package1_2.jpg", "images/package1.webp", "images/package1_3.webp", "images/package1_4.webp"],
            desc: "Discover the breathtaking blue waters and iconic white architecture of Greece.",
            transport: "Ferry & Luxury Bus",
            duration: "3 Days, 2 Nights"
        },
        { 
            id: 1,
            name: "Bali Retreat", 
            rating: "4.9 (250 reviews)", 
            price: "LKR 15,000", 
            images: ["images/package2.jpg", "images/package2_2.jpeg", "images/package2_3.webp", "images/package2_4.jpg"],
            desc: "Relax in the tropical paradise of Bali with villas and temples.",
            transport: "Private Van",
            duration: "5 Days, 4 Nights"
        },
        { 
            id: 2,
            name: "Parisian Night", 
            rating: "4.7 (95 reviews)", 
            price: "LKR 10,000", 
            images: ["images/package3.jpg", "images/package3_2.jpg", "images/package3_3.jpeg", "images/package3.jpg"],
            desc: "Enjoy the romance of Paris with Eiffel Tower dinner.",
            transport: "Metro & Walking Tour",
            duration: "2 Days, 1 Night"
        },
        { 
            id: 3,
            name: "Tokyo Explorer", 
            rating: "4.6 (180 reviews)", 
            price: "LKR 18,000", 
            images: ["images/package4.webp", "images/package4.webp", "images/package4.webp", "images/package4.webp"],
            desc: "Explore Tokyo’s modern and traditional attractions.",
            transport: "Bullet Train",
            duration: "4 Days, 3 Nights"
        },
        { 
            id: 4,
            name: "Swiss Alps", 
            rating: "4.9 (310 reviews)", 
            price: "LKR 20,000", 
            images: ["images/package5.jpeg", "images/package5.jpeg", "images/package5.jpeg", "images/package5.jpeg"],
            desc: "Mountain adventure with skiing and scenic views.",
            transport: "Cable Car",
            duration: "6 Days, 5 Nights"
        },
        { 
            id: 5,
            name: "Dubai Safari", 
            rating: "4.5 (150 reviews)", 
            price: "LKR 14,000", 
            images: ["images/package6.webp", "images/package6.webp", "images/package6.webp", "images/package6.webp"],
            desc: "Desert safari with luxury experience.",
            transport: "4x4 Jeep",
            duration: "3 Days, 2 Nights"
        },
        { 
            id: 6,
            name: "London Classic", 
            rating: "4.4 (200 reviews)", 
            price: "LKR 14,000", 
            images: ["images/package7.webp", "images/package7.webp", "images/package7.webp", "images/package7.webp"],
            desc: "Historic tour of London landmarks.",
            transport: "Bus",
            duration: "3 Days, 2 Nights"
        },
        { 
            id: 7,
            name: "Maldives Blue", 
            rating: "5.0 (80 reviews)", 
            price: "LKR 14,000", 
            images: ["images/package8.webp", "images/package8.webp", "images/package8.webp", "images/package8.webp"],
            desc: "Luxury island experience with clear waters.",
            transport: "Speedboat",
            duration: "4 Days, 3 Nights"
        }
    ];

    const packageContainer = document.getElementById('package-container');
    if (packageContainer) {
        packageContainer.innerHTML = "";
        travelPackages.forEach(pkg => {
            packageContainer.innerHTML += `
                <div class="col-lg-3 col-md-6">
                    <div class="package-card shadow-sm border rounded-3 overflow-hidden bg-white h-100">
                        <a href="details.html?id=${pkg.id}" class="text-decoration-none text-dark">
                            <div class="package-img" style="background-image:url('${pkg.images[0]}');height:200px;background-size:cover;background-position:center;"></div>
                            <div class="p-3">
                                <h3 class="h5 fw-bold mb-1">${pkg.name}</h3>
                                <p class="text-warning mb-1">⭐ ${pkg.rating}</p>
                                <p class="fw-bold text-success mb-2">${pkg.price}</p>
                            </div>
                        </a>
                        <div class="p-3 pt-0 d-flex justify-content-between align-items-center">
                            <button class="btn btn-primary btn-sm rounded-pill" onclick="window.location.href='booking.html?id=${pkg.id}'">Book Now</button>
                            <i class="fa-regular fa-heart favorite-icon"></i>
                        </div>
                    </div>
                </div>
            `;
        });
        document.querySelectorAll('.favorite-icon').forEach(heart => {
            heart.addEventListener('click', function () {
                this.classList.toggle('fa-solid');
                this.classList.toggle('fa-regular');
                this.style.color = this.classList.contains('fa-solid') ? '#e03a3c' : '#000';
            });
        });
    }

    // ===== 4. TABS =====
    const tabs = document.querySelectorAll('.nav-tabs-custom .nav-link');
    const search = document.querySelector('.search-bar');

    tabs.forEach(tab => {
        tab.addEventListener('click', function () {

            tabs.forEach(t => {
                t.classList.remove('active');
                const icon = t.querySelector('i');
                if (icon) icon.classList.replace('fa-square-check', 'fa-square');
            });

            this.classList.add('active');

            const icon = this.querySelector('i');
            if (icon) icon.classList.replace('fa-square', 'fa-square-check');

            const category = this.getAttribute('data-category');

            if (search) {
                search.placeholder =
                    category === 'all'
                        ? "Places to go, Hotels..."
                        : `Search for ${category}...`;
            }
        });
    });

    // ===== 5. HERO SLIDER =====
    const hero = document.getElementById("heroImage");

    if (hero) {
        const images = ["images/hero1.jpg", "images/hero2.jpg", "images/hero3.jpg"];
        let i = 0;

        setInterval(() => {
            hero.style.opacity = 0;

            setTimeout(() => {
                i = (i + 1) % images.length;
                hero.src = images[i];
                hero.style.opacity = 1;
            }, 500);

        }, 4000);
    }
});


// ===== 6. SCROLL EFFECT =====
window.addEventListener('scroll', () => {
    const sticky = document.querySelector('.category-sticky-wrapper');
    if (!sticky) return;

    sticky.classList.toggle('scrolled', window.scrollY > 150);
});