import asyncio
from bleak import BleakScanner

async def main():
    print("🔍 Scanning for BLE devices...")
    devices = await BleakScanner.discover(timeout=5)
    for device in devices:
        print(f"📡 Name: {device.name}, Address: {device.address}")

asyncio.run(main())
