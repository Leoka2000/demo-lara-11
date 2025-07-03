import requests
import time

LARAVEL_API_URL = "http://127.0.0.1:8000/api/temperature"

def get_temperature():
    try:
        response = requests.get(LARAVEL_API_URL)
        if response.status_code == 200:
            data = response.json()
            print(f"🌡️ Dummy temperature received: {data['temperature']}°C")
        else:
            print(f"❌ Failed to get temperature. Status: {response.status_code}")
    except Exception as e:
        print(f"❗ Error: {e}")

if __name__ == "__main__":
    print("📡 Starting dummy temperature fetcher...")
    while True:
        get_temperature()
        time.sleep(3)
