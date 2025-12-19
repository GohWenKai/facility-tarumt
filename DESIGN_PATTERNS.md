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

### Description
The Adapter pattern allows incompatible interfaces to work together. It acts as a bridge between two objects.

### Implementation
We use `ChartJsAdapter` to transform raw database collections (from Eloquent models) into the specific array format required by the Chart.js frontend library.
- **Input**: `data->pluck('total')` (Database Format)
- **Output**: `{ labels: [...], data: [...] }` (Chart.js Format)

### Benefits (Justification)
- **Interoperability**: The backend Service layer does not need to know about frontend charting requirements. 
- **Modularity**: If we switch from Chart.js to another library (e.g., ApexCharts), we only update the Adapter, not the Service logic.

---

## 3. Service Layer Pattern
**Class:** `App\Services\AdminDashboardService` ([View Source](app/Services/AdminDashboardService.php))
**Class:** `App\Services\SuspiciousActivityDetector` ([View Source](app/Services/SuspiciousActivityDetector.php))

### Description
The Service Layer pattern defines a set of available operations in this application's boundary. It encapsulates the application's business logic.

### Implementation
- `AdminDashboardService`: Encapsulates complex queries for gathering schedule data, calculating popular stats, and filtering damaged assets.
- `SuspiciousActivityDetector`: Contains the logic for detecting security threats (bulk deletions, rapid actions).

### Benefits (Justification)
- **Reusability**: The logic in `AdminDashboardService` can be reused by API controllers or Console commands.
- **Testability**: Services can be easily unit tested with mocked dependencies, unlike Controllers which are tied to HTTP requests.
- **Slim Controllers**: The Controllers remain "thin" (just handling Request/Response), while heavy logic sits in the Service.

---

## 4. Model-View-Controller (MVC)
**Context:** Entire Application Architecture

### Description
MVC separates the application into three main logical components: the Model, the View, and the Controller.

### Implementation
- **Model** (`App\Models\Asset`): Represents the data structure.
- **View** (`resources/views/admin/audit_logs/index.blade.php`): Presents the data (UI).
- **Controller** (`App\Http\Controllers\Admin\AuditLogController`): Handles the user input and updates the Model.

### Benefits (Justification)
- **Structure**: Clear separation of concerns makes the codebase easy to navigate.
- **Parallel Development**: Developers can work on Views (Frontend) and Controllers (Backend) simultaneously.
