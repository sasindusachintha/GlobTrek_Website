document.addEventListener('DOMContentLoaded', function () {
    const travelPackages = [
        {
            name: "Santorini Breeze",
            price: "LKR 12,000",
            desc: "Discover the breathtaking blue waters and iconic white architecture of Greece.",
            transport: "Ferry & Luxury Bus",
            duration: "3 Days, 2 Nights",
            rating: "4.8 (120 reviews)",
            images: [
                "images/package1_2.jpg",
                "images/package1.webp",
                "images/package1_3.webp",
                "images/package1_4.webp"
            ],
            highlights: [
                "Sunset cruise along the caldera",
                "Private guided island tour",
                "Luxury waterfront villa stay",
                "Authentic Greek dining experience"
            ],
            itinerary: [
                { day: "Day 1", activity: "Arrival in Santorini and welcome reception at the hotel." },
                { day: "Day 2", activity: "Explore Oia, the caldera villages, and watch the famous sunset." },
                { day: "Day 3", activity: "Ferry excursion to nearby islands and leisure time at the beach." }
            ],
            included: [
                "3-star hotel accommodation",
                "Daily breakfast and dinner",
                "Airport transfers and local transport",
                "Guided sightseeing tour"
            ]
        },
        {
            name: "Bali Retreat",
            price: "LKR 15,000",
            desc: "Relax in the tropical paradise of Bali with villas and temples.",
            transport: "Private Van",
            duration: "5 Days, 4 Nights",
            rating: "4.9 (250 reviews)",
            images: [
                "images/package2.jpg",
                "images/package2_2.jpeg",
                "images/package2_3.webp",
                "images/package2_4.jpg"
            ],
            highlights: [
                "Secluded villa accommodation",
                "Morning yoga session",
                "Cultural temple visit",
                "Sunset dinner by the beach"
            ],
            itinerary: [
                { day: "Day 1", activity: "Arrival in Ubud and introductory village walk." },
                { day: "Day 2", activity: "Visit Tirta Empul temple and rice terrace views." },
                { day: "Day 3", activity: "Beach day in Seminyak with sunset dinner." }
            ],
            included: [
                "4-night villa stay",
                "Breakfast every morning",
                "All transfers and driver services",
                "Entrance fees to temple sites"
            ]
        },
        {
            name: "Parisian Night",
            price: "LKR 10,000",
            desc: "Enjoy the romance of Paris with Eiffel Tower dinner.",
            transport: "Metro & Walking Tour",
            duration: "2 Days, 1 Night",
            rating: "4.7 (95 reviews)",
            images: [
                "images/package3.jpg",
                "images/package3_2.jpg",
                "images/package3_3.jpeg",
                "images/package3.jpg"
            ],
            highlights: [
                "Eiffel Tower dinner experience",
                "Private Seine river cruise",
                "Champs-Élysées photo tour",
                "Boutique hotel stay"
            ],
            itinerary: [
                { day: "Day 1", activity: "Arrival and welcome stroll through the Latin Quarter." },
                { day: "Day 2", activity: "Morning city tour and evening Eiffel Tower dinner." }
            ],
            included: [
                "Romantic hotel accommodation",
                "Dinner reservation at Eiffel Tower",
                "Guided metro sightseeing tour",
                "Luxury Seine cruise"
            ]
        },
        {
            name: "Tokyo Explorer",
            price: "LKR 18,000",
            desc: "Explore Tokyo’s modern and traditional attractions.",
            transport: "Bullet Train",
            duration: "4 Days, 3 Nights",
            rating: "4.6 (180 reviews)",
            images: [
                "images/package4.webp",
                "images/package4.webp",
                "images/package4.webp",
                "images/package4.webp"
            ],
            highlights: [
                "Bullet train experience",
                "Shibuya and Harajuku walking tour",
                "Traditional tea ceremony",
                "Nightlife and street food adventure"
            ],
            itinerary: [
                { day: "Day 1", activity: "Arrival and Shinjuku evening exploration." },
                { day: "Day 2", activity: "Temple visit and shopping in Asakusa." },
                { day: "Day 3", activity: "Full-day Tokyo city tour and gourmet food tasting." }
            ],
            included: [
                "3-night city hotel stay",
                "Daily breakfast",
                "Train transfers and city pass",
                "Cultural activity fees"
            ]
        },
        {
            name: "Swiss Alps",
            price: "LKR 20,000",
            desc: "Mountain adventure with skiing and scenic views.",
            transport: "Cable Car",
            duration: "6 Days, 5 Nights",
            rating: "4.9 (310 reviews)",
            images: [
                "images/package5.jpeg",
                "images/package5.jpeg",
                "images/package5.jpeg",
                "images/package5.jpeg"
            ],
            highlights: [
                "Panoramic alpine views",
                "Cable car mountain ascent",
                "Cosy chalet dining",
                "Village exploration"
            ],
            itinerary: [
                { day: "Day 1", activity: "Arrival in the Alps and lodge check-in." },
                { day: "Day 2", activity: "Cable car ride and alpine village tour." },
                { day: "Day 3", activity: "Optional ski experience and mountain spa time." }
            ],
            included: [
                "Comfortable chalet accommodation",
                "Breakfast and dinner",
                "Cable car tickets",
                "Guided mountain walk"
            ]
        },
        {
            name: "Dubai Safari",
            price: "LKR 14,000",
            desc: "Desert safari with luxury experience.",
            transport: "4x4 Jeep",
            duration: "3 Days, 2 Nights",
            rating: "4.5 (150 reviews)",
            images: [
                "images/package6.webp",
                "images/package6.webp",
                "images/package6.webp",
                "images/package6.webp"
            ],
            highlights: [
                "Desert dune drive",
                "Luxury camp dining",
                "Sunset desert views",
                "Cultural entertainment show"
            ],
            itinerary: [
                { day: "Day 1", activity: "Arrival and city highlights tour." },
                { day: "Day 2", activity: "Morning leisure and evening desert safari." },
                { day: "Day 3", activity: "Departure after a relaxed breakfast." }
            ],
            included: [
                "2-night hotel stay",
                "Desert safari experience",
                "Dinner under the stars",
                "Transfers and guide services"
            ]
        },
        {
            name: "London Classic",
            price: "LKR 14,000",
            desc: "Historic tour of London landmarks.",
            transport: "Bus",
            duration: "3 Days, 2 Nights",
            rating: "4.4 (200 reviews)",
            images: [
                "images/package7.webp",
                "images/package7.webp",
                "images/package7.webp",
                "images/package7.webp"
            ],
            highlights: [
                "Classic city landmarks",
                "River Thames cruise",
                "Royal palace tour",
                "Historic walking route"
            ],
            itinerary: [
                { day: "Day 1", activity: "Arrival and Westminster tour." },
                { day: "Day 2", activity: "Full-day city highlights with river cruise." },
                { day: "Day 3", activity: "Free morning and departure." }
            ],
            included: [
                "City hotel stay",
                "Daily breakfast",
                "Guided city tour",
                "Thames river cruise"
            ]
        },
        {
            name: "Maldives Blue",
            price: "LKR 14,000",
            desc: "Luxury island experience with clear waters.",
            transport: "Speedboat",
            duration: "4 Days, 3 Nights",
            rating: "5.0 (80 reviews)",
            images: [
                "images/package8.webp",
                "images/package8.webp",
                "images/package8.webp",
                "images/package8.webp"
            ],
            highlights: [
                "Premium island resort stay",
                "Speedboat water transfers",
                "Beachfront dining",
                "Snorkeling adventure"
            ],
            itinerary: [
                { day: "Day 1", activity: "Arrival and transfer to the island resort." },
                { day: "Day 2", activity: "Beach day with snorkeling and leisure time." },
                { day: "Day 3", activity: "Optional spa or water sports experience." }
            ],
            included: [
                "3-night beachfront resort stay",
                "Daily breakfast",
                "Speedboat transfers",
                "Welcome dinner"
            ]
        }
    ];

    travelPackages.forEach((pkg, idx) => { pkg.id = idx; });

    const urlParams = new URLSearchParams(window.location.search);
    const id = Number(urlParams.get('id'));

    const pkg = travelPackages.find(packageItem => packageItem.id === id);
    if (!pkg) return;

    document.getElementById('detailTitle').innerText = pkg.name;
    document.getElementById('detailDesc').innerText = pkg.desc;
    document.getElementById('detailPrice').innerText = pkg.price;
    document.getElementById('detailDuration').innerText = pkg.duration;
    document.getElementById('detailTransport').innerText = pkg.transport;
    document.getElementById('detailRating').innerText = pkg.rating;

    document.getElementById('mainImg').src = pkg.images[0];
    document.getElementById('img1').src = pkg.images[1];
    document.getElementById('img2').src = pkg.images[2];
    document.getElementById('img3').src = pkg.images[3];

    document.getElementById('packageSpecs').innerHTML = `
        <p><strong>💰 Price:</strong> ${pkg.price}</p>
        <p><strong>🚌 Transport:</strong> ${pkg.transport}</p>
        <p><strong>📅 Duration:</strong> ${pkg.duration}</p>
        <p><strong>⭐ Rating:</strong> ${pkg.rating}</p>
    `;

    const highlightsContainer = document.getElementById('packageHighlights');
    highlightsContainer.innerHTML = pkg.highlights
        .map(item => `<li class="list-group-item"><i class="fa-solid fa-star"></i>${item}</li>`)
        .join('');

    const itineraryContainer = document.getElementById('itineraryList');
    itineraryContainer.innerHTML = pkg.itinerary
        .map(step => `
            <div class="itinerary-step mb-4">
                <h6>${step.day}</h6>
                <p>${step.activity}</p>
            </div>
        `)
        .join('');

    const includedContainer = document.getElementById('includedList');
    includedContainer.innerHTML = pkg.included
        .map(item => `<li><i class="fa-solid fa-check"></i>${item}</li>`)
        .join('');

    const bookNowBtn = document.getElementById('bookNowBtn');
    if (bookNowBtn) {
        bookNowBtn.addEventListener('click', () => {
            window.location.href = `booking.html?id=${id}`;
        });
    }
});
