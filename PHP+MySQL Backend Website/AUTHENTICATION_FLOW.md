# GlobeTrek Authentication Flow - 100% Database-Driven

## System Architecture

### 1. DATABASE SCHEMA
```sql
CREATE TABLE users (
  user_id INT PRIMARY KEY AUTO_INCREMENT,
  full_name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  password VARCHAR(255) NOT NULL,        -- bcrypt hashed
  role ENUM('customer', 'admin', 'staff'),
  status TINYINT(1) DEFAULT 1,           -- 1 = active, 0 = awaiting approval
  created_at TIMESTAMP DEFAULT NOW()
)
```

---

## REGISTRATION FLOW

### File: `register.php` + `functions.php::createUser()`

**Step 1: User Submits Registration Form**
```html
<form id="registerForm" method="post">
  <input name="registerName" type="text" required>
  <input name="registerEmail" type="email" required>
  <input name="registerPassword" type="password" required>
  <input name="registerConfirmPassword" type="password" required>
  <select name="registerRole" required>
    <option value="user">User</option>
    <option value="staff">Staff</option>
    <option value="admin">Admin</option>
  </select>
</form>
```

**Step 2: Form POSTs to register.php**
- NO JavaScript interception (event.preventDefault removed)
- Server receives POST data

**Step 3: register.php Validates Input**
```php
if (!$name || !$email || !$password || !$confirm || !$role) {
    $errorMessage = 'Please complete all fields.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errorMessage = 'Please enter a valid email address.';
} elseif (strlen($password) < 6) {
    $errorMessage = 'Password must be at least 6 characters.';
} elseif ($password !== $confirm) {
    $errorMessage = 'Passwords do not match.';
} elseif (fetchUserByEmail($email)) {
    $errorMessage = 'An account with this email already exists.';
} else {
    // INSERT INTO DATABASE
    createUser($name, $email, $password, $role);
}
```

**Step 4: createUser() Inserts into Database**
```php
function createUser($fullName, $email, $password, $role) {
    $role = normalizeRole($role);  // 'user' → 'customer', 'staff' → 'staff', 'admin' → 'admin'
    $status = $role === 'customer' ? 1 : 0;  // customers auto-approved, staff/admin need approval
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);  // bcrypt
    
    query(
        'INSERT INTO users (full_name, email, password, role, status) VALUES (?, ?, ?, ?, ?)',
        [$fullName, $email, $hashedPassword, $role, $status]
    );
    
    return db()->insert_id;
}
```

**Step 5: Response to User**
- ✅ If role='user': "Registration complete. Please log in."
- ✅ If role='staff'/'admin': "Registration submitted. Admin approval is required."
- ❌ If error: Display error message ONLY
- ❌ NO HTML redirects

---

## LOGIN FLOW

### File: `login.php` + `functions.php::loginUser()`

**Step 1: User Submits Login Form**
```html
<form id="loginForm" method="post">
  <input name="loginEmail" type="email" required>
  <input name="loginPassword" type="password" required>
</form>
```

**Step 2: Form POSTs to login.php**
- NO JavaScript interception (event.preventDefault removed)
- Server receives POST data

**Step 3: login.php Validates Input**
```php
if (!$email || !$password) {
    $errorMessage = 'Please enter both email and password.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errorMessage = 'Please enter a valid email address.';
} else {
    // QUERY DATABASE
    $user = loginUser($email, $password);
    
    if (!$user) {
        $errorMessage = 'Invalid email or password.';
    } elseif ($user['role'] !== 'customer' && intval($user['status']) === 0) {
        $errorMessage = 'Your account is awaiting admin approval.';
    } else {
        // SUCCESS - CREATE SESSION
        setAuthUser($user);
        redirect('/dashboard.php');
    }
}
```

**Step 4: loginUser() Queries Database**
```php
function loginUser($email, $password) {
    // Query user by email
    $user = fetchUserByEmail($email);  // SELECT * FROM users WHERE email = ?
    
    if (!$user) {
        return null;  // User not found
    }
    
    // Verify password with bcrypt
    if (!password_verify($password, $user['password'])) {
        return null;  // Password incorrect
    }
    
    return $user;  // Login successful
}
```

**Step 5: setAuthUser() Creates PHP Session**
```php
function setAuthUser(array $user) {
    $user['role'] = normalizeRole($user['role']);
    $_SESSION['user'] = $user;  // Session created
}
```

**Step 6: Redirect to Dashboard**
```php
redirect('/dashboard.php');  // header('Location: /dashboard.php')
```

---

## SESSION PROTECTION

### All Protected Pages Use: `requireLogin()`

**Files Protected:**
- ✅ dashboard.php
- ✅ booking.php
- ✅ payment.php

**Protection Logic:**
```php
function requireLogin() {
    if (!isLoggedIn()) {
        $redirectUrl = $_SERVER['REQUEST_URI'];
        redirect('login.php?redirect=' . urlencode($redirectUrl));
    }
}

function isLoggedIn() {
    return !empty($_SESSION['user']);
}

function currentUser() {
    return $_SESSION['user'] ?? null;
}
```

---

## ROLE-BASED ROUTING

### dashboard.php Handles All Roles

**Roles:**
- `customer` (registered as 'user') → User Menu
- `staff` → Staff Menu
- `admin` → Admin Menu

**Dashboard Logic:**
```php
$user = currentUser();
$role = userRole();  // Converts 'customer' → 'user' for display

$menuByRole = [
    'user' => [...],    // User bookings, queries, payments
    'staff' => [...],   // Package management, booking confirmation
    'admin' => [...],   // User management, approvals, reports
];

if (!array_key_exists($section, $menuByRole[$role])) {
    $section = array_key_first($menuByRole[$role]);
}
```

---

## NAVIGATION LINKS

**All .php files (NO .html anywhere):**
- ✅ index.php - Home
- ✅ packages.php - Browse packages
- ✅ details.php - Package details
- ✅ booking.php - Create booking
- ✅ payment.php - Process payment
- ✅ dashboard.php - User/staff/admin dashboard
- ✅ contact.php - Contact form
- ✅ login.php - Authentication
- ✅ register.php - Registration
- ✅ logout.php - Logout

---

## SESSION LIFECYCLE

```
User Registration (register.php)
    ↓
INSERT INTO users (MySQL)
    ↓
Show success message
    ↓
User navigates to login.php
    ↓
User Login (login.php)
    ↓
SELECT FROM users WHERE email (MySQL)
    ↓
password_verify() check
    ↓
Success → setAuthUser() creates $_SESSION['user']
    ↓
redirect('/dashboard.php')
    ↓
dashboard.php requireLogin() validates session exists
    ↓
Display role-based dashboard
    ↓
User clicks Logout (logout.php)
    ↓
logoutUser() → session_unset() + session_destroy()
    ↓
redirect('login.php')
```

---

## DATA FLOW VERIFICATION

### ✅ Registration
- Form captures: name, email, password, role
- Validates input
- Hashes password with bcrypt
- Normalizes role: 'user'→'customer', 'staff'→'staff', 'admin'→'admin'
- Sets status: customer=1 (approved), staff/admin=0 (pending)
- Inserts into database
- Response only (no redirect)

### ✅ Login
- Form captures: email, password
- Validates format
- Queries database for user
- Verifies password hash
- Checks approval status
- Creates PHP session
- Redirects to dashboard.php

### ✅ Protected Pages
- requireLogin() checks session exists
- If no session → redirects to login.php
- If session exists → displays role-based content

### ✅ Navigation
- All links use .php files
- No HTML redirects
- No static file fallbacks
- Database-driven routing

---

## SECURITY MEASURES

1. **Password Hashing**: bcrypt via `password_hash(PASSWORD_DEFAULT)`
2. **Password Verification**: `password_verify()` with timing-safe comparison
3. **Email Validation**: `filter_var(..., FILTER_VALIDATE_EMAIL)`
4. **SQL Injection Prevention**: Prepared statements with parameter binding
5. **Session Protection**: `requireLogin()` on protected pages
6. **XSS Prevention**: `h()` function for HTML escaping output
7. **HTML Redirect Blocking**: All `.html` URLs blocked in login.php
8. **Role Normalization**: Prevents role manipulation
9. **Approval Status**: Staff/admin require admin approval

---

## TESTING CHECKLIST

- [ ] Register as 'user' → INSERT successful, status=1, redirect NOT used
- [ ] Register as 'staff' → INSERT successful, status=0, approval message shown
- [ ] Login with user account → Session created, redirects to dashboard.php
- [ ] Login with unapproved staff → Error: "awaiting admin approval"
- [ ] Visit protected page without login → Redirects to login.php
- [ ] Visit dashboard after login → Shows role-based menu
- [ ] Click logout → Session destroyed, redirects to login.php
- [ ] All navigation links work → No 404 errors, no HTML pages

---

## SYSTEM STATUS

✅ 100% Database-Driven
✅ PHP + MySQL Only
✅ No HTML Files Used
✅ No JavaScript Form Interception
✅ Session Protection Active
✅ Role-Based Access Control
✅ UI Design Unchanged
