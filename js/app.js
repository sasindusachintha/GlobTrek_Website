// AUTO LOGIN (temporary fix)
if (!localStorage.getItem("currentUser")) {
    localStorage.setItem("currentUser", JSON.stringify({
        name: "Admin User",
        email: "admin@test.com",
        role: "admin"
    }));
}

// ===== STORAGE HELPERS =====
function getStorage(key) {
    return JSON.parse(localStorage.getItem(key)) || [];
}

function setStorage(key, value) {
    localStorage.setItem(key, JSON.stringify(value));
}

// ===== USER HELPERS =====
function getCurrentUser() {
    return JSON.parse(localStorage.getItem('currentUser'));
}

function setCurrentUser(user) {
    localStorage.setItem('currentUser', JSON.stringify(user));
}

function clearCurrentUser() {
    localStorage.removeItem('currentUser');
}

// ===== ROLE NORMALIZATION =====
function normalizeRole(role) {
    if (!role) return 'user';
    role = role.toLowerCase();
    if (role === 'admin') return 'admin';
    if (role === 'staff') return 'staff';
    return 'user';
}

// ===== MENU BUILDER =====
function buildDashboardMenu(items) {
    const menu = document.getElementById('dashboardMenu');
    if (!menu) return;

    menu.innerHTML = '';

    items.forEach(item => {
        const button = document.createElement('button');
        button.className = 'list-group-item list-group-item-action';
        button.innerText = item.label;

        button.addEventListener('click', () => {
            loadDashboardSection(item.id);
        });

        menu.appendChild(button);
    });
}

// ===== DEFAULT DASHBOARD CONTENT =====
function loadDashboardSection(section) {
    const content = document.getElementById('dashboardContent');

    content.innerHTML = `<h4>${section}</h4><p>Content loading...</p>`;
}

const defaultUsers = [
    { id: 1, name: 'Admin User', email: 'admin@globetrek.com', password: 'Admin123', role: 'admin', approved: true },
    { id: 2, name: 'Staff User', email: 'staff@globetrek.com', password: 'Staff123', role: 'staff', approved: true },
    { id: 3, name: 'Customer User', email: 'user@globetrek.com', password: 'User123', role: 'user', approved: true }
];

const defaultPackages = [
    {
        id: 0,
        name: 'Santorini Breeze',
        price: 'LKR 12,000',
        desc: 'Discover the breathtaking blue waters and iconic white architecture of Greece.',
        transport: 'Ferry & Luxury Bus',
        duration: '3 Days, 2 Nights',
        rating: '4.8 (120 reviews)',
        accommodation: 'Luxury waterfront villa',
        transportation: 'Private transfers and local bus trips',
        guide: 'English-speaking island guide',
        images: [
            'images/package1_2.jpg',
            'images/package1.webp',
            'images/package1_3.webp',
            'images/package1_4.webp'
        ],
        highlights: ['Sunset cruise along the caldera', 'Private guided island tour', 'Luxury waterfront villa stay', 'Authentic Greek dining experience'],
        itinerary: [
            { day: 'Day 1', activity: 'Arrival and welcome reception at the hotel.' },
            { day: 'Day 2', activity: 'Explore Oia, the caldera villages, and sunset view.' },
            { day: 'Day 3', activity: 'Ferry excursion and leisure beach time.' }
        ],
        included: ['3-night hotel accommodation', 'Daily breakfast and dinner', 'Airport transfers', 'Guided sightseeing tour']
    },
    {
        id: 1,
        name: 'Bali Retreat',
        price: 'LKR 15,000',
        desc: 'Relax in the tropical paradise of Bali with villas and temples.',
        transport: 'Private Van',
        duration: '5 Days, 4 Nights',
        rating: '4.9 (250 reviews)',
        accommodation: 'Private villa with pool',
        transportation: 'Private van transfers',
        guide: 'Local cultural guide',
        images: [
            'images/package2.jpg',
            'images/package2_2.jpeg',
            'images/package2_3.webp',
            'images/package2_4.jpg'
        ],
        highlights: ['Secluded villa stay', 'Morning yoga session', 'Cultural temple visit', 'Sunset beach dinner'],
        itinerary: [
            { day: 'Day 1', activity: 'Arrival in Ubud and village walk.' },
            { day: 'Day 2', activity: 'Visit Tirta Empul temple and rice terraces.' },
            { day: 'Day 3', activity: 'Beach day in Seminyak with sunset dinner.' }
        ],
        included: ['4-night villa stay', 'Breakfast daily', 'Transfers and driver services', 'Temple entrance fees']
    },
    {
        id: 2,
        name: 'Parisian Night',
        price: 'LKR 10,000',
        desc: 'Enjoy the romance of Paris with Eiffel Tower dinner.',
        transport: 'Metro & Walking Tour',
        duration: '2 Days, 1 Night',
        rating: '4.7 (95 reviews)',
        accommodation: 'Boutique city hotel',
        transportation: 'Metro city pass',
        guide: 'Paris city guide',
        images: [
            'images/package3.jpg',
            'images/package3_2.jpg',
            'images/package3_3.jpeg',
            'images/package3.jpg'
        ],
        highlights: ['Eiffel Tower dinner', 'Seine river cruise', 'Champs-Élysées tour', 'Boutique hotel stay'],
        itinerary: [
            { day: 'Day 1', activity: 'Arrival and Latin Quarter stroll.' },
            { day: 'Day 2', activity: 'City tour and evening dinner by the tower.' }
        ],
        included: ['Hotel accommodation', 'Eiffel Tower dinner', 'Metro sightseeing pass', 'Seine cruise']
    },
    {
        id: 3,
        name: 'Tokyo Explorer',
        price: 'LKR 18,000',
        desc: 'Explore Tokyo’s modern and traditional attractions.',
        transport: 'Bullet Train',
        duration: '4 Days, 3 Nights',
        rating: '4.6 (180 reviews)',
        accommodation: 'City hotel',
        transportation: 'Bullet train tickets',
        guide: 'Tokyo cultural guide',
        images: [
            'images/package4.webp',
            'images/package4.webp',
            'images/package4.webp',
            'images/package4.webp'
        ],
        highlights: ['Bullet train experience', 'Shibuya and Harajuku tour', 'Tea ceremony', 'Night street food adventure'],
        itinerary: [
            { day: 'Day 1', activity: 'Arrival and Shinjuku exploration.' },
            { day: 'Day 2', activity: 'Asakusa temples and city tour.' },
            { day: 'Day 3', activity: 'Full-day Tokyo highlight tour.' }
        ],
        included: ['City hotel stay', 'Daily breakfast', 'Train transfers', 'Cultural activities']
    },
    {
        id: 4,
        name: 'Swiss Alps',
        price: 'LKR 20,000',
        desc: 'Mountain adventure with skiing and scenic views.',
        transport: 'Cable Car',
        duration: '6 Days, 5 Nights',
        rating: '4.9 (310 reviews)',
        accommodation: 'Alpine chalet',
        transportation: 'Cable car pass',
        guide: 'Alpine mountain guide',
        images: [
            'images/package5.jpeg',
            'images/package5.jpeg',
            'images/package5.jpeg',
            'images/package5.jpeg'
        ],
        highlights: ['Alpine views', 'Cable car ride', 'Chalet dining', 'Village tour'],
        itinerary: [
            { day: 'Day 1', activity: 'Arrival and lodge check-in.' },
            { day: 'Day 2', activity: 'Cable car and mountain walk.' },
            { day: 'Day 3', activity: 'Optional ski or spa day.' }
        ],
        included: ['Chalet stay', 'Breakfast and dinner', 'Cable car pass', 'Guided mountain walk']
    },
    {
        id: 5,
        name: 'Dubai Safari',
        price: 'LKR 14,000',
        desc: 'Desert safari with luxury experience.',
        transport: '4x4 Jeep',
        duration: '3 Days, 2 Nights',
        rating: '4.5 (150 reviews)',
        accommodation: 'Desert camp',
        transportation: '4x4 transfers',
        guide: 'Safari guide',
        images: [
            'images/package6.webp',
            'images/package6.webp',
            'images/package6.webp',
            'images/package6.webp'
        ],
        highlights: ['Desert dune drive', 'Camp dinner', 'Sunset views', 'Entertainment show'],
        itinerary: [
            { day: 'Day 1', activity: 'Arrival and city highlights.' },
            { day: 'Day 2', activity: 'Morning rest and evening safari.' },
            { day: 'Day 3', activity: 'Departure after breakfast.' }
        ],
        included: ['2-night hotel stay', 'Desert safari experience', 'Dinner under the stars', 'Transfers and guide service']
    },
    {
        id: 6,
        name: 'London Classic',
        price: 'LKR 14,000',
        desc: 'Historic tour of London landmarks.',
        transport: 'Bus',
        duration: '3 Days, 2 Nights',
        rating: '4.4 (200 reviews)',
        accommodation: 'City hotel',
        transportation: 'Luxury coach',
        guide: 'Historical city guide',
        images: [
            'images/package7.webp',
            'images/package7.webp',
            'images/package7.webp',
            'images/package7.webp'
        ],
        highlights: ['Classic city landmarks', 'River Thames cruise', 'Royal palace tour', 'Historic walking route'],
        itinerary: [
            { day: 'Day 1', activity: 'Arrival and Westminster tour.' },
            { day: 'Day 2', activity: 'City highlights and river cruise.' },
            { day: 'Day 3', activity: 'Departure after breakfast.' }
        ],
        included: ['City hotel stay', 'Daily breakfast', 'Guided city tour', 'Thames river cruise']
    },
    {
        id: 7,
        name: 'Maldives Blue',
        price: 'LKR 14,000',
        desc: 'Luxury island experience with clear waters.',
        transport: 'Speedboat',
        duration: '4 Days, 3 Nights',
        rating: '5.0 (80 reviews)',
        accommodation: 'Beachfront resort',
        transportation: 'Speedboat transfer',
        guide: 'Island concierge service',
        images: [
            'images/package8.webp',
            'images/package8.webp',
            'images/package8.webp',
            'images/package8.webp'
        ],
        highlights: ['Premium island resort', 'Snorkeling adventure', 'Beachfront dining', 'Sunset speedboat trip'],
        itinerary: [
            { day: 'Day 1', activity: 'Arrival and resort welcome.' },
            { day: 'Day 2', activity: 'Beach day and snorkeling.' },
            { day: 'Day 3', activity: 'Spa or water sports experience.' }
        ],
        included: ['Beachfront resort stay', 'Daily breakfast', 'Speedboat transfers', 'Welcome dinner']
    }
];

function storageKey(key) {
    return `globe_${key}`;
}

function getStorage(key) {
    const raw = localStorage.getItem(storageKey(key));
    if (!raw) return [];
    try {
        const parsed = JSON.parse(raw);
        if (Array.isArray(parsed)) return parsed;
        return parsed || [];
    } catch (error) {
        return [];
    }
}

function setStorage(key, value) {
    localStorage.setItem(storageKey(key), JSON.stringify(value));
}

function getCurrentUser() {
    try {
        return JSON.parse(localStorage.getItem('globe_current_user') || 'null');
    } catch (error) {
        return null;
    }
}

function setCurrentUser(user) {
    localStorage.setItem('globe_current_user', JSON.stringify(user));
}

function clearCurrentUser() {
    localStorage.removeItem('globe_current_user');
}

function ensureDefaultData() {
    if (!getStorage('users').length) {
        setStorage('users', defaultUsers);
    }

    if (!getStorage('packages').length) {
        setStorage('packages', defaultPackages);
    }

    if (!localStorage.getItem(storageKey('bookings'))) {
        setStorage('bookings', []);
    }

    if (!localStorage.getItem(storageKey('queries'))) {
        setStorage('queries', []);
    }

    if (!localStorage.getItem(storageKey('payments'))) {
        setStorage('payments', []);
    }
}

function refreshPackageData() {
    const packages = getStorage('packages');
    window.packageData = packages.length ? packages : defaultPackages;
}

function findPackage(id) {
    const packages = getStorage('packages');
    return packages.find(pkg => pkg.id === Number(id)) || defaultPackages.find(pkg => pkg.id === Number(id));
}

function findUser(email) {
    return getStorage('users').find(user => user.email.toLowerCase() === email.toLowerCase());
}

function normalizeRole(role) {
    const value = String(role || '').trim().toLowerCase();
    if (['admin', 'staff', 'user'].includes(value)) return value;
    if (['traveler', 'traveller', 'customer', 'guest', 'agent'].includes(value)) return value === 'agent' ? 'staff' : 'user';
    return 'user';
}

function normalizeStoredUsers() {
    const users = getStorage('users');
    let changed = false;
    users.forEach(user => {
        const normalized = normalizeRole(user.role);
        if (normalized !== user.role) {
            user.role = normalized;
            changed = true;
        }
        if (typeof user.approved !== 'boolean') {
            user.approved = user.role === 'user';
            changed = true;
        }
    });
    if (changed) setStorage('users', users);
}

function formatCurrency(value) {
    return `LKR ${Number(value).toLocaleString('en-US')}`;
}

function initRegisterForm() {
    const form = document.getElementById('registerForm');
    if (!form) return;

    const nameInput = document.getElementById('registerName');
    const emailInput = document.getElementById('registerEmail');
    const passwordInput = document.getElementById('registerPassword');
    const confirmInput = document.getElementById('registerConfirmPassword');
    const roleInput = document.getElementById('registerRole');
    const messageArea = document.getElementById('registerMessage');

    form.addEventListener('submit', event => {
        event.preventDefault();
        const name = nameInput.value.trim();
        const email = emailInput.value.trim();
        const password = passwordInput.value.trim();
        const confirm = confirmInput.value.trim();
        const role = roleInput.value;

        messageArea.innerText = '';

        if (!name || !email || !password || !confirm || !role) {
            messageArea.innerText = 'Please complete all fields.';
            return;
        }

        if (password !== confirm) {
            messageArea.innerText = 'Passwords do not match.';
            return;
        }

        if (findUser(email)) {
            messageArea.innerText = 'An account with this email already exists.';
            return;
        }

        const users = getStorage('users');
        users.push({
            id: users.length + 1,
            name,
            email,
            password,
            role,
            approved: role === 'user'
        });
        setStorage('users', users);
        messageArea.classList.remove('text-danger');
        messageArea.classList.add('text-success');
        messageArea.innerText = role === 'user' ? 'Registration complete. Please log in.' : 'Registration submitted. Admin approval is required.';
        form.reset();
        setTimeout(() => { window.location.href = 'login.html'; }, 1800);
    });
}

function initLoginForm() {
    const form = document.getElementById('loginForm');
    if (!form) return;

    const emailInput = document.getElementById('loginEmail');
    const passwordInput = document.getElementById('loginPassword');
    const messageArea = document.getElementById('loginMessage');

    form.addEventListener('submit', event => {
        event.preventDefault();
        const email = emailInput.value.trim();
        const password = passwordInput.value.trim();
        messageArea.innerText = '';

        if (!email || !password) {
            messageArea.innerText = 'Please enter both email and password.';
            return;
        }

        const user = findUser(email);
        if (!user || user.password !== password) {
            messageArea.innerText = 'Invalid email or password.';
            return;
        }

        if (user.role !== 'user' && !user.approved) {
            messageArea.innerText = 'Your account is awaiting admin approval.';
            return;
        }

        setCurrentUser(user);
        window.location.href = 'dashboard.html';
    });
}

function initBookingPage() {

    // stop if not booking page
    if (!document.getElementById("bookingForm")) return;

    const form = document.getElementById('bookingForm');
    const packageName = document.getElementById('packageName');
    const packageDuration = document.getElementById('packageDuration');
    const packageTransport = document.getElementById('packageTransport');
    const packagePrice = document.getElementById('packagePrice');
    const packageDescription = document.getElementById('packageDescription');

    const travelDate = document.getElementById('travelDate');
    const returnDate = document.getElementById('returnDate');
    const guestsInput = document.getElementById('guestCount');
    const nameInput = document.getElementById('bookingName');
    const emailInput = document.getElementById('bookingEmail');
    const phoneInput = document.getElementById('bookingPhone');
    const messageArea = document.getElementById('bookingMessage');

    const params = new URLSearchParams(window.location.search);
    const packageId = params.get('id');
    const pkg = findPackage(packageId);

    // SAFE UI updates
    if (pkg) {
        if (packageName) packageName.innerText = pkg.name;
        if (packageDuration) packageDuration.innerText = pkg.duration;
        if (packageTransport) packageTransport.innerText = pkg.transport;
        if (packagePrice) packagePrice.innerText = pkg.price;
        if (packageDescription) packageDescription.innerText = pkg.desc;
    }

    const currentUser = getCurrentUser();
    if (currentUser) {
        if (nameInput) nameInput.value = currentUser.name;
        if (emailInput) emailInput.value = currentUser.email;
    }

    if (!form) return;

    form.addEventListener('submit', event => {
        event.preventDefault();

        if (messageArea) messageArea.innerText = '';

        if (!pkg) {
            if (messageArea) messageArea.innerText = 'Please select a valid package first.';
            return;
        }

        const name = nameInput?.value.trim();
        const email = emailInput?.value.trim();
        const phone = phoneInput?.value.trim();
        const travel = travelDate?.value;
        const ret = returnDate?.value;
        const guests = guestsInput?.value.trim();

        if (!name || !email || !phone || !travel || !ret || !guests) {
            if (messageArea) messageArea.innerText = 'Please complete all booking fields.';
            return;
        }

        const bookings = getStorage('bookings');
        const bookingId = bookings.length ? bookings[bookings.length - 1].id + 1 : 1;

        const booking = {
            id: bookingId,
            userEmail: email,
            userName: name,
            packageId: pkg.id,
            packageName: pkg.name,
            travelDate: travel,
            returnDate: ret,
            guests: Number(guests),
            status: 'Pending',
            createdAt: new Date().toISOString(),
            total: 13650
        };

        bookings.push(booking);
        setStorage('bookings', bookings);

        window.location.href = `payment.html?bookingId=${bookingId}`;
    });
}

function initPaymentPage() {
    const paymentForm = document.getElementById('paymentForm');
    const paymentPackageName = document.getElementById('paymentPackageName');
    const paymentPackageSummary = document.getElementById('paymentPackageSummary');
    const paymentTotal = document.getElementById('paymentTotal');
    const paymentMessage = document.getElementById('paymentMessage');
    const bookingId = Number(new URLSearchParams(window.location.search).get('bookingId'));
    const bookings = getStorage('bookings');
    const booking = bookings.find(b => b.id === bookingId);

    if (booking && paymentPackageName) {
        paymentPackageName.innerText = booking.packageName;
        paymentPackageSummary.innerText = `${booking.travelDate} to ${booking.returnDate}`;
        paymentTotal.innerText = `LKR ${booking.total.toLocaleString('en-US')}`;
    }

    if (!paymentForm) return;
    paymentForm.addEventListener('submit', event => {
        event.preventDefault();
        paymentMessage.innerText = '';
        if (!booking) {
            paymentMessage.innerText = 'No booking found to pay for.';
            return;
        }

        const payments = getStorage('payments');
        payments.push({
            id: payments.length + 1,
            bookingId: booking.id,
            userEmail: booking.userEmail,
            amount: booking.total,
            status: 'Paid',
            date: new Date().toISOString()
        });
        setStorage('payments', payments);

        const updatedBookings = bookings.map(b => b.id === booking.id ? { ...b, status: 'Confirmed' } : b);
        setStorage('bookings', updatedBookings);

        paymentMessage.classList.remove('text-danger');
        paymentMessage.classList.add('text-success');
        paymentMessage.innerText = 'Payment successful. Your booking is confirmed.';
        setTimeout(() => { window.location.href = 'dashboard.html'; }, 1800);
    });
}

function initContactPage() {
    const form = document.getElementById('queryForm');
    if (!form) return;
    const nameInput = document.getElementById('queryName');
    const emailInput = document.getElementById('queryEmail');
    const messageInput = document.getElementById('queryMessage');
    const messageArea = document.getElementById('queryMessageArea');

    form.addEventListener('submit', event => {
        event.preventDefault();
        const name = nameInput.value.trim();
        const email = emailInput.value.trim();
        const message = messageInput.value.trim();

        if (!name || !email || !message) {
            messageArea.innerText = 'Please complete all fields.';
            return;
        }

        const queries = getStorage('queries');
        queries.push({
            id: queries.length + 1,
            name,
            email,
            message,
            submittedAt: new Date().toISOString(),
            status: 'Open',
            response: ''
        });
        setStorage('queries', queries);
        messageArea.classList.remove('text-danger');
        messageArea.classList.add('text-success');
        messageArea.innerText = 'Your query has been submitted. We will respond shortly.';
        form.reset();
    });
}

function buildDashboardMenu(items) {
    const menu = document.getElementById('dashboardMenu');
    if (!menu) return;

    // Generate the HTML for each menu button
    menu.innerHTML = items
        .map(item => `<button class="list-group-item list-group-item-action text-start dashboard-menu-btn" data-section="${item.id}">${item.label}</button>`)
        .join('');

    const buttons = menu.querySelectorAll('.dashboard-menu-btn');
    buttons.forEach(button => {
        button.addEventListener('click', () => {
            // Remove active highlight from all buttons and add it to the clicked one
            buttons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
            
            // Load the actual content for this section
            loadDashboardSection(button.dataset.section);
        });
    });

    // Automatically trigger the first menu item so the dashboard isn't blank on load
    if (buttons.length > 0) {
        buttons[0].click();
    }
}

function renderTable(rows, headers) {
    return `
        <div class="table-responsive">
            <table class="table table-borderless align-middle">
                <thead>
                    <tr>${headers.map(header => `<th>${header}</th>`).join('')}</tr>
                </thead>
                <tbody>
                    ${rows.join('')}
                </tbody>
            </table>
        </div>
    `;
}

function getActionButtons(section, userRole) {
    const buttons = [];
    
    if (userRole === 'user') {
        if (section === 'bookings') buttons.push({ label: 'New Booking', id: 'newBooking', class: 'btn-primary' });
        if (section === 'queries') buttons.push({ label: 'Submit Query', id: 'submitQuery', class: 'btn-primary' });
        if (section === 'customize') buttons.push({ label: 'Save Plan', id: 'savePlan', class: 'btn-success' });
    }
    
    if (userRole === 'staff') {
        if (section === 'confirmBookings') buttons.push({ label: 'Refresh', id: 'refreshBookings', class: 'btn-info' });
        if (section === 'customerQueries') buttons.push({ label: 'Export Queries', id: 'exportQueries', class: 'btn-secondary' });
        if (section === 'managePackages') buttons.push({ label: 'Add Package', id: 'addPackage', class: 'btn-primary' });
    }
    
    if (userRole === 'admin') {
        if (section === 'manageUsers') buttons.push({ label: 'Bulk Actions', id: 'bulkActions', class: 'btn-warning' });
        if (section === 'confirmStaff') buttons.push({ label: 'Reset Approvals', id: 'resetApprovals', class: 'btn-danger' });
        if (section === 'reports') buttons.push({ label: 'Download Report', id: 'downloadReport', class: 'btn-success' });
    }
    
    return buttons;
}

function renderActionButtons(section, userRole) {
    const buttons = getActionButtons(section, userRole);
    if (!buttons.length) return '';
    
    return `
        <div class="mb-3 d-flex gap-2">
            ${buttons.map(btn => `<button class="btn ${btn.class} btn-sm" id="${btn.id}">${btn.label}</button>`).join('')}
        </div>
    `;
}

function loadDashboardSection(section) {
    const content = document.getElementById('dashboardContent');
    const user = getCurrentUser();
    if (!content || !user) return;

    const bookings = getStorage('bookings');
    const payments = getStorage('payments');
    const queries = getStorage('queries');
    const users = getStorage('users');
    const packages = getStorage('packages');

    const userBookings = bookings.filter(b => b.userEmail.toLowerCase() === user.email.toLowerCase());
    const userQueries = queries.filter(q => q.email.toLowerCase() === user.email.toLowerCase());
    const userPayments = payments.filter(p => p.userEmail.toLowerCase() === user.email.toLowerCase());

    switch (section) {
        case 'companyProfile':
            content.innerHTML = `
                <h4>GlobeTrek Company Profile</h4>
                <p>GlobeTrek Adventures is a full-service travel platform offering curated tour packages, accommodation options, transportation services, and expert travel guides.</p>
                <ul class="list-group list-group-flush mb-4">
                    <li class="list-group-item"><strong>Destinations:</strong> Global beach escapes, city breaks, mountain adventures, cultural tours.</li>
                    <li class="list-group-item"><strong>Accommodation:</strong> Resorts, boutique hotels, chalets, villas, and beachfront stays.</li>
                    <li class="list-group-item"><strong>Transportation:</strong> Private transfers, trains, boats, buses, and airport shuttles.</li>
                    <li class="list-group-item"><strong>Guides:</strong> Local experts, cultural specialists, and multilingual travel hosts.</li>
                </ul>
                <h5>Our platform capabilities</h5>
                <p>Customers can browse packages, book trips, customize travel plans, make secure payments, and submit queries. Staff can update packages, confirm bookings, coordinate hotels and transport, and support customers. Admins manage staff, oversee booking operations, generate reports, and protect user data.</p>
            `;
            break;
        case 'tourPackages':
            content.innerHTML = `
                <h4>Available Tour Packages</h4>
                <div class="row g-3">
                    ${packages.map(pkg => `
                        <div class="col-md-6 col-xl-4">
                            <div class="card h-100 shadow-sm border-0">
                                <img src="${pkg.images[0]}" class="card-img-top" alt="${pkg.name}">
                                <div class="card-body">
                                    <h5 class="card-title">${pkg.name}</h5>
                                    <p class="card-text text-muted mb-2">${pkg.desc}</p>
                                    <p class="mb-1"><strong>Duration:</strong> ${pkg.duration}</p>
                                    <p class="mb-1"><strong>Transport:</strong> ${pkg.transport}</p>
                                    <p class="mb-1"><strong>Price:</strong> ${pkg.price}</p>
                                </div>
                            </div>
                        </div>
                    `).join('')}
                </div>
            `;
            break;
        case 'profile':
            content.innerHTML = `
                <h4>My Profile</h4>
                <p><strong>Name:</strong> ${user.name}</p>
                <p><strong>Email:</strong> ${user.email}</p>
                <p><strong>Role:</strong> ${user.role}</p>
                <p><strong>Status:</strong> ${user.approved ? 'Active' : 'Pending approval'}</p>
            `;
            break;
        case 'bookings':
            content.innerHTML = `<h4>My Bookings</h4>${renderActionButtons('bookings', user.role)}${userBookings.length ? renderTable(userBookings.map(booking => `
                <tr>
                    <td>${booking.packageName}</td>
                    <td>${booking.travelDate} - ${booking.returnDate}</td>
                    <td>${booking.guests}</td>
                    <td><span class="badge ${booking.status === 'Confirmed' ? 'bg-success' : 'bg-warning'}">${booking.status}</span></td>
                    <td><button class="btn btn-sm btn-outline-danger cancel-booking" data-id="${booking.id}">Cancel</button></td>
                </tr>`), ['Package', 'Travel Date', 'Guests', 'Status', 'Action']) : '<p>No bookings found. <a href="packages.html">Browse packages</a></p>'}`;
            document.querySelectorAll('.cancel-booking').forEach(button => {
                button.addEventListener('click', () => {
                    const bookingId = Number(button.dataset.id);
                    const updatedBookings = bookings.map(b => b.id === bookingId ? { ...b, status: 'Cancelled' } : b);
                    setStorage('bookings', updatedBookings);
                    loadDashboardSection('bookings');
                });
            });
            document.getElementById('newBooking')?.addEventListener('click', () => {
                window.location.href = 'packages.html';
            });
            break;
        case 'customize':
            content.innerHTML = `
                <h4>Customize Travel Plan</h4>
                <p>Create a custom travel request for our team to review.</p>
                <form id="customPlanForm">
                    <div class="mb-3"><label class="form-label">Desired destination</label><input class="form-control" id="customDestination"></div>
                    <div class="mb-3"><label class="form-label">Preferred dates</label><input class="form-control" id="customDates" placeholder="e.g. June 10 - 15"></div>
                    <div class="mb-3"><label class="form-label">Special requests</label><textarea class="form-control" id="customNotes" rows="4"></textarea></div>
                    <button class="btn btn-primary" type="submit">Submit request</button>
                    <div id="customPlanMessage" class="mt-3"></div>
                </form>
            `;
            const customForm = document.getElementById('customPlanForm');
            customForm?.addEventListener('submit', event => {
                event.preventDefault();
                const destination = document.getElementById('customDestination').value.trim();
                const dates = document.getElementById('customDates').value.trim();
                const notes = document.getElementById('customNotes').value.trim();
                const messageBox = document.getElementById('customPlanMessage');
                if (!destination || !dates || !notes) {
                    messageBox.innerText = 'Please complete all fields.';
                    messageBox.className = 'text-danger mt-3';
                    return;
                }
                const planRequests = getStorage('planRequests');
                planRequests.push({ id: planRequests.length + 1, userEmail: user.email, destination, dates, notes, status: 'Submitted' });
                setStorage('planRequests', planRequests);
                messageBox.innerText = 'Plan request submitted successfully.';
                messageBox.className = 'text-success mt-3';
                customForm.reset();
            });
            break;
        case 'queries':
            content.innerHTML = `<h4>My Queries</h4>${renderActionButtons('queries', user.role)}${userQueries.length ? renderTable(userQueries.map(query => `
                    <tr>
                        <td>${query.message}</td>
                        <td><span class="badge ${query.status === 'Responded' ? 'bg-success' : 'bg-info'}">${query.status}</span></td>
                        <td>${query.response || '<em>Awaiting response</em>'}</td>
                    </tr>`), ['Query', 'Status', 'Response']) : '<p>No submitted queries. <a href="contact.html">Submit a query</a></p>'}`;
            break;
        case 'payments':
            content.innerHTML = `
                <h4>Payments</h4>
                ${userPayments.length ? renderTable(userPayments.map(payment => `
                    <tr>
                        <td>${payment.bookingId}</td>
                        <td>LKR ${payment.amount.toLocaleString('en-US')}</td>
                        <td>${payment.status}</td>
                        <td>${new Date(payment.date).toLocaleDateString()}</td>
                    </tr>`), ['Booking', 'Amount', 'Status', 'Paid On']) : '<p>No payments recorded.</p>'}
            `;
            break;
        case 'managePackages':
            content.innerHTML = `<h4>Manage Packages</h4>${renderActionButtons('managePackages', user.role)}${renderTable(packages.map(pkg => `
                    <tr>
                        <td>${pkg.name}</td>
                        <td>${pkg.duration}</td>
                        <td>${pkg.price}</td>
                        <td><button class="btn btn-sm btn-outline-primary edit-package" data-id="${pkg.id}">Edit</button></td>
                    </tr>`), ['Name', 'Duration', 'Price', 'Action'])}`;
            document.querySelectorAll('.edit-package').forEach(button => {
                button.addEventListener('click', () => {
                    const pkgId = Number(button.dataset.id);
                    const pkg = findPackage(pkgId);
                    content.innerHTML = `
                        <h4>Edit Package</h4>
                        <form id="editPackageForm">
                            <div class="mb-3"><label class="form-label">Name</label><input class="form-control" id="packageNameEdit" value="${pkg.name}"></div>
                            <div class="mb-3"><label class="form-label">Price</label><input class="form-control" id="packagePriceEdit" value="${pkg.price}"></div>
                            <div class="mb-3"><label class="form-label">Duration</label><input class="form-control" id="packageDurationEdit" value="${pkg.duration}"></div>
                            <div class="mb-3"><label class="form-label">Description</label><textarea class="form-control" id="packageDescEdit" rows="4">${pkg.desc}</textarea></div>
                            <button class="btn btn-primary" type="submit">Save Changes</button>
                        </form>
                        <button class="btn btn-link mt-3" id="cancelEdit">Back to list</button>
                    `;
                    document.getElementById('editPackageForm').addEventListener('submit', e => {
                        e.preventDefault();
                        const packages = getStorage('packages');
                        const index = packages.findIndex(item => item.id === pkgId);
                        packages[index] = {
                            ...packages[index],
                            name: document.getElementById('packageNameEdit').value.trim(),
                            price: document.getElementById('packagePriceEdit').value.trim(),
                            duration: document.getElementById('packageDurationEdit').value.trim(),
                            desc: document.getElementById('packageDescEdit').value.trim()
                        };
                        setStorage('packages', packages);
                        refreshPackageData();
                        loadDashboardSection('managePackages');
                    });
                    document.getElementById('cancelEdit').addEventListener('click', () => loadDashboardSection('managePackages'));
                });
            });
            break;
        case 'confirmBookings':
            content.innerHTML = `<h4>Confirm Bookings</h4>${renderActionButtons('confirmBookings', user.role)}${renderTable(bookings.map(booking => `
                    <tr>
                        <td>${booking.packageName}</td>
                        <td>${booking.userName}</td>
                        <td>${booking.travelDate}</td>
                        <td><span class="badge ${booking.status === 'Confirmed' ? 'bg-success' : 'bg-warning'}">${booking.status}</span></td>
                        <td><button class="btn btn-sm btn-outline-success confirm-booking" data-id="${booking.id}" ${booking.status === 'Confirmed' ? 'disabled' : ''}>Confirm</button></td>
                    </tr>`), ['Package', 'Customer', 'Travel Date', 'Status', 'Action'])}`;
            document.querySelectorAll('.confirm-booking').forEach(button => {
                button.addEventListener('click', () => {
                    const bookingId = Number(button.dataset.id);
                    const updatedBookings = bookings.map(booking => booking.id === bookingId ? { ...booking, status: 'Confirmed' } : booking);
                    setStorage('bookings', updatedBookings);
                    loadDashboardSection('confirmBookings');
                });
            });
            break;
        case 'hotels':
            content.innerHTML = `
                <h4>Hotels Coordination</h4>
                <p>Keep the hotel teams updated and review availability.</p>
                <div class="list-group">
                    <div class="list-group-item">Ocean View Resort - Available</div>
                    <div class="list-group-item">Mountain Lodge - Booking pending</div>
                    <div class="list-group-item">City Central Hotel - Confirmed</div>
                </div>
            `;
            break;
        case 'transport':
            content.innerHTML = `
                <h4>Transport Providers</h4>
                <p>Coordinate vehicle assignments and route planning.</p>
                <div class="list-group">
                    <div class="list-group-item">Luxury Bus Fleet - Ready</div>
                    <div class="list-group-item">4x4 Jeep Service - Scheduled</div>
                    <div class="list-group-item">Speedboat Operator - Confirmed</div>
                </div>
            `;
            break;
        case 'customerQueries':
            content.innerHTML = `<h4>Customer Queries</h4>${renderActionButtons('customerQueries', user.role)}${queries.length ? renderTable(queries.map(query => `
                    <tr>
                        <td>${query.name}</td>
                        <td>${query.email}</td>
                        <td>${query.message}</td>
                        <td>${query.status}</td>
                        <td><button class="btn btn-sm btn-outline-primary reply-query" data-id="${query.id}">Reply</button></td>
                    </tr>`), ['Name', 'Email', 'Query', 'Status', 'Action']) : '<p>No queries found.</p>'}`;
            document.querySelectorAll('.reply-query').forEach(button => {
                button.addEventListener('click', () => {
                    const queryId = Number(button.dataset.id);
                    const query = queries.find(q => q.id === queryId);
                    content.innerHTML = `
                        <h4>Reply to Query</h4>
                        <p><strong>${query.name}</strong> (${query.email})</p>
                        <p>${query.message}</p>
                        <form id="replyQueryForm">
                            <div class="mb-3"><label class="form-label">Response</label><textarea class="form-control" id="queryResponse" rows="4">${query.response || ''}</textarea></div>
                            <button class="btn btn-primary" type="submit">Send Response</button>
                            <button class="btn btn-link" id="backToQueries" type="button">Back</button>
                        </form>
                    `;
                    document.getElementById('replyQueryForm').addEventListener('submit', e => {
                        e.preventDefault();
                        const response = document.getElementById('queryResponse').value.trim();
                        const updatedQueries = queries.map(q => q.id === queryId ? { ...q, response, status: 'Responded' } : q);
                        setStorage('queries', updatedQueries);
                        loadDashboardSection('customerQueries');
                    });
                    document.getElementById('backToQueries').addEventListener('click', () => loadDashboardSection('customerQueries'));
                });
            });
            document.getElementById('exportQueries')?.addEventListener('click', () => {
                const csvContent = 'data:text/csv;charset=utf-8,' +
                    ['Name,Email,Message,Status,Response']
                        .concat(queries.map(q => `"${q.name}","${q.email}","${q.message}","${q.status}","${q.response || ''}"`))
                        .join('\n');
                const encodedUri = encodeURI(csvContent);
                const link = document.createElement('a');
                link.setAttribute('href', encodedUri);
                link.setAttribute('download', 'customer-queries.csv');
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            });
            break;
        case 'manageUsers':
            content.innerHTML = `<h4>Manage Users</h4>${renderActionButtons('manageUsers', user.role)}${renderTable(users.map(userItem => `
                    <tr>
                        <td>${userItem.name}</td>
                        <td>${userItem.email}</td>
                        <td><span class="badge badge-outline">${userItem.role}</span></td>
                        <td><span class="badge ${userItem.approved ? 'bg-success' : 'bg-warning'}">${userItem.approved ? 'Active' : 'Pending'}</span></td>
                        <td><button class="btn btn-sm btn-outline-secondary toggle-user" data-email="${userItem.email}">${userItem.approved ? 'Deactivate' : 'Approve'}</button></td>
                    </tr>`), ['Name', 'Email', 'Role', 'Status', 'Action'])}`;
            document.querySelectorAll('.toggle-user').forEach(button => {
                button.addEventListener('click', () => {
                    const email = button.dataset.email;
                    const updatedUsers = users.map(item => item.email === email ? { ...item, approved: !item.approved } : item);
                    setStorage('users', updatedUsers);
                    loadDashboardSection('manageUsers');
                });
            });
            break;
        case 'confirmStaff':
            content.innerHTML = `<h4>Confirm Staff</h4>${renderActionButtons('confirmStaff', user.role)}${users.filter(item => item.role === 'staff').length ? renderTable(users.filter(item => item.role === 'staff').map(staff => `
                    <tr>
                        <td>${staff.name}</td>
                        <td>${staff.email}</td>
                        <td>${staff.approved ? 'Approved' : 'Pending'}</td>
                        <td><button class="btn btn-sm btn-outline-secondary toggle-staff" data-email="${staff.email}">${staff.approved ? 'Revoke' : 'Approve'}</button></td>
                    </tr>`), ['Name', 'Email', 'Status', 'Action']) : '<p>No staff accounts yet.</p>'}`;
            document.querySelectorAll('.toggle-staff').forEach(button => {
                button.addEventListener('click', () => {
                    const email = button.dataset.email;
                    const updatedUsers = users.map(item => item.email === email ? { ...item, approved: !item.approved } : item);
                    setStorage('users', updatedUsers);
                    loadDashboardSection('confirmStaff');
                });
            });
            document.getElementById('resetApprovals')?.addEventListener('click', () => {
                const updatedUsers = users.map(item => item.role === 'staff' ? { ...item, approved: false } : item);
                setStorage('users', updatedUsers);
                loadDashboardSection('confirmStaff');
            });
            break;
        case 'overseeBooking':
            content.innerHTML = `<h4>Oversee Booking</h4>${bookings.length ? renderTable(bookings.map(booking => `
                    <tr>
                        <td>${booking.packageName}</td>
                        <td>${booking.userName}</td>
                        <td>${booking.travelDate}</td>
                        <td>${booking.status}</td>
                    </tr>`), ['Package', 'Customer', 'Travel Date', 'Status']) : '<p>No bookings available.</p>'}`;
            break;
        case 'reports':
            const bookingCount = bookings.length;
            const userCount = users.length;
            const paymentTotal = payments.reduce((sum, item) => sum + Number(item.amount), 0);
            content.innerHTML = `
                <h4>Reports & Analytics</h4>
                ${renderActionButtons('reports', user.role)}
                <div class="row g-3 mb-4">
                    <div class="col-md-4"><div class="p-4 bg-light rounded"><h5>${bookingCount}</h5><p>Total bookings</p></div></div>
                    <div class="col-md-4"><div class="p-4 bg-light rounded"><h5>${userCount}</h5><p>Total users</p></div></div>
                    <div class="col-md-4"><div class="p-4 bg-light rounded"><h5>LKR ${paymentTotal.toLocaleString('en-US')}</h5><p>Total revenue</p></div></div>
                </div>
                <div class="alert alert-info">
                    <strong>Report Summary:</strong> This dashboard shows key metrics. Generate detailed reports using the button above.
                </div>
            `;
            document.getElementById('downloadReport')?.addEventListener('click', () => {
                const reportData = {
                    generatedAt: new Date().toISOString(),
                    totalBookings: bookingCount,
                    totalUsers: userCount,
                    totalRevenue: paymentTotal,
                    bookings,
                    users: users.map(({ password, ...u }) => u)
                };
                const blob = new Blob([JSON.stringify(reportData, null, 2)], { type: 'application/json' });
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `globetrek-report-${Date.now()}.json`;
                a.click();
            });
            break;
        default:
            content.innerHTML = '<h4>Welcome</h4><p>Select a section from the menu to start.</p>';
    }
}

function isDashboardPage() {
    return document.getElementById('dashboardMenu') && document.getElementById('dashboardContent');
}

function initLogoutButtons() {
    const logoutBtn = document.getElementById('logoutBtn');
    if (!logoutBtn) return;
    logoutBtn.type = 'button';
    logoutBtn.addEventListener('click', () => {
        clearCurrentUser();
        window.location.href = 'login.html';
    });
}

function initDashboardPage() {
    if (!isDashboardPage()) return;

    const user = getCurrentUser();
    if (!user) {
        window.location.href = 'login.html';
        return;
    }

    const title = document.getElementById('dashboardTitle');
    const roleLabel = document.getElementById('dashboardRole');

    if (title) title.innerText = user.role === 'admin' ? 'Admin Dashboard' : user.role === 'staff' ? 'Staff Dashboard' : 'Dashboard';
    if (roleLabel) roleLabel.innerText = `Hello ${user.name}, role: ${user.role}`;

    const itemsByRole = {
        user: [
            { id: 'companyProfile', label: 'Company Profile' },
            { id: 'tourPackages', label: 'Tour Packages' },
            { id: 'profile', label: 'My Profile' },
            { id: 'bookings', label: 'My Bookings' },
            { id: 'customize', label: 'Customize Travel Plan' },
            { id: 'queries', label: 'Queries' },
            { id: 'payments', label: 'My Payments' }
        ],
        staff: [
            { id: 'companyProfile', label: 'Company Profile' },
            { id: 'tourPackages', label: 'Tour Packages' },
            { id: 'managePackages', label: 'Manage Packages' },
            { id: 'confirmBookings', label: 'Confirm Bookings' },
            { id: 'hotels', label: 'Hotels Coordination' },
            { id: 'transport', label: 'Transport Providers' },
            { id: 'customerQueries', label: 'Customer Queries' }
        ],
        admin: [
            { id: 'companyProfile', label: 'Company Profile' },
            { id: 'tourPackages', label: 'Tour Packages' },
            { id: 'manageUsers', label: 'Manage Users' },
            { id: 'confirmStaff', label: 'Confirm Staff' },
            { id: 'overseeBooking', label: 'Oversee Booking' },
            { id: 'reports', label: 'Generate Reports' }
        ]
    };

    const normalizedRole = normalizeRole(user.role);
    if (normalizedRole !== user.role) {
        user.role = normalizedRole;
        setCurrentUser(user);
    }

    // Build the menu and pass the items based on the user's role
    const roleItems = itemsByRole[normalizedRole] || itemsByRole.user;
    buildDashboardMenu(roleItems);
}

function initDetailsButton() {
    const bookButton = document.getElementById('bookNowBtn');
    if (!bookButton) return;
    const params = new URLSearchParams(window.location.search);
    const packageId = params.get('id');
    bookButton.addEventListener('click', () => {
        window.location.href = `booking.html?id=${packageId}`;
    });
}

function initPackagesPage() {
    const container = document.getElementById('package-container');
    if (!container) return;
    refreshPackageData();
    container.innerHTML = '';
    (window.packageData || []).forEach(pkg => {
        container.innerHTML += `
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
            </div>`;
    });
    document.querySelectorAll('.favorite-icon').forEach(heart => {
        heart.addEventListener('click', function () {
            this.classList.toggle('fa-solid');
            this.classList.toggle('fa-regular');
            this.style.color = this.classList.contains('fa-solid') ? '#e03a3c' : '#000';
        });
    });
}

function initDetailsPage() {
    const params = new URLSearchParams(window.location.search);
    const id = Number(params.get('id'));
    const pkg = findPackage(id);
    if (!pkg) return;

    document.getElementById('detailTitle').innerText = pkg.name;
    document.getElementById('detailDesc').innerText = pkg.desc;
    document.getElementById('detailDuration').innerText = pkg.duration;
    document.getElementById('detailTransport').innerText = pkg.transport;
    document.getElementById('detailRating').innerText = pkg.rating;
    document.getElementById('detailPrice').innerText = pkg.price;
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
    document.getElementById('packageHighlights').innerHTML = pkg.highlights.map(item => `<li class="list-group-item"><i class="fa-solid fa-star"></i>${item}</li>`).join('');
    document.getElementById('itineraryList').innerHTML = pkg.itinerary.map(step => `
        <div class="itinerary-step mb-4">
            <h6>${step.day}</h6>
            <p>${step.activity}</p>
        </div>
    `).join('');
    document.getElementById('includedList').innerHTML = pkg.included.map(item => `<li><i class="fa-solid fa-check"></i>${item}</li>`).join('');
    initDetailsButton();
}

function initPage() {
    ensureDefaultData();
    normalizeStoredUsers();
    refreshPackageData();
    initLogoutButtons();
    initRegisterForm();
    initLoginForm();
    initBookingPage();
    initPaymentPage();
    initContactPage();
    initDashboardPage();
    initPackagesPage();
    initDetailsPage();
}

window.addEventListener('DOMContentLoaded', initPage);

document.addEventListener("DOMContentLoaded", function () {

    const user = getCurrentUser();
    if (!user) return;

    const role = normalizeRole(user.role);

    // Set title
    document.getElementById("dashboardTitle").innerText = role.toUpperCase() + " Dashboard";
    document.getElementById("dashboardRole").innerText = "Welcome, " + user.name;

    let menuItems = [];

    // ===== ROLE BASED MENU =====
    if (role === "admin") {
        menuItems = [
            { id: "manageUsers", label: "Manage Users" },
            { id: "managePackages", label: "Manage Packages" },
            { id: "reports", label: "View Reports" }
        ];
    } 
    else if (role === "staff") {
        menuItems = [
            { id: "bookings", label: "Manage Bookings" },
            { id: "customers", label: "View Customers" }
        ];
    } 
    else {
        menuItems = [
            { id: "myBookings", label: "My Bookings" },
            { id: "profile", label: "My Profile" }
        ];
    }

    buildDashboardMenu(menuItems);

});

document.addEventListener("DOMContentLoaded", function () {

    const user = getCurrentUser();
    if (!user) return;

    const role = normalizeRole(user.role);

    // Set dashboard text
    document.getElementById("dashboardTitle").innerText = role.toUpperCase() + " Dashboard";
    document.getElementById("dashboardRole").innerText = "Welcome, " + user.name;

    let menuItems = [];

    // ===== ROLE BASED MENU =====
    if (role === "admin") {
        menuItems = [
            { id: "manageUsers", label: "Manage Users" },
            { id: "managePackages", label: "Manage Packages" },
            { id: "reports", label: "Reports" }
        ];
    } 
    else if (role === "staff") {
        menuItems = [
            { id: "bookings", label: "Bookings" },
            { id: "customers", label: "Customers" }
        ];
    } 
    else {
        menuItems = [
            { id: "myBookings", label: "My Bookings" },
            { id: "profile", label: "Profile" }
        ];
    }

    // 🔥 THIS LINE IS THE MOST IMPORTANT
    buildDashboardMenu(menuItems);

}); 