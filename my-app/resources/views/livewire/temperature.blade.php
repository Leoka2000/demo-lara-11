<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;



new class extends Component {
    public float $temperature = 0;
    public int $timestamp = 0;

    #[On('echo:battery.temperature,BatteryTemperature')]
    public function updateTemperature(array $event)
    {
        $this->temperature = $event['temperature'];
        $this->timestamp = $event['timestamp'];
    }
};
?>

<div>
    <div class="relative p-4">
        <button id="connectBtn" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Connect to Bluetooth Device
        </button>

        <div class="mt-4 space-y-2">
            <div class="p-4 bg-gray-100 rounded">
                <h3 class="font-bold">Device Status:</h3>
                <p id="deviceStatus">Not connected</p>
            </div>

            <div class="p-4 bg-gray-100 rounded">
                <h3 class="font-bold">Temperature Data:</h3>
                <p>Livewire Data: {{ $temperature }}°C at {{ date('Y-m-d H:i:s', $timestamp) }}</p>
                <p id="temperatureData">No data received yet</p>
            </div>

            <div class="p-4 bg-gray-100 rounded">
                <h3 class="font-bold">WebSocket Log:</h3>
                <div id="wsLog" class="h-32 overflow-y-auto bg-white p-2 rounded text-sm font-mono"></div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    const connectBtn = document.getElementById('connectBtn');
    const deviceStatus = document.getElementById('deviceStatus');
    const temperatureData = document.getElementById('temperatureData');
    const wsLog = document.getElementById('wsLog');

    let bluetoothDevice = null;
    let characteristic = null;
    let echo = null;

    // Initialize Echo for WebSockets
    function initializeEcho() {
        window.Echo = new Echo({
            broadcaster: 'reverb',
            key: 'kyvkshzfjpzfeiv0y4et', // Your Reverb key
            wsHost: window.location.hostname,
            wsPort: 8080,
            forceTLS: false,
            enabledTransports: ['ws', 'wss'],
        });

        logToConsole('Echo initialized');

        // Listen for temperature updates (optional, if you want to confirm the data was received by the server)
        window.Echo.channel('battery.temperature')
            .listen('BatteryTemperature', (data) => {
                logToConsole(`Received confirmation from server: ${data.temperature}°C at ${new Date(data.timestamp * 1000).toLocaleString()}`);
            });
    }

    // Log messages to the WebSocket log area
    function logToConsole(message) {
        const logEntry = document.createElement('div');
        logEntry.textContent = `[${new Date().toLocaleTimeString()}] ${message}`;
        wsLog.appendChild(logEntry);
        wsLog.scrollTop = wsLog.scrollHeight;
    }

    // Parse BLE data from hexadecimal to timestamp and temperature
    function parseBleData(data) {
        // Convert ArrayBuffer to hex string
        const hexArray = Array.from(new Uint8Array(data)).map(b => b.toString(16).padStart(2, '0'));
        const hexStr = hexArray.join('');

        // Extract timestamp (first 8 chars) and temperature (next 4 chars)
        const timestampHex = hexStr.substring(0, 8);
        const tempHex = hexStr.substring(8, 12);

        // Convert to decimal
        const timestamp = parseInt(timestampHex, 16);
        const temperature = parseInt(tempHex, 16) / 10; // Divide by 10 for human-readable

        return { timestamp, temperature };
    }

    // Connect to Bluetooth device
    async function connectToBluetooth() {
        try {
            logToConsole('Requesting Bluetooth device...');
            deviceStatus.textContent = 'Searching for device...';

            bluetoothDevice = await navigator.bluetooth.requestDevice({
                filters: [{ name: 'LeoPayload' }],
                optionalServices: ['12345678-1234-1234-1234-1234567890ab'] // Your service UUID
            });

            bluetoothDevice.addEventListener('gattserverdisconnected', onDisconnected);

            logToConsole(`Connecting to device: ${bluetoothDevice.name}`);
            deviceStatus.textContent = `Connecting to ${bluetoothDevice.name}...`;

            const server = await bluetoothDevice.gatt.connect();
            logToConsole('Connected to GATT server');

            const service = await server.getPrimaryService('12345678-1234-1234-1234-1234567890ab'); // Your service UUID
            logToConsole('Service found');

            characteristic = await service.getCharacteristic('abcdefab-1234-5678-9abc-def123456789'); // Your characteristic UUID
            logToConsole('Characteristic found');

            deviceStatus.textContent = `Connected to ${bluetoothDevice.name}`;
            connectBtn.textContent = 'Disconnect';
            connectBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
            connectBtn.classList.add('bg-red-600', 'hover:bg-red-700');

            // Start notifications
            await characteristic.startNotifications();
            logToConsole('Notifications started');

            characteristic.addEventListener('characteristicvaluechanged', handleNotifications);

        } catch (error) {
            logToConsole(`Error: ${error}`);
            deviceStatus.textContent = `Error: ${error.message}`;
        }
    }

    // Handle disconnection
    function onDisconnected() {
        logToConsole('Device disconnected');
        deviceStatus.textContent = 'Disconnected';
        connectBtn.textContent = 'Connect to Bluetooth Device';
        connectBtn.classList.remove('bg-red-600', 'hover:bg-red-700');
        connectBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');

        if (characteristic) {
            characteristic.removeEventListener('characteristicvaluechanged', handleNotifications);
            characteristic = null;
        }

        bluetoothDevice = null;
    }

    // Handle incoming notifications
    function handleNotifications(event) {
        const { timestamp, temperature } = parseBleData(event.target.value.buffer);

        // Update UI
        const date = new Date(timestamp * 1000);
        temperatureData.textContent = `${temperature}°C at ${date.toLocaleString()}`;

        logToConsole(`Received: ${temperature}°C at ${date.toLocaleString()}`);

        // Send to Laravel via WebSocket
        sendTemperatureToServer(timestamp, temperature);

        // Dispatch Livewire event
        Livewire.dispatch('temperature-updated', {
            timestamp,
            temperature
        });
    }

    // Send temperature data to Laravel via WebSocket
    function sendTemperatureToServer(timestamp, temperature) {
        if (!window.Echo) {
            logToConsole('Echo not initialized, cannot send data');
            return;
        }

        try {
            // Using axios to send data (alternative to Echo)
            axios.post('/api/temperature', {
                timestamp,
                temperature
            })
            .then(response => {
                logToConsole('Data sent to server successfully');
            })
            .catch(error => {
                logToConsole(`Error sending data: ${error.message}`);
            });

            // Alternatively, you could use Echo.connector.socket.send() directly
            // but the above method is more standard with Laravel

        } catch (error) {
            logToConsole(`WebSocket error: ${error}`);
        }
    }

    // Disconnect from Bluetooth device
    async function disconnectFromBluetooth() {
        if (!bluetoothDevice) return;

        try {
            if (bluetoothDevice.gatt.connected) {
                if (characteristic) {
                    await characteristic.stopNotifications();
                    characteristic.removeEventListener('characteristicvaluechanged', handleNotifications);
                }
                bluetoothDevice.gatt.disconnect();
            }
            onDisconnected();
        } catch (error) {
            logToConsole(`Error disconnecting: ${error}`);
        }
    }

    // Toggle connection
    connectBtn.addEventListener('click', async function() {
        if (bluetoothDevice && bluetoothDevice.gatt.connected) {
            await disconnectFromBluetooth();
        } else {
            await connectToBluetooth();
        }
    });

    // Initialize Echo when the page loads
    initializeEcho();

    // Check if Bluetooth is available
    if (!navigator.bluetooth) {
        deviceStatus.textContent = 'Web Bluetooth API is not supported in this browser';
        connectBtn.disabled = true;
        logToConsole('Web Bluetooth API not supported');
    }
});
</script>