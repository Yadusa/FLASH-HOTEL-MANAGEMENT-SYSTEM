# create_admin.py
import mysql.connector
from werkzeug.security import generate_password_hash
import os

DB_CONFIG = {
    'host': os.environ.get('DB_HOST','localhost'),
    'user': os.environ.get('DB_USER','root'),
    'password': os.environ.get('DB_PASSWORD',''),
    'database': os.environ.get('DB_NAME','hotel_booking'),
    'auth_plugin': 'mysql_native_password'
}

def create_admin(username, password):
    h = generate_password_hash(password)
    conn = mysql.connector.connect(**DB_CONFIG)
    cur = conn.cursor()
    cur.execute("INSERT INTO admins (username, password_hash) VALUES (%s, %s)", (username, h))
    conn.commit()
    cur.close()
    conn.close()
    print("Created admin", username)

if __name__ == '__main__':
    create_admin('admin4', 'Admin@123')  # change password immediately
