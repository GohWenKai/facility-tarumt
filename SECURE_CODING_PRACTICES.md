# 🛡️ Secure Coding Practices

## Overview
This document demonstrates how the TARUMT Facility Booking System implements secure coding practices to mitigate the threats identified in `SECURITY_THREATS.md`. Each practice includes code examples and links to relevant security threats.

---

## Table of Contents
1. [Input Validation & Sanitization](#1-input-validation--sanitization)
2. [SQL Injection Prevention](#2-sql-injection-prevention)
3. [XSS Protection](#3-xss-protection)
4. [CSRF Protection](#4-csrf-protection)
5. [Authentication & Authorization](#5-authentication--authorization)
6. [Session Security](#6-session-security)
7. [Security Headers](#7-security-headers)
8. [Audit Logging](#8-audit-logging)

---

## 1. Input Validation & Sanitization

**Threat Mitigated**: Mass Assignment, SQL Injection, XSS  
**SECURITY_THREATS.md Reference**: Asset Management - Threat 1, Audit Log - Threat 1

### Implementation

#### Form Request Validation
```php
// app/Http/Requests/StoreAssetRequest.php
class StoreAssetRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->role === 'admin';
    }

    public function rules()
    {
        return [
            'facility_id' => ['required', 'exists:facilities,id'],
            'name' => ['required', 'string', 'max:255', 'regex:/^[\w\s\-]+$/'],
            'type' => ['required', 'in:Equipment,Furniture,Electronics,Other'],
            'serial_number' => ['required', 'string', 'max:50', 'unique:assets'],
            'condition' => ['required', 'in:Good,Fair,Poor,Damaged'],
            'maintenance_note' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages()
    {
        return [
            'name.regex' => 'Asset name can only contain letters, numbers, spaces, and hyphens.',
            'serial_number.unique' => 'This serial number is already in use.',
        ];
    }
}
```

#### Controller Usage
```php
// app/Http/Controllers/Admin/AssetController.php
public function store(StoreAssetRequest $request)
{
    // Validation automatically happens before this point
    // Only validated data passes through
    
    $asset = Asset::create($request->validated());
    
    return redirect()->route('admin.assets.manage')
        ->with('success', 'Asset created successfully');
}
```

**Security Benefits**:
- ✅ Whitelist validation prevents unexpected fields
- ✅ Type validation ensures data integrity  
- ✅ Regex patterns prevent malicious input
- ✅ Length limits prevent buffer overflow attacks

---

## 2. SQL Injection Prevention

**Threat Mitigated**: SQL Injection  
**SECURITY_THREATS.md Reference**: Audit Log Module - Threat 1

### Implementation

#### Eloquent ORM (Recommended)
```php
// ❌ NEVER DO THIS - Vulnerable to SQL Injection
$action = $_GET['action'];
$logs = DB::select("SELECT * FROM audit_logs WHERE action = '$action'");

// ✅ SAFE - Eloquent with parameter binding
$logs = AuditLog::where('action', request('action'))->get();

// ✅ SAFE - Query Builder with parameter binding
$logs = DB::table('audit_logs')
    ->where('action', request('action'))
    ->where('created_at', '>=', request('date_from'))
    ->get();
```

#### Complex Queries with Named Bindings
```php
// ✅ SAFE - Named parameter binding
$logs = DB::select('
    SELECT * FROM audit_logs 
    WHERE action = :action 
    AND model_type = :model 
    AND created_at BETWEEN :start AND :end
', [
    'action' => request('action'),
    'model' => request('model_type'),
    'start' => request('date_from'),
    'end' => request('date_to'),
]);
```

**Security Benefits**:
- ✅ Automatic parameter escaping
- ✅ Type-safe queries
- ✅ No string concatenation of user input
- ✅ PDO prepared statements under the hood

---

## 3. XSS Protection

**Threat Mitigated**: Cross-Site Scripting  
**SECURITY_THREATS.md Reference**: Asset Management - Threat 2

### Implementation

#### Blade Template Escaping
```blade
{{-- ✅ SAFE - Automatic HTML entity encoding --}}
<td>{{ $asset->name }}</td>
<!-- Output: &lt;script&gt;alert('XSS')&lt;/script&gt; -->

{{-- ⚠️ DANGEROUS - Only use for trusted admin-generated HTML --}}
<td>{!! $asset->description !!}</td>

{{-- ✅ SAFE - JavaScript context escaping --}}
<script>
    const assetName = @json($asset->name);
    console.log(assetName); // Properly escaped
</script>

{{-- ✅ SAFE - Attribute escaping --}}
<input type="text" name="search" value="{{ old('search') }}">
```

#### Content Security Policy
```php
// app/Http/Middleware/SecurityHeaders.php
public function handle($request, Closure $next)
{
    $response = $next($request);
    
    $response->headers->set('X-XSS-Protection', '1; mode=block');
    $response->headers->set('X-Content-Type-Options', 'nosniff');
    
    $csp = "default-src 'self'; " .
           "script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; " .
           "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net;";
    
    $response->headers->set('Content-Security-Policy', $csp);
    
    return $response;
}
```

**Security Benefits**:
- ✅ Automatic output encoding by default
- ✅ Context-aware escaping (HTML, JS, attributes)
- ✅ CSP prevents external script execution
- ✅ XSS Auditor enabled in browsers

---

## 4. CSRF Protection

**Threat Mitigated**: Cross-Site Request Forgery  
**SECURITY_THREATS.md Reference**: Booking System - Threat 1

### Implementation

#### Form Protection
```blade
{{-- ✅ REQUIRED for all POST/PUT/DELETE forms --}}
<form method="POST" action="{{ route('bookings.store') }}">
    @csrf
    
    <input type="hidden" name="facility_id" value="{{ $facility->id }}">
    <input type="date" name="booking_date" required>
    
    <button type="submit">Book Facility</button>
</form>
```

#### AJAX Requests
```javascript
// ✅ SAFE - Include CSRF token in AJAX headers
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

// Modern Fetch API
fetch('/api/bookings', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify(data)
});
```

#### Meta Tag in Layout
```blade
{{-- resources/views/layouts/admin.blade.php --}}
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
```

**Security Benefits**:
- ✅ Unique token per session
- ✅ Automatic validation by middleware
- ✅ Token rotation on each request
- ✅ Protection against replay attacks

---

## 5. Authentication & Authorization

**Threat Mitigated**: Unauthorized Access, Privilege Escalation  
**SECURITY_THREATS.md Reference**: Audit Log - Threat 2, Admin Panel - Threat 1

### Implementation

#### Route Middleware Protection
```php
// routes/web.php

// ✅ Protected admin routes
Route::middleware(['auth', 'admin', 'admin.ip.whitelist'])->prefix('admin')->group(function () {
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('admin.audit_logs.index');
    Route::resource('assets', AssetController::class);
    Route::resource('facilities', FacilityController::class');
});

// ✅ Protected user routes
Route::middleware(['auth'])->group(function () {
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::post('/bookings', [BookingController::class, 'store']);
});
```

#### Policy-Based Authorization
```php
// app/Policies/AssetPolicy.php
class AssetPolicy
{
    public function viewAny(User $user)
    {
        return $user->role === 'admin';
    }

    public function update(User $user, Asset $asset)
    {
        return $user->role === 'admin' && 
               $asset->facility->building->is_active;
    }

    public function delete(User $user, Asset $asset)
    {
        // Only admins can delete, and asset must not be assigned
        return $user->role === 'admin' && 
               !$asset->assignments()->exists();
    }
}

// Controller usage
public function update(Request $request, Asset $asset)
{
    $this->authorize('update', $asset);
    
    // Proceed with update logic
}
```

#### IP Whitelist Middleware
```php
// app/Http/Middleware/AdminIpWhitelist.php
public function handle($request, Closure $next)
{
    $allowedIPs = config('security.admin_ips', ['127.0.0.1']);
    $currentIP = $request->ip();
    
    if (!in_array($currentIP, $allowedIPs)) {
        // Log unauthorized access attempt
        Log::channel('security')->warning('Unauthorized admin access attempt', [
            'ip' => $currentIP,
            'url' => $request->fullUrl(),
            'user_agent' => $request->userAgent(),
        ]);
        
        abort(403, 'Access denied from this IP address.');
    }
    
    return $next($request);
}
```

**Security Benefits**:
- ✅ Defense in depth (multiple layers)
- ✅ Role-based access control (RBAC)
- ✅ Granular permission checking
- ✅ IP-based restrictions for sensitive areas

---

## 6. Session Security

**Threat Mitigated**: Session Hijacking  
**SECURITY_THREATS.md Reference**: Booking System - Threat 2

### Implementation

#### Secure Session Configuration
```php
// config/session.php
return [
    'driver' => 'database',  // More secure than file-based
    'lifetime' => 120,       // Auto logout after 2 hours
    'expire_on_close' => true,
    
    'encrypt' => true,       // Encrypt session data
    
    'cookie' => env('SESSION_COOKIE', 'tarumt_session'),
    'secure' => env('SESSION_SECURE_COOKIE', true),  // HTTPS only
    'http_only' => true,     // Prevent JavaScript access
    'same_site' => 'strict', // CSRF protection
];
```

#### Session Regeneration
```php
// app/Http/Controllers/Auth/LoginController.php
protected function authenticated(Request $request, $user)
{
    // ✅ Regenerate session ID after successful login
    $request->session()->regenerate();
    
    // Log successful login
    Log::channel('security')->info('User logged in', [
        'user_id' => $user->id,
        'ip' => $request->ip(),
        'user_agent' => $request->userAgent(),
    ]);
}

// Regenerate on privilege changes
public function promoteToAdmin(User $user)
{
    $user->update(['role' => 'admin']);
    
    session()->regenerate();  // New session for new privileges
}
```

**Security Benefits**:
- ✅ Session fixation prevention
- ✅ Encrypted session data
- ✅ Automatic timeout
- ✅ HttpOnly cookies prevent XSS theft

---

## 7. Security Headers

**Threat Mitigated**: Various injection attacks, clickjacking  
**SECURITY_THREATS.md Reference**: Multiple modules

### Implementation

```php
// app/Http/Middleware/SecurityHeaders.php
public function handle($request, Closure $next)
{
    $response = $next($request);
    
    // Prevent clickjacking
    $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
    
    // Prevent MIME-type sniffing
    $response->headers->set('X-Content-Type-Options', 'nosniff');
    
    // Enable XSS filter
    $response->headers->set('X-XSS-Protection', '1; mode=block');
    
    // HTTPS enforcement (HSTS)
    $response->headers->set('Strict-Transport-Security', 
        'max-age=31536000; includeSubDomains');
    
    // Content Security Policy
    $csp = "default-src 'self'; " .
           "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net; " .
           "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; " .
           "font-src 'self' https://cdn.jsdelivr.net; " .
           "img-src 'self' data: https:; " .
           "frame-src https://www.google.com;";  // For reCAPTCHA
    
    $response->headers->set('Content-Security-Policy', $csp);
    
    return $response;
}
```

**Security Benefits**:
- ✅ Prevents embedding in iframes
- ✅ Forces HTTPS connections
- ✅ Blocks MIME-based attacks
- ✅ Restricts resource loading sources

---

## 8. Audit Logging

**Threat Mitigated**: Accountability, forensics, threat detection  
**SECURITY_THREATS.md Reference**: All modules (monitoring)

### Implementation

#### Model Observer Pattern
```php
// app/Observers/AssetObserver.php
class AssetObserver
{
    public function created(Asset $asset)
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'created',
            'model_type' => Asset::class,
            'model_id' => $asset->id,
            'new_values' => $asset->toArray(),
            'ip_address' => request()->ip(),
        ]);
    }

    public function updated(Asset $asset)
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'updated',
            'model_type' => Asset::class,
            'model_id' => $asset->id,
            'old_values' => $asset->getOriginal(),
            'new_values' => $asset->getDirty(),
            'ip_address' => request()->ip(),
        ]);
    }

    public function deleted(Asset $asset)
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'deleted',
            'model_type' => Asset::class,
            'model_id' => $asset->id,
            'old_values' => $asset->toArray(),
            'ip_address' => request()->ip(),
        ]);
    }
}
```

#### Registration
```php
// app/Providers/AppServiceProvider.php
public function boot()
{
    Asset::observe(AssetObserver::class);
    Booking::observe(BookingObserver::class);
    User::observe(UserObserver::class);
}
```

**Security Benefits**:
- ✅ Complete audit trail of all changes
- ✅ Forensic analysis capability
- ✅ Threat pattern detection
- ✅ Compliance with security standards

---

## 🎯 Threat-to-Practice Mapping

| Secure Practice | Threats Mitigated | Implementation |
|----------------|-------------------|----------------|
| **Eloquent ORM** | SQL Injection | `AuditLog::where('action', $action)` |
| **Blade Escaping** | XSS Attacks | `{{ $variable }}` auto-escapes |
| **@csrf Token** | CSRF Attacks | `<form>@csrf</form>` |
| **Middleware Auth** | Unauthorized Access | `Route::middleware(['auth', 'admin'])` |
| **Policy Gates** | Privilege Escalation | `$this->authorize('update', $asset)` |
| **Secure Sessions** | Session Hijacking | `secure => true, http_only => true` |
| **IP Whitelist** | IP Spoofing | `AdminIpWhitelist` middleware |
| **Mass Assignment Protection** | Data Manipulation | `protected $fillable = [...]` |
| **Audit Logging** | Accountability | `AssetObserver` auto-logs changes |

---

## 📚 References

- **Laravel Security Documentation**: https://laravel.com/docs/10.x/security
- **OWASP Secure Coding Practices**: https://owasp.org/www-project-secure-coding-practices-quick-reference-guide/
- **OWASP Top 10**: https://owasp.org/Top10/
- **Laravel Best Practices**: https://github.com/alexeymezenin/laravel-best-practices

---

## 🔒 Security Checklist

Before deploying to production, ensure:

- [ ] All user input is validated using Form Requests
- [ ] Database queries use Eloquent or parameter binding
- [ ] All forms include `@csrf` token
- [ ] Sensitive routes protected by authentication middleware
- [ ] Admin routes use IP whitelist
- [ ] Sessions configured with secure cookies
- [ ] Security headers middleware enabled
- [ ] Audit logging enabled for critical models
- [ ] `.env` file not committed to version control
- [ ] Environment set to `APP_ENV=production`
- [ ] Debug mode disabled (`APP_DEBUG=false`)
- [ ] HTTPS enforced in production

---

**Maintained by**: TARUMT Security Team  
**Last Updated**: December 19, 2025  
**Next Security Audit**: March 2026
