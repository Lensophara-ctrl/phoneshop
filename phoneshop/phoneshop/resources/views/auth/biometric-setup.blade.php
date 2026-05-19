@extends('layouts.app')

@section('title', 'Biometric Setup')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-fingerprint me-2"></i>Biometric Authentication Setup</h4>
                </div>
                <div class="card-body">
                    <!-- Setup Options -->
                    <div class="row">
                        <!-- QR Code Pairing -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-qrcode fa-4x text-primary mb-3"></i>
                                    <h5>QR Code Pairing</h5>
                                    <p class="text-muted">Scan QR code with your mobile device to enable Face ID/Touch ID</p>
                                    <button class="btn btn-primary" onclick="generateQR()">
                                        <i class="fas fa-qrcode me-2"></i>Generate QR Code
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Manual Setup -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-mobile-alt fa-4x text-success mb-3"></i>
                                    <h5>Manual Setup</h5>
                                    <p class="text-muted">Register your device manually for biometric login</p>
                                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#manualSetupModal">
                                        <i class="fas fa-cog me-2"></i>Manual Setup
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- QR Code Display -->
                    <div id="qrCodeSection" class="text-center mt-4" style="display: none;">
                        <h5>Scan this QR Code</h5>
                        <div id="qrCodeDisplay" class="my-4"></div>
                        <p class="text-muted">QR code expires in <span id="qrTimer" class="fw-bold">10:00</span></p>
                        <button class="btn btn-secondary" onclick="hideQR()">Close</button>
                    </div>

                    <!-- Registered Devices -->
                    <div class="mt-5">
                        <h5><i class="fas fa-devices me-2"></i>Registered Devices</h5>
                        <div id="devicesList" class="mt-3">
                            <p class="text-muted">Loading devices...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- How It Works -->
            <div class="card shadow mt-4">
                <div class="card-body">
                    <h5><i class="fas fa-info-circle me-2"></i>How It Works</h5>
                    <ol>
                        <li><strong>QR Code Method:</strong> Generate QR code and scan with your iPhone/iPad</li>
                        <li><strong>Enable Biometrics:</strong> Your device will prompt for Face ID or Touch ID</li>
                        <li><strong>Login:</strong> Next time, just use Face ID/Touch ID to login instantly</li>
                        <li><strong>Security:</strong> Your biometric data never leaves your device</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Manual Setup Modal -->
<div class="modal fade" id="manualSetupModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Manual Device Setup</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="manualSetupForm">
                    <div class="mb-3">
                        <label class="form-label">Device Name</label>
                        <input type="text" class="form-control" id="deviceName" placeholder="e.g., iPhone 13 Pro" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Device ID</label>
                        <input type="text" class="form-control" id="deviceId" placeholder="Auto-generated" readonly>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        This will register your current device for biometric authentication.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="registerDevice()">Register Device</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
let qrCode = null;

// Generate unique device ID
document.getElementById('deviceId').value = generateDeviceId();

function generateDeviceId() {
    return 'device_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
}

async function generateQR() {
    try {
        const response = await fetch('/api/biometric/pairing/qr', {
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
                'Content-Type': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            displayQR(data.qr_data);
        } else {
            alert('Failed to generate QR code');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error generating QR code');
    }
}

function displayQR(qrData) {
    const qrSection = document.getElementById('qrCodeSection');
    const qrDisplay = document.getElementById('qrCodeDisplay');
    
    qrDisplay.innerHTML = '';
    qrSection.style.display = 'block';

    qrCode = new QRCode(qrDisplay, {
        text: qrData,
        width: 256,
        height: 256,
        colorDark: '#000000',
        colorLight: '#ffffff',
        correctLevel: QRCode.CorrectLevel.H
    });

    startQRTimer();
}

function hideQR() {
    document.getElementById('qrCodeSection').style.display = 'none';
}

function startQRTimer() {
    let timeLeft = 600; // 10 minutes
    const timerElement = document.getElementById('qrTimer');
    
    const countdown = setInterval(function() {
        timeLeft--;
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        timerElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        
        if (timeLeft <= 0) {
            clearInterval(countdown);
            hideQR();
            alert('QR code expired. Please generate a new one.');
        }
    }, 1000);
}

async function registerDevice() {
    const deviceName = document.getElementById('deviceName').value;
    const deviceId = document.getElementById('deviceId').value;

    if (!deviceName) {
        alert('Please enter a device name');
        return;
    }

    // Generate public key (in production, use WebAuthn API)
    const publicKey = await generatePublicKey();

    try {
        const response = await fetch('/api/biometric/register', {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                device_id: deviceId,
                device_name: deviceName,
                public_key: publicKey
            })
        });

        const data = await response.json();

        if (data.success) {
            alert('Device registered successfully!');
            bootstrap.Modal.getInstance(document.getElementById('manualSetupModal')).hide();
            loadDevices();
        } else {
            alert('Failed to register device');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error registering device');
    }
}

async function generatePublicKey() {
    // In production, use WebAuthn API to generate actual public key
    // For demo, generate a random key
    return 'pk_' + Math.random().toString(36).substr(2, 32);
}

async function loadDevices() {
    try {
        const response = await fetch('/api/biometric/devices', {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('auth_token')
            }
        });

        const data = await response.json();

        if (data.success) {
            displayDevices(data.devices);
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

function displayDevices(devices) {
    const devicesList = document.getElementById('devicesList');

    if (devices.length === 0) {
        devicesList.innerHTML = '<p class="text-muted">No devices registered yet</p>';
        return;
    }

    let html = '<div class="list-group">';
    devices.forEach(device => {
        html += `
            <div class="list-group-item">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1"><i class="fas fa-mobile-alt me-2"></i>${device.device_name}</h6>
                        <small class="text-muted">Last used: ${device.last_used || 'Never'}</small>
                    </div>
                    <button class="btn btn-sm btn-danger" onclick="revokeDevice(${device.id})">
                        <i class="fas fa-trash"></i> Remove
                    </button>
                </div>
            </div>
        `;
    });
    html += '</div>';

    devicesList.innerHTML = html;
}

async function revokeDevice(tokenId) {
    if (!confirm('Are you sure you want to remove this device?')) {
        return;
    }

    try {
        const response = await fetch(`/api/biometric/devices/${tokenId}`, {
            method: 'DELETE',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('auth_token')
            }
        });

        const data = await response.json();

        if (data.success) {
            alert('Device removed successfully');
            loadDevices();
        } else {
            alert('Failed to remove device');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error removing device');
    }
}

// Load devices on page load
document.addEventListener('DOMContentLoaded', loadDevices);
</script>
@endsection
