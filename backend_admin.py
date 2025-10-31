# backend.py
from http.server import BaseHTTPRequestHandler, HTTPServer
import json, urllib.parse, mysql.connector, configparser

# Read MySQL credentials
cfg = configparser.ConfigParser()
cfg.read('config.ini')
dbconf = cfg['mysql']

def get_db():
    return mysql.connector.connect(
        user=dbconf['user'],
        password=dbconf['password'],
        host=dbconf['host'],
        database=dbconf['database']
    )

class Handler(BaseHTTPRequestHandler):
    def _set_headers(self, status=200):
        self.send_response(status)
        self.send_header("Content-type", "application/json")
        self.send_header("Access-Control-Allow-Origin", "*")
        self.send_header("Access-Control-Allow-Headers", "Content-Type")
        self.send_header("Access-Control-Allow-Methods", "GET, POST, OPTIONS, DELETE")
        self.end_headers()

    def do_OPTIONS(self):  # CORS preflight
        self._set_headers()

    def do_GET(self):
        if self.path == "/api/bookings":
            try:
                conn = get_db()
                cur = conn.cursor(dictionary=True)
                cur.execute("SELECT * FROM bookings ORDER BY id DESC")
                rows = cur.fetchall()
                self._set_headers()
                self.wfile.write(json.dumps(rows, default=str).encode())
            except Exception as e:
                self._set_headers(500)
                self.wfile.write(json.dumps({"error": str(e)}).encode())
            finally:
                cur.close()
                conn.close()
        else:
            self._set_headers(404)
            self.wfile.write(b'{"error":"Not found"}')

    def do_POST(self):
        if self.path == "/api/bookings":
            length = int(self.headers.get('Content-Length', 0))
            data = json.loads(self.rfile.read(length))
            try:
                conn = get_db()
                cur = conn.cursor()
                cur.execute("""
                    INSERT INTO bookings (guest_name, room_type, check_in, check_out, price)
                    VALUES (%s, %s, %s, %s, %s)
                """, (data["name"], data["room"], data["checkin"], data["checkout"], data["price"]))
                conn.commit()
                self._set_headers(201)
                self.wfile.write(json.dumps({"message": "Booking added"}).encode())
            except Exception as e:
                self._set_headers(500)
                self.wfile.write(json.dumps({"error": str(e)}).encode())
            finally:
                cur.close()
                conn.close()
        else:
            self._set_headers(404)
            self.wfile.write(b'{"error":"Not found"}')

if __name__ == "__main__":
    PORT = 8000
    print(f"Backend running at http://127.0.0.1:{PORT}")
    HTTPServer(("", PORT), Handler).serve_forever()
