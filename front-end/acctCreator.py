import csv
import requests

api_base_url = "http://localhost:8080/index.php"  # Replace with your actual API URL

def add_users_from_csv(file_path):
    with open(file_path, newline='', encoding='utf-8') as csvfile:
        reader = csv.reader(csvfile)
        next(reader)  # Skip the header row
        
        for row in reader:
            if len(row) < 4:
                continue  # Skip invalid rows
            
            user_data = {
                "fullname": row[0].strip(),
                "username": row[1].strip(),
                "email": row[2].strip(),
                "password": row[3].strip()
            }
            
            try:
                response = requests.post(f"{api_base_url}?request=register", json=user_data)
                data = response.json()
                print(f"User {user_data['username']} registration response:", data)
            except Exception as e:
                print(f"Error registering user {user_data['username']}: {e}")

# Example usage:
add_users_from_csv("hello.csv")