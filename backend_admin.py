import mysql.connector
import hashlib
import argparse
import csv

CONFIG = {
    'user':'root',
    'password':'your_mysql_password',
    'host':'127.0.0.1',
    'database':'flashhotel',
    'raise_on_warnings': True
}

def sha256_hex(s): return hashlib.sha256(s.encode('utf-8')).hexdigest()

def connect():
    return mysql.connector.connect(**CONFIG)

def add_admin(username, password, fullname=''):
    conn = connect()
    cur = conn.cursor()
    h = sha256_hex(password)
    cur.execute("INSERT INTO admins (username,password_hash,fullname) VALUES (%s,%s,%s) ON DUPLICATE KEY UPDATE password_hash=%s,fullname=%s",
                (username,h,fullname,h,fullname))
    conn.commit()
    cur.close(); conn.close()
    print("Admin added/updated.")

def list_bookings():
    conn = connect(); cur = conn.cursor(dictionary=True)
    cur.execute("SELECT * FROM bookings ORDER BY created_at DESC")
    for r in cur.fetchall():
        print(r)
    cur.close(); conn.close()

def export_bookings_csv(path='bookings_export.csv'):
    conn = connect(); cur = conn.cursor()
    cur.execute("SELECT id,guest_name,room_type,check_in,check_out,price FROM bookings")
    rows = cur.fetchall()
    with open(path,'w',newline='') as f:
        writer = csv.writer(f)
        writer.writerow(['id','guest_name','room_type','check_in','check_out','price'])
        writer.writerows(rows)
    print("Exported to", path)
    cur.close(); conn.close()

if __name__ == '__main__':
    p = argparse.ArgumentParser()
    p.add_argument('--add-admin', nargs=2, metavar=('username','password'))
    p.add_argument('--list-bookings', action='store_true')
    p.add_argument('--export-csv', action='store_true')
    args = p.parse_args()
    if args.add_admin:
        add_admin(args.add_admin[0], args.add_admin[1])
    if args.list_bookings:
        list_bookings()
    if args.export_csv:
        export_bookings_csv()
