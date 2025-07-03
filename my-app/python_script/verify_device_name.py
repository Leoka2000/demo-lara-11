from bleak import BleakScanner

import asyncio

async def scan_devices():
    devices = await BleakScanner.discover(timeout=10)
    for d in devices:
        print(f"Found: {d.name} (Address: {d.address})")

asyncio.run(scan_devices())
