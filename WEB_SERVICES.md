# Web Services Implementation Report

## Overview
This document outlines the web services architecture implemented in the TARUMT Facility Booking System. The system uses a hybrid approach combining **SOAP/XML** for ticket generation and **RESTful API** for client communications.

---

## 🔗 Architecture Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                     CLIENT LAYER                                 │
├─────────────────────────────────────────────────────────────────┤
│   Web Browser      │    Mobile App      │    External Systems   │
│   (Blade Views)    │    (REST API)      │    (SOAP/XML)         │
└────────┬───────────┴────────┬───────────┴────────┬──────────────┘
         │                    │                    │
         ▼                    ▼                    ▼
┌─────────────────────────────────────────────────────────────────┐
│                     API GATEWAY LAYER                            │
├─────────────────────────────────────────────────────────────────┤
│   Rate Limiting   │   Authentication   │   CORS Handling        │
│   (100 req/min)   │   (Sanctum)        │   (PHP-CORS)           │
└────────┬───────────┴────────┬───────────┴────────┬──────────────┘
         │                    │                    │
         ▼                    ▼                    ▼
┌─────────────────────────────────────────────────────────────────┐
│                     SERVICE LAYER                                │
├─────────────────────────────────────────────────────────────────┤
│   BookingService  │   AuthService    │   AdminDashboardService  │
└────────┬───────────┴────────┬───────────┴────────┬──────────────┘
         │                    │                    │
         ▼                    ▼                    ▼
┌─────────────────────────────────────────────────────────────────┐
│                     DATA LAYER                                   │
├─────────────────────────────────────────────────────────────────┤
│   MySQL Database  │   XML File Storage │   Redis Cache          │
└─────────────────────────────────────────────────────────────────┘
```

---

## 1. SOAP/XML Web Service

### Purpose
Generate and manage booking tickets in SOAP/XML format for interoperability with external systems and PDF generation.

### Implementation

**File:** `app/Patterns/State/PendingState.php`

```php
// SOAP Envelope Generation
$ns = 'http://schemas.xmlsoap.org/soap/envelope/';
$soapEnvelope = new SimpleXMLElement('<soap:Envelope xmlns:soap="'.$ns.'"></soap:Envelope>');
$soapBody = $soapEnvelope->addChild('Body', null, $ns);
$ticket = $soapBody->addChild('ticket');

$ticket->addChild('id', $booking->id);
$ticket->addChild('student_name', $booking->user->name);
$ticket->addChild('facility', $booking->facility->name);
$ticket->addChild('start_time', Carbon::parse($booking->start_time)->format('Y-m-d H:i'));
$ticket->addChild('end_time', Carbon::parse($booking->end_time)->format('Y-m-d H:i'));
$ticket->addChild('generated_at', now()->toDateTimeString());

// Save XML file
Storage::put("xml/{$booking->id}.xml", $soapEnvelope->asXML());
```

### XML Output Structure
```xml
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
    <soap:Body>
        <ticket>
            <id>uuid-string</id>
            <student_name>John Doe</student_name>
            <role>student</role>
            <facility>H204 - Lecture Hall</facility>
            <start_time>2025-12-24 14:00</start_time>
            <end_time>2025-12-24 16:00</end_time>
            <generated_at>2025-12-20 10:30:00</generated_at>
        </ticket>
    </soap:Body>
</soap:Envelope>
```

### XML Parsing (Adapter Pattern)

**File:** `app/Adapters/XmlTicketAdapter.php`

```php
class XmlTicketAdapter
{
    public function parseTicket($bookingId): array
    {
        $path = "xml/{$bookingId}.xml";
        
        if (!Storage::exists($path)) {
            return [];
        }

        $xml = Storage::get($path);
        $envelope = simplexml_load_string($xml);
        
        // Register SOAP namespace
        $envelope->registerXPathNamespace('soap', 'http://schemas.xmlsoap.org/soap/envelope/');
        
        return [
            'id' => (string) $envelope->Body->ticket->id,
            'name' => (string) $envelope->Body->ticket->student_name,
            'role' => (string) $envelope->Body->ticket->role,
            'facility' => (string) $envelope->Body->ticket->facility,
            'time' => (string) $envelope->Body->ticket->start_time . ' to ' . 
                      (string) $envelope->Body->ticket->end_time,
            'generated_at' => (string) $envelope->Body->ticket->generated_at,
        ];
    }
}
```

---

## 2. RESTful API Endpoints

### Authentication Endpoints

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| POST | `/login` | User login with credentials | No |
| POST | `/verify-otp` | Verify 2FA OTP code | No |
| POST | `/resend-otp` | Resend OTP code | No |
| POST | `/logout` | Logout user | Yes |

### Booking API Endpoints

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/api/bookings` | List user's bookings | Yes |
| POST | `/api/bookings` | Create new booking | Yes |
| GET | `/api/bookings/{id}` | Get booking details | Yes |
| POST | `/api/bookings/{id}/cancel` | Cancel a booking | Yes |
| GET | `/api/bookings/{id}/ticket` | Download PDF ticket | Yes |

### Facility API Endpoints

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/facilities` | List all facilities | Yes |
| GET | `/facilities/{id}` | Get facility details | Yes |
| GET | `/facilities/{id}/schedule` | Get availability | Yes |

### Response Format (JSON)

```json
{
    "status": "success",
    "message": "Booking created successfully",
    "data": {
        "booking": {
            "id": "uuid-string",
            "facility_id": 1,
            "start_time": "2025-12-24 14:00:00",
            "end_time": "2025-12-24 16:00:00",
            "status": "pending",
            "total_cost": 2
        }
    }
}
```

---

## 3. API Optimization Techniques

### 3.1 Rate Limiting (Throttling)

**Purpose:** Prevent API abuse and ensure fair resource usage.

**Implementation:** `routes/web.php`

```php
// Login rate limiting - 5 attempts per minute
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1');

// Search rate limiting - 100 requests per minute
Route::get('/bookings/search', [SearchController::class, 'search'])
    ->middleware('throttle:100,1');
```

### 3.2 Response Caching

**Purpose:** Improve performance by caching frequently accessed data.

**Implementation:**

```php
// Cache facility list for 60 minutes
public function index()
{
    $facilities = Cache::remember('facilities.all', 3600, function () {
        return Facility::with('building')->get();
    });
    
    return response()->json($facilities);
}
```

### 3.3 Eager Loading (N+1 Prevention)

**Purpose:** Reduce database queries by preloading relationships.

**Implementation:**

```php
// WITHOUT eager loading: N+1 queries
$bookings = Booking::all();
foreach ($bookings as $booking) {
    echo $booking->user->name; // Extra query each time!
}

// WITH eager loading: Only 2 queries
$bookings = Booking::with(['user', 'facility'])->get();
foreach ($bookings as $booking) {
    echo $booking->user->name; // No extra query!
}
```

### 3.4 Pagination

**Purpose:** Handle large datasets efficiently.

**Implementation:**

```php
public function bookings()
{
    $bookings = Booking::with(['user', 'facility'])
        ->orderBy('created_at', 'desc')
        ->paginate(10); // 10 items per page
    
    return view('admin.bookings.index', compact('bookings'));
}
```

### 3.5 JSON Response Optimization

**Purpose:** Minimize payload size by selecting only needed fields.

**Implementation:**

```php
// Only select needed columns
$users = User::select('id', 'name', 'email')->get();

// Use API Resources for consistent formatting
return new BookingResource($booking);
```

---

## 4. Security in Web Services

### 4.1 API Authentication (Sanctum)

```php
// Protected API routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/api/user', function (Request $request) {
        return $request->user();
    });
});
```

### 4.2 CORS Configuration

**File:** `config/cors.php`

```php
return [
    'paths' => ['api/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['https://yourfrontend.com'],
    'allowed_headers' => ['*'],
    'supports_credentials' => true,
];
```

### 4.3 Request Validation

```php
public function store(Request $request)
{
    $validated = $request->validate([
        'facility_id' => 'required|exists:facilities,id',
        'booking_date' => 'required|date|after:today',
        'start_time' => 'required|date_format:H:i',
        'end_time' => 'required|date_format:H:i|after:start_time',
    ]);
    
    // Process validated data
}
```

---

## 5. Module Integration Flow

```
┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│   Frontend   │    │  Controller  │    │   Service    │
│  (Blade/JS)  │───▶│  (API Layer) │───▶│   Layer      │
└──────────────┘    └──────────────┘    └──────────────┘
                           │                    │
                           ▼                    ▼
                    ┌──────────────┐    ┌──────────────┐
                    │   Response   │◀───│   Database   │
                    │  (JSON/XML)  │    │   (MySQL)    │
                    └──────────────┘    └──────────────┘
```

### Booking Creation Flow
1. **Frontend** submits booking form (AJAX POST)
2. **Controller** validates request
3. **BookingService** checks availability & credits
4. **Database** transaction creates booking
5. **Response** returns JSON with booking details

### Ticket Download Flow
1. **User** clicks "Download Ticket" button
2. **Controller** calls `BookingController@downloadTicket`
3. **XmlTicketAdapter** parses SOAP/XML file
4. **DomPDF** generates PDF from Blade template
5. **Response** streams PDF download

---

## 6. Summary

| Feature | Technology | Purpose |
|---------|------------|---------|
| SOAP/XML | SimpleXMLElement | Ticket generation & interoperability |
| REST API | Laravel Controllers | Client communication |
| Rate Limiting | ThrottleRequests | Abuse prevention |
| Caching | Laravel Cache | Performance optimization |
| Authentication | Sanctum | API security |
| Validation | FormRequest | Data integrity |

---

**Last Updated:** December 20, 2025  
**API Version:** 1.0
