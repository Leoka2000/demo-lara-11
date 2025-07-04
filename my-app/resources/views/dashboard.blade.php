<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div
                class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 bg-50 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full " />
            </div>
            <div
                class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 bg-50 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full " />
            </div>
            <div
                class="relative aspect-video overflow-hidden rounded-xl border bg-50 border-neutral-200 bg-50 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full " />
            </div>
        </div>
        <div
            class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <x-placeholder-pattern class="absolute inset-0 size-full " />
            <div class="relative">
                <button id="connectBtn" class=" bg-blue-600 text-xs text-white rounded hover:bg-blue-700">
                    Connect to Bluetooth Device
                </button>

                <div class="">
                    <div class="text-xs  rounded">
                        <h3 class="font-bold text-xs">Device Status:</h3>
                        <p class="text-xs" id="deviceStatus">Not connected</p>
                    </div>

                    <div class=" text-xs 0 rounded">
                        <h3 class=" text-xs font-bold">Temperature Data:</h3>

                        <p class="text-xs" id="temperatureData">No data received yet</p>
                    </div>

                    <div class=" bg-gray-100 text-xs  rounded">
                        <h3 class="font-bold hidden text-xs ">WebSocket Log:</h3>
                        <div id="wsLog" class=" overflow-y-auto bg-white hidden rounded text-xs font-mono"></div>
                        {{-- VERY IMPORTANT THE WEBSOCKET LOG!!! if it is removed, the function that button triggers
                        does not work --}}
                    </div>
                </div>
            </div>
            <div class="h-[40rem]">
                <div class="h-full" id="historical_data"></div>
            </div>
        </div>
    </div>

    <script>
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
    </script>


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
        Livewire.dispatch('temperature.updated', {
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



    <script>
        document.addEventListener("DOMContentLoaded", function () {
    const maxPoints = 20;
    // Initialize data array for dynamic chart
    let data = [];
    let lastDate = new Date().getTime();
    const XAXISRANGE = 60000; // 60 seconds range

    // Function to generate new data point
    function getNewSeries(baseval, yrange) {
        const newDate = baseval + 1000;
        lastDate = newDate;

        return {
            x: newDate,
            y: Math.floor(Math.random() * (yrange.max - yrange.min + 1)) + yrange.min
        };
    }

    // Initialize with empty data
    for (let i = 0; i < maxPoints; i++) {
        data.push(getNewSeries(lastDate, { min: 10, max: 90 }));
    }

    // Initialize chart with the options you provided
    var chart = new ApexCharts(document.querySelector("#historical_data"), {
        series: [{
            data: data.slice()
        }],
        chart: {
            id: 'realtime',
            height: 500,
            type: 'line',
            animations: {
                enabled: true,
                easing: 'linear',
                dynamicAnimation: {
                    speed: 1000
                }
            },
            toolbar: {
                show: false
            },
            zoom: {
                enabled: false
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 3
        },
        title: {
            text: 'Dynamic Temperature Data',
            align: 'left',
            style: {
                color: '#374151',
                fontSize: '16px',
                fontWeight: 'thin'
            }
        },
        markers: {
            size: 0
        },
        xaxis: {
            type: 'datetime',
            range: XAXISRANGE,
            title: {
                text: 'Timestamp',
                style: {
                    color: '#374151',
                    fontWeight: 'bold',
                    fontSize: '14px',
                }
            }
        },
        yaxis: {
            max: 100,
            title: {
                text: 'Temperature (°C)',
                style: {
                    color: '#374151',
                    fontWeight: 'bold',
                    fontSize: '14px',
                }
            },
            min: 0
        },
        legend: {
            show: false
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return val + " °C";
                }
            }
        },
        grid: {
            borderColor: '#f1f1f1'
        }
    });

    chart.render();

    // Update chart with real data when Echo receives a broadcast
    if (typeof Echo !== 'undefined') {
        Echo.channel('battery.temperature')
            .listen('BatteryTemperature', (e) => {
                const temp = e.temperature;
                const time = e.timestamp * 1000; // Convert UNIX timestamp to milliseconds

                // Push new data point and trim if needed
                data.push({
                    x: time,
                    y: temp
                });

                if (data.length > maxPoints) {
                    data.shift();
                }

                // Update chart
                chart.updateSeries([{
                    data: data
                }]);
            });
    } else {
        console.warn("Laravel Echo is not available.");

        // Fallback to random data if Echo is not available (for testing)
        var interval = window.setInterval(function () {
            getNewSeries(lastDate, {
                min: 10,
                max: 90
            });

            data.push(getNewSeries(lastDate, {
                min: 10,
                max: 90
            }));

            if (data.length > maxPoints) {
                data.shift();
            }

            chart.updateSeries([{
                data: data
            }]);
        }, 1000);
    }
});
    </script>

</x-layouts.app>