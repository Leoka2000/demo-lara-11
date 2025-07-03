from bleak import BleakScanner
from bleak import BleakClient


async def find_device_by_name(name):
    devices = await BleakScanner.discover(timeout=5)
    for device in devices:
        if device.name and name in device.name:
            print(f"✅ Found {name} at {device.address}")
            return device.address
    raise Exception(f"❌ Device {name} not found")

# Usage
import asyncio

async def main():
    device_address = await find_device_by_name("EV07BG")
    client = BleakClient(device_address)
    await client.connect(timeout=30.0)

asyncio.run(main())
