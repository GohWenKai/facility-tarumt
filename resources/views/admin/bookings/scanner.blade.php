@extends('layouts.admin')

@section('content')
<style>
.scanner-container {
    max-width: 600px;
    margin: 0 auto;
}

/* Main Scanner Card */
.scanner-card {
    background: #0f172a; /* Slate-900: Reduces eye strain, professional appearance */
    border-radius: 20px; /* Soft corners = modern, approachable feel */
    overflow: hidden;
    position: relative;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.2);
    /* Elevated shadow = importance hierarchy */
    min-height: 480px;
    display: flex;
    flex-direction: column;
}

/* Header with Controls */
.scanner-header {
    background: linear-gradient(180deg, rgba(0, 0, 0, 0.6) 0%, rgba(0, 0, 0, 0.3) 100%);
    /* Gradient = depth perception, doesn't block camera view */
    backdrop-filter: blur(12px); /* Glassmorphism = modern, clean */
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    z-index: 20; /* Above viewfinder overlay */
    padding: 1rem 1.25rem;
    display: flex;
    align-items: center;
    justify-content: space-between; /* Space for stop button */
}

.scanner-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: white;
    font-size: 1.125rem;
    font-weight: 700;
    margin: 0;
    letter-spacing: -0.01em; /* Improved readability */
}

/* Stop Button - HCI Principle: Escape hatch always visible */
.btn-stop {
    display: none; /* Hidden until camera starts */
    align-items: center;
    gap: 0.375rem;
    padding: 0.5rem 1rem;
    background: rgba(239, 68, 68, 0.9); /* Red with transparency */
    color: white;
    border: 2px solid rgba(255, 255, 255, 0.3); /* Subtle border for depth */
    border-radius: 10px;
    font-size: 0.875rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    white-space: nowrap;
    /* Fitts's Law: Adequate size for easy clicking */
    min-height: 40px;
}

.btn-stop:hover {
    background: rgba(220, 38, 38, 1); /* Darker red = clear hover state */
    transform: translateY(-1px); /* Micro-interaction feedback */
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3);
}

.btn-stop:active {
    transform: translateY(0); /* Press effect */
}

/* Scanner Viewport */
.scanner-viewport {
    height: 400px;
    background: #1e293b; /* Slate-800: Subtle contrast from card background */
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    flex: 1;
}

/* HTML5-QRCode styling */
#reader {
    width: 100%;
    height: 100%;
}

#reader video {
    object-fit: cover;
    border-radius: 0; /* No radius on video for full viewport usage */
    width: 100% !important;
    height: 100% !important;
}

/* Hide default HTML5-QRCode UI elements that conflict with our design */
#reader__dashboard_section {
    display: none !important;
}

/* Viewfinder Overlay - Guides user attention (Gestalt: Figure-ground) */
.viewfinder-overlay {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 260px;
    height: 260px;
    border: 2px solid rgba(255, 255, 255, 0.3); /* Subtle frame */
    border-radius: 24px;
    /* Dark overlay outside = focuses attention inside (Spotlight effect) */
    box-shadow: 0 0 0 4000px rgba(0, 0, 0, 0.65);
    z-index: 10;
    pointer-events: none;
}

/* Animated scan line - Provides continuous feedback */
.scan-line {
    position: absolute;
    width: 100%;
    height: 3px;
    background: linear-gradient(90deg, 
        transparent 0%, 
        #3b82f6 50%, 
        transparent 100%
    );
    /* Blue = action color, gradient = direction indicator */
    box-shadow: 0 0 10px rgba(59, 130, 246, 0.8);
    animation: scan 2s ease-in-out infinite;
}

@keyframes scan {
    0%, 100% { top: 20%; opacity: 0; }
    50% { top: 80%; opacity: 1; }
}

/* Corner Brackets - Visual affordance for "scan area" */
.viewfinder-corner {
    position: absolute;
    width: 48px;
    height: 48px;
    border-color: #3b82f6; /* Blue-500: Action color */
    border-style: solid;
    border-width: 4px;
    /* Thicker borders = clearer visual cue */
}

.vl { 
    top: -2px; 
    left: -2px; 
    border-right: 0; 
    border-bottom: 0; 
    border-top-left-radius: 20px; 
}

.vr { 
    top: -2px; 
    right: -2px; 
    border-left: 0; 
    border-bottom: 0; 
    border-top-right-radius: 20px; 
}

.bl { 
    bottom: -2px; 
    left: -2px; 
    border-right: 0; 
    border-top: 0; 
    border-bottom-left-radius: 20px; 
}

.br { 
    bottom: -2px; 
    right: -2px; 
    border-left: 0; 
    border-top: 0; 
    border-bottom-right-radius: 20px; 
}

/* Instruction Text - Positioned for visibility without obstruction */
.scan-instruction {
    position: absolute;
    bottom: 2rem;
    left: 1rem;
    right: 1rem;
    text-align: center;
    color: white;
    font-size: 0.9375rem;
    font-weight: 500;
    z-index: 15;
    text-shadow: 0 2px 8px rgba(0, 0, 0, 0.8); /* Strong shadow for readability */
    background: rgba(0, 0, 0, 0.4);
    padding: 0.625rem 1rem;
    border-radius: 12px;
    backdrop-filter: blur(8px);
}

/* Stopped State - Clear communication when scanner inactive */
.stopped-state {
    display: none;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 1rem;
    color: rgba(255, 255, 255, 0.8);
    font-size: 1rem;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 15;
    text-align: center;
    width: 100%;
    padding: 2rem;
}

.stopped-state i {
    font-size: 3rem;
    opacity: 0.7;
}

/* Restart Button */
.btn-restart {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.875rem 1.75rem;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    /* Green = positive action (restart/go) */
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3);
    margin-top: 1rem;
}

.btn-restart:hover {
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 12px -2px rgba(16, 185, 129, 0.4);
}

.btn-restart:active {
    transform: translateY(0);
}

/* Manual Entry Section */
.manual-entry-card {
    background: white;
    border-radius: 16px;
    padding: 1.75rem;
    margin-top: 1.5rem;
    border: 2px solid #e2e8f0; /* Slate-200: Subtle but defined boundary */
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.08);
    transition: border-color 0.2s ease;
}

.manual-entry-card:focus-within {
    border-color: #3b82f6; /* Blue focus state = active section */
}

.manual-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1.25rem;
    color: #475569; /* Slate-600: Readable but de-emphasized vs primary content */
    font-size: 0.8125rem;
    text-transform: uppercase;
    letter-spacing: 0.075em;
    font-weight: 700;
}

.manual-header i {
    font-size: 1.125rem;
    color: #64748b; /* Slate-500 */
}

/* Input Group */
.input-group-custom {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap; /* Mobile responsiveness */
}

.form-input-lg {
    flex: 1;
    min-width: 200px; /* Prevents too-narrow input on mobile */
    padding: 1rem 1.25rem;
    border: 2px solid #cbd5e1; /* Slate-300: Clear boundary */
    border-radius: 12px;
    font-size: 1rem;
    transition: all 0.2s ease;
    background: #f8fafc; /* Slate-50: Subtle field indication */
}

.form-input-lg:focus {
    border-color: #3b82f6; /* Blue = focused state */
    background: white;
    outline: none;
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); /* Soft glow = attention */
}

.form-input-lg::placeholder {
    color: #94a3b8; /* Slate-400: Readable but clearly placeholder */
}

/* Primary Action Button */
.btn-primary-lg {
    padding: 1rem 2rem;
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    /* Gradient = premium feel, direction = forward action */
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.3);
    /* Blue shadow = depth + brand consistency */
    white-space: nowrap;
    min-height: 56px; /* Fitts's Law: Large enough for easy interaction */
}

.btn-primary-lg:hover {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 12px -2px rgba(37, 99, 235, 0.4);
}

.btn-primary-lg:active {
    transform: translateY(0);
}

/* Help Text */
.help-text {
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
    margin-top: 1rem;
    padding: 0.875rem;
    background: #f1f5f9; /* Slate-100: Subtle info background */
    border-radius: 10px;
    color: #475569; /* Slate-600 */
    font-size: 0.875rem;
    line-height: 1.5;
}

.help-text i {
    color: #3b82f6; /* Blue: Matches info icon convention */
    margin-top: 0.125rem;
    flex-shrink: 0;
}

/* Responsive Design */
@media (max-width: 640px) {
    .scanner-header {
        padding: 0.875rem 1rem;
        flex-direction: column;
        gap: 0.5rem;
        align-items: flex-start;
    }
    
    .btn-stop {
        position: absolute;
        top: 0.875rem;
        right: 1rem;
    }
    
    .input-group-custom {
        flex-direction: column;
    }
    
    .form-input-lg,
    .btn-primary-lg {
        width: 100%;
    }
}

/* Loading State (if needed) */
.loading-indicator {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-size: 1.125rem;
    text-align: center;
    z-index: 5;
}

.spinner {
    border: 3px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    border-top: 3px solid white;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
    margin: 0 auto 1rem;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

<div class="container py-4">
    <div class="scanner-container">
        
        <!-- Scanner Card -->
        <div class="scanner-card">
            <!-- Header with Title and Stop Button -->
            <div class="scanner-header">
                <h5 class="scanner-title">
                    <i class="bi bi-qr-code-scan"></i>
                    QR Check-in
                </h5>
                <button id="btnStop" class="btn-stop" onclick="stopScanning()">
                    <i class="bi bi-stop-circle"></i>
                    Stop
                </button>
            </div>
            
            <!-- Viewport -->
            <div class="scanner-viewport">
                <!-- HTML5-QRCode Reader -->
                <div id="reader"></div>
                
                <!-- Loading Indicator (shown before camera starts) -->
                <div id="loadingIndicator" class="loading-indicator">
                    <div class="spinner"></div>
                    <div>Initializing camera...</div>
                </div>
                
                <!-- Viewfinder Overlay -->
                <div id="viewfinder" class="viewfinder-overlay">
                    <div class="scan-line"></div>
                    <div class="viewfinder-corner vl"></div>
                    <div class="viewfinder-corner vr"></div>
                    <div class="viewfinder-corner bl"></div>
                    <div class="viewfinder-corner br"></div>
                </div>

                <!-- Instruction Text -->
                <div id="instruction" class="scan-instruction">
                    <i class="bi bi-camera me-2"></i>
                    Point camera at the ticket QR code
                </div>
                
                <!-- Stopped State -->
                <div id="stoppedState" class="stopped-state">
                    <i class="bi bi-pause-circle"></i>
                    <div>Scanner stopped</div>
                    <button class="btn-restart" onclick="restartScanning()">
                        <i class="bi bi-arrow-clockwise"></i>
                        Restart Scanner
                    </button>
                </div>
            </div>
        </div>

        <!-- Manual Entry Section -->
        <div class="manual-entry-card">
            <div class="manual-header">
                <i class="bi bi-keyboard"></i>
                <span>Manual Entry</span>
            </div>
            <div class="input-group-custom">
                <input 
                    type="text" 
                    id="manualBookingId" 
                    class="form-input-lg" 
                    placeholder="Enter Booking ID (e.g., 550e8400)"
                    aria-label="Booking ID"
                >
                <button class="btn-primary-lg" onclick="manualCheckin()">
                    <i class="bi bi-arrow-right-circle me-2"></i>
                    Check In
                </button>
            </div>
            <div class="help-text">
                <i class="bi bi-info-circle"></i>
                <span>Use manual entry if the QR code is damaged or unreadable.</span>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js" type="text/javascript"></script>
<script>
    let html5QrCode = null;
    let isProcessing = false;

    // Success handler
    function onScanSuccess(decodedText, decodedResult) {
        if (isProcessing) return;
        isProcessing = true;

        // Haptic feedback
        if (navigator.vibrate) {
            navigator.vibrate([200, 100, 200]);
        }

        // Visual feedback
        const viewfinder = document.getElementById('viewfinder');
        if (viewfinder) viewfinder.style.borderColor = '#10b981';
        
        // Stop scanner before redirect
        if (html5QrCode) {
            html5QrCode.stop().catch(e => console.log(e));
        }
        
        // Process the scan
        if (decodedText.includes('/checkin')) {
            window.location.href = decodedText;
        } else {
            const id = decodedText.trim();
            window.location.href = `/admin/bookings/${id}/checkin`;
        }
    }

    // Initialize scanner with direct camera access
    async function initScanner() {
        const readerElement = document.getElementById('reader');
        const loadingIndicator = document.getElementById('loadingIndicator');
        const viewfinder = document.getElementById('viewfinder');
        const instruction = document.getElementById('instruction');
        const btnStop = document.getElementById('btnStop');
        
        try {
            html5QrCode = new Html5Qrcode("reader");
            
            // Get available cameras
            const devices = await Html5Qrcode.getCameras();
            
            if (!devices || devices.length === 0) {
                loadingIndicator.innerHTML = '<div style="color: #ef4444;"><i class="bi bi-camera-video-off" style="font-size: 2rem;"></i><div>No camera found</div><div style="font-size: 0.8rem; margin-top: 0.5rem;">Please check camera permissions</div></div>';
                return;
            }
            
            console.log('Available cameras:', devices);
            
            // Prefer back camera, fallback to first available
            let cameraId = devices[0].id;
            for (const device of devices) {
                if (device.label.toLowerCase().includes('back') || 
                    device.label.toLowerCase().includes('rear') ||
                    device.label.toLowerCase().includes('environment')) {
                    cameraId = device.id;
                    break;
                }
            }
            
            // Start scanning
            await html5QrCode.start(
                cameraId,
                {
                    fps: 10,
                    qrbox: { width: 250, height: 250 },
                    aspectRatio: 1.0
                },
                onScanSuccess,
                (errorMessage) => {
                    // Ignore scan errors (normal when no QR code in view)
                }
            );
            
            // Camera started successfully - update UI
            if (loadingIndicator) loadingIndicator.style.display = 'none';
            if (viewfinder) viewfinder.style.display = 'block';
            if (instruction) instruction.style.display = 'block';
            if (btnStop) btnStop.style.display = 'flex';
            
            console.log('Camera started successfully');
            
        } catch (err) {
            console.error('Camera init error:', err);
            
            let errorMsg = 'Camera error';
            if (err.name === 'NotAllowedError') {
                errorMsg = 'Camera permission denied. Please allow camera access.';
            } else if (err.name === 'NotFoundError') {
                errorMsg = 'No camera found on this device.';
            } else if (err.name === 'NotReadableError') {
                errorMsg = 'Camera is in use by another application.';
            } else {
                errorMsg = err.message || 'Unknown camera error';
            }
            
            if (loadingIndicator) {
                loadingIndicator.innerHTML = `
                    <div style="color: #ef4444;">
                        <i class="bi bi-exclamation-triangle" style="font-size: 2rem;"></i>
                        <div style="margin-top: 0.5rem;">${errorMsg}</div>
                        <button onclick="location.reload()" class="btn btn-outline-light btn-sm mt-3">
                            <i class="bi bi-arrow-clockwise me-1"></i> Retry
                        </button>
                    </div>`;
            }
        }
    }

    // Stop scanning
    async function stopScanning() {
        if (!html5QrCode) return;
        
        try {
            await html5QrCode.stop();
            document.getElementById('viewfinder').style.display = 'none';
            document.getElementById('instruction').style.display = 'none';
            document.getElementById('stoppedState').style.display = 'flex';
            document.getElementById('btnStop').style.display = 'none';
            document.getElementById('loadingIndicator').style.display = 'none';
        } catch (error) {
            console.error("Failed to stop:", error);
        }
    }

    // Restart scanning
    function restartScanning() {
        document.getElementById('stoppedState').style.display = 'none';
        document.getElementById('loadingIndicator').style.display = 'block';
        document.getElementById('loadingIndicator').innerHTML = '<div class="spinner"></div><div>Restarting camera...</div>';
        isProcessing = false;
        initScanner();
    }

    // Manual check-in
    function manualCheckin() {
        const input = document.getElementById('manualBookingId');
        const id = input.value.trim();
        
        if (!id) {
            input.focus();
            input.style.borderColor = '#ef4444';
            setTimeout(() => input.style.borderColor = '', 2000);
            return;
        }
        
        window.location.href = `/admin/bookings/${id}/checkin`;
    }

    // Enter key for manual entry
    document.getElementById('manualBookingId')?.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') manualCheckin();
    });

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', () => {
        initScanner();
    });
</script>
@endpush
@endsection