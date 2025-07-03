from bleak import BleakScanner, BleakClient
import asyncio

# Configuration - UPDATE THESE WITH YOUR ACTUAL VALUES
DEVICE_NAME = "LeoPayload"  # From your BLE scanner screenshot
SERVICE_UUID = "12345678-1234-1234-1234-1234567890AB"  # Replace XXXX with your service UUID
CHARACTERISTIC_UUID = "ABCDEFAB-1234-5678-9ABC-DEF123456789"  # Replace YYYY with your characteristic UUID

def parse_ble_data(data: bytearray):
    """Parse the BLE data into timestamp and temperature"""
    hex_str = data.hex()

    # Extract components
    timestamp_hex = hex_str[:8]  # First 8 characters
    temp_hex = hex_str[8:12]    # Next 4 characters

    # Convert to integers
    timestamp = int(timestamp_hex, 16)
    temperature = int(temp_hex, 16)

    return timestamp, temperature

async def connect_and_monitor():
    print("🔍 Scanning for BLE devices...")

    # Find the device
    device = await BleakScanner.find_device_by_filter(
        lambda d, ad: d.name and d.name.lower() == DEVICE_NAME.lower()
    )

    if not device:
        print(f"❌ Device '{DEVICE_NAME}' not found")
        return

    print(f"✅ Found {device.name} at {device.address}")

    # Connect and monitor
    async with BleakClient(device) as client:
        print("🔗 Connected to device")
        print("📊 Starting data monitoring...")
        print("Press Ctrl+C to stop\n")

        # Print header
        print("{:<15} {:<15} {:<10}".format("Timestamp (hex)", "Timestamp (dec)", "Temp (°C)"))
        print("-" * 45)

        while True:
            try:
                # Read the characteristic value
                data = await client.read_gatt_char(CHARACTERISTIC_UUID)

                # Parse the data
                timestamp, temp = parse_ble_data(data)

                # Display the data
                hex_str = data.hex()
                timestamp_hex = hex_str[:8]
                print("{:<15} {:<15} {:<10.2f}".format(
                    timestamp_hex,
                    timestamp,
                    temp
                ))

                # Adjust the polling interval as needed
                await asyncio.sleep(1.0)

            except Exception as e:
                print(f"⚠️ Error: {e}")
                break

# Run the program
try:
    asyncio.run(connect_and_monitor())
except KeyboardInterrupt:
    print("\n🛑 Monitoring stopped by user")
except Exception as e:
    print(f"❌ Fatal error: {e}")
