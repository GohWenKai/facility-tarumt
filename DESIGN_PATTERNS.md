# Design Patterns Implementation Report

## Overview
This document outlines the software design patterns implemented in the Facility Booking System. The use of these patterns ensures the application is scalable, maintainable, and adheres to modern software architecture principles.

---

## 1. Observer Pattern
**Class:** `App\Observers\AssetObserver` ([View Source](app/Observers/AssetObserver.php))

### Description
The Observer pattern is used to define a one-to-many dependency between objects so that when one object changes state, all its dependents are notified and updated automatically.

### Implementation
We implemented `AssetObserver` to listen for `created`, `updated`, and `deleted` events on the `Asset` model.
- **Subject**: `Asset` (Model)
- **Observer**: `AssetObserver`

### Benefits (Justification)
- **Decoupling**: The business logic for creation (saving) is separated from the side-effects (Audit Logging, XML File Generation).
- **Single Responsibility**: The `Asset` model focuses on database interactions, while the `AssetObserver` handles logging.
- **Scalability**: New reactions to asset changes (e.g., sending emails) can be added to the Observer without modifying the Model code.

---

## 2. Adapter Pattern
**Class:** `App\Adapters\ChartJsAdapter` ([View Source](app/Adapters/ChartJsAdapter.php))
**Class:** `App\Adapters\XmlTicketAdapter` ([View Source](app/Adapters/XmlTicketAdapter.php))

### Description
The Adapter pattern allows incompatible interfaces to work together. It acts as a bridge between two objects.

### Implementation
- `ChartJsAdapter`: Transforms raw database collections into Chart.js format.
- `XmlTicketAdapter`: Parses SOAP/XML ticket files into PHP arrays for PDF generation.

```php
// XmlTicketAdapter - Converts XML to Array
public function parseTicket($bookingId): array
{
    $xml = Storage::get("xml/{$bookingId}.xml");
    $envelope = simplexml_load_string($xml);
    return [
        'id' => (string) $envelope->Body->ticket->id,
        'name' => (string) $envelope->Body->ticket->student_name,
        // ... more fields
    ];
}
```

### Benefits (Justification)
- **Interoperability**: Backend doesn't need to know about frontend charting requirements.
- **Modularity**: Switching from Chart.js to ApexCharts only requires updating the Adapter.
- **Data Format Conversion**: XML to Array conversion is isolated from business logic.

---

## 3. Service Layer Pattern
**Class:** `App\Services\AdminDashboardService` ([View Source](app/Services/AdminDashboardService.php))
**Class:** `App\Services\BookingService` ([View Source](app/Services/BookingService.php))
**Class:** `App\Services\AuthService` ([View Source](app/Services/AuthService.php))

### Description
The Service Layer pattern defines a set of available operations in this application's boundary. It encapsulates the application's business logic.

### Implementation
| Service | Responsibility |
|---------|---------------|
| `AdminDashboardService` | Dashboard stats, charts, analytics |
| `BookingService` | Booking creation, cancellation, credit management |
| `AuthService` | Login, OTP verification, session management |
| `SuspiciousActivityDetector` | Security threat detection |

### Benefits (Justification)
- **Reusability**: Services can be reused by API controllers, Console commands, or queued jobs.
- **Testability**: Services can be easily unit tested with mocked dependencies.
- **Slim Controllers**: Controllers remain "thin" (just handling Request/Response).

---

## 4. State Pattern ⭐
**Context:** `App\Services\BookingContext` ([View Source](app/Services/BookingContext.php))
**States:** `App\Patterns\State\*` ([View Folder](app/Patterns/State/))

### Description
The State pattern allows an object to alter its behavior when its internal state changes. The object will appear to change its class.

### Implementation
We use the State pattern to manage booking status transitions (Pending → Approved/Rejected).

```
┌─────────────────┐
│  BookingContext │  (Context)
│  getState()     │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  BookingState   │  (Interface)
│  approve()      │
│  reject()       │
└────────┬────────┘
         │
    ┌────┴────┬────────────┐
    ▼         ▼            ▼
┌────────┐ ┌────────┐ ┌────────┐
│Pending │ │Approved│ │Rejected│
│State   │ │State   │ │State   │
└────────┘ └────────┘ └────────┘
```

**State Classes:**
| State | approve() | reject() |
|-------|-----------|----------|
| `PendingState` | ✅ Updates to Approved, generates XML ticket, sends email | ✅ Refunds credits, updates to Rejected |
| `ApprovedState` | ❌ Throws exception (already approved) | ❌ Throws exception |
| `RejectedState` | ❌ Throws exception | ❌ Throws exception (already rejected) |

```php
// BookingContext.php - State Factory
public static function getState(Booking $booking): BookingState
{
    return match ($booking->status) {
        'approved' => new ApprovedState(),
        'rejected' => new RejectedState(),
        default    => new PendingState(),
    };
}

// AdminBookingController.php - Usage
$state = BookingContext::getState($booking);
$message = $state->approve($booking); // Behavior depends on current state
```

### Benefits (Justification)
- **Clean Code**: Eliminates complex if-else chains for status checks.
- **Open/Closed Principle**: New states can be added without modifying existing code.
- **Single Responsibility**: Each state class handles only its own transitions.
- **Maintainability**: Business rules for each state are isolated and easy to modify.

---

## 5. Strategy Pattern ⭐
**Context:** `App\Services\BookingSearchContext` ([View Source](app/Services/BookingSearchContext.php))

### Description
The Strategy pattern defines a family of algorithms, encapsulates each one, and makes them interchangeable. It lets the algorithm vary independently from clients that use it.

### Implementation
We use Strategy pattern for flexible booking search algorithms.

```php
// Different search strategies
interface SearchStrategy {
    public function search(array $criteria): Collection;
}

class DateRangeSearch implements SearchStrategy { ... }
class FacilitySearch implements SearchStrategy { ... }
class StatusSearch implements SearchStrategy { ... }

// Context selects strategy at runtime
$context->setStrategy(new DateRangeSearch());
$results = $context->executeSearch($criteria);
```

### Benefits (Justification)
- **Flexibility**: Search behavior can be changed at runtime.
- **Extensibility**: New search strategies can be added without changing existing code.
- **Testing**: Each strategy can be tested in isolation.

---

## 6. Model-View-Controller (MVC)
**Context:** Entire Application Architecture

### Description
MVC separates the application into three main logical components: the Model, the View, and the Controller.

### Implementation
- **Model** (`App\Models\Booking`): Represents the data structure.
- **View** (`resources/views/users/bookings/history.blade.php`): Presents the data (UI).
- **Controller** (`App\Http\Controllers\BookingController`): Handles user input and updates the Model.

### Benefits (Justification)
- **Structure**: Clear separation of concerns makes the codebase easy to navigate.
- **Parallel Development**: Developers can work on Views (Frontend) and Controllers (Backend) simultaneously.

---

## Summary

| Pattern | Location | Purpose |
|---------|----------|---------|
| Observer | `AssetObserver` | Auto-logging on model changes |
| Adapter | `ChartJsAdapter`, `XmlTicketAdapter` | Data format conversion |
| Service Layer | `*Service.php` | Business logic encapsulation |
| **State** | `Patterns/State/*` | Booking status behavior management |
| **Strategy** | `BookingSearchContext` | Flexible search algorithms |
| MVC | Application-wide | Separation of concerns |

---

**Last Updated:** December 20, 2025

