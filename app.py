print("Flask app is starting...")

from flask import Flask
from flask_mysqldb import MySQL


app = Flask(__name__)

# --- MySQL Database Connection Info ---
app.config['MYSQL_USER'] = 'root'          # your MySQL username
app.config['MYSQL_PASSWORD'] = 'Hotelio12'  # your MySQL password
app.config['MYSQL_DB'] = 'hotel_db'  # your database name
app.config['MYSQL_HOST'] = 'localhost'     # server address (default: localhost)

mysql = MySQL(app)  # connect Flask to MySQL


@app.route("/")
def home():
    return "Hello, Flask is running on your PC!"

@app.route("/rooms")
def rooms():
    return "This is the rooms page."

@app.route("/booking")
def booking():
    return "This is the booking page."

@app.route("/signin")
def signin():
    return "This is the signin page."

if __name__ == '__main__':
    print("Flask app is starting...")
    app.run(debug=True)