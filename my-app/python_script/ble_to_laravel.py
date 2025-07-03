import requests
import random
import time

LARAVEL_API_URL = "http://127.0.0.1:8000/api/temperature"

def generate_dummy_temperature():
    return round(random.uniform(20.0, 40.0), 2)

def send_temperature():
    temp = generate_dummy_temperature()
    payload = {"temperature": temp}
    headers = {
        "Accept": "application/json",
        "Content-Type": "application/json",
    }

    try:
        response = requests.post(LARAVEL_API_URL, json=payload, headers=headers)

        if response.status_code == 200:
            print(f"✅ Sent temperature: {temp}°C")
        else:
            print(f"❌ Failed to send temperature. Status: {response.status_code}, Message: {response.text}")
    except Exception as e:
        print(f"❗ Error: {e}")

if __name__ == "__main__":
    print("📡 Starting dummy temperature sender...")
    while True:
        send_temperature()
        time.sleep(3)
