from bleak import BleakScanner, BleakClient
import asyncio
import websockets
import json

# Configuration
DEVICE_NAME = "LeoPayload"
CHARACTERISTIC_UUID = "ABCDEFAB-1234-5678-9ABC-DEF123456789"  # Your actual UUID
LARAVEL_WS_URL = "ws://localhost:8080/app/kyvkshzfjpzfeiv0y4et"  # Update with your Reverb config

async def parse_ble_data(data: bytearray):
    """Parse the BLE data into timestamp and temperature"""
    hex_str = data.hex()
    timestamp_hex = hex_str[:8]  # First 8 characters
    temp_hex = hex_str[8:12]    # Next 4 characters
    return int(timestamp_hex, 16), int(temp_hex, 16)

async def send_to_laravel(websocket, timestamp, temperature):
    """Send data to Laravel via WebSocket"""
    payload = json.dumps({
        "event": "battery.temperature",
        "data": {
            "timestamp": timestamp,
            "temperature": temperature
        }
    })
    await websocket.send(payload)

async def monitor_ble_and_send():
    # Connect to Laravel WebSocket first
    try:
        async with websockets.connect(LARAVEL_WS_URL) as websocket:
            print("✅ Connected to Laravel WebSocket")

            # Then connect to BLE device
            device = await BleakScanner.find_device_by_filter(
                lambda d, ad: d.name and d.name.lower() == DEVICE_NAME.lower()
            )

            if not device:
                print(f"❌ Device '{DEVICE_NAME}' not found")
                return

            print(f"✅ Found {device.name} at {device.address}")

            async with BleakClient(device) as client:
                print("🔗 Connected to BLE device")
                print("📊 Starting data monitoring... (Ctrl+C to stop)")

                while True:
                    try:
                        data = await client.read_gatt_char(CHARACTERISTIC_UUID)
                        timestamp, temp = await parse_ble_data(data)

                        # Print to console
                        print(f"⏱ {timestamp} | 🌡 {temp}°C", end="\r")

                        # Send to Laravel
                        await send_to_laravel(websocket, timestamp, temp)
                        await asyncio.sleep(1.0)

                    except Exception as e:
                        print(f"⚠️ Error: {e}")
                        break

    except Exception as e:
        print(f"❌ WebSocket connection failed: {e}")

if __name__ == "__main__":
    try:
        asyncio.run(monitor_ble_and_send())
    except KeyboardInterrupt:
        print("\n🛑 Monitoring stopped by user")



#DEVICE_NAME = "LeoPayload"  # From your BLE scanner screenshot
#SERVICE_UUID = "12345678-1234-1234-1234-1234567890AB"  # Replace XXXX with your service UUID
#CHARACTERISTIC_UUID = "ABCDEFAB-1234-5678-9ABC-DEF123456789"  # Replace YYYY with your characteristic UUID
