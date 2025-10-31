
#include <mysqlx/xdevapi.h>
#include <iostream>
using namespace std;
int main(){
  try{
    mysqlx::Session sess("127.0.0.1", 33060, "root", "your_mysql_password");
    mysqlx::Schema db = sess.getSchema("flashhotel");
    mysqlx::Table bookings = db.getTable("bookings");
    mysqlx::RowResult res = bookings.select("id","guest_name","room_type","check_in","check_out","price").execute();
    for(auto row : res){
      cout << "ID: " << row[0] << " Name: " << row[1] << " Room: " << row[2] << endl;
    }
    sess.close();
  } catch(const mysqlx::Error &err){
    cerr<<"Error: "<<err.what()<<endl;
  }
  return 0;
}
