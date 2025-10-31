/* JavaJDBCExample.java
   Requires MySQL Connector/J on classpath.
*/
import java.sql.*;
public class JavaJDBCExample {
  public static void main(String[] args) throws Exception {
    String url = "jdbc:mysql://127.0.0.1:3306/flashhotel";
    String user = "root";
    String pass = "your_mysql_password";
    Connection conn = DriverManager.getConnection(url,user,pass);
    Statement st = conn.createStatement();
    ResultSet rs = st.executeQuery("SELECT id,guest_name,room_type,check_in,check_out,price FROM bookings");
    while(rs.next()){
      System.out.printf("ID %d - %s (%s) %s -> %s : %.2f\n",
        rs.getInt("id"),
        rs.getString("guest_name"),
        rs.getString("room_type"),
        rs.getDate("check_in"),
        rs.getDate("check_out"),
        rs.getDouble("price"));
    }
    rs.close(); st.close(); conn.close();
  }
}
