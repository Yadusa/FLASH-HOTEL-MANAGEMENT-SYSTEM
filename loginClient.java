// LoginClient.java (requires Java 11+)
import java.net.URI;
import java.net.http.*;
import java.net.http.HttpRequest.BodyPublishers;
import java.net.URLEncoder;
import java.nio.charset.StandardCharsets;

public class LoginClient {
    public static void main(String[] args) throws Exception {
        HttpClient client = HttpClient.newHttpClient();
        String form = "username=" + URLEncoder.encode("admin", StandardCharsets.UTF_8)
                    + "&password=" + URLEncoder.encode("Admin@123", StandardCharsets.UTF_8);

        HttpRequest req = HttpRequest.newBuilder()
            .uri(URI.create("http://localhost:5000/admin/login"))
            .header("Content-Type", "application/x-www-form-urlencoded")
            .POST(BodyPublishers.ofString(form))
            .build();

        HttpResponse<String> resp = client.send(req, HttpResponse.BodyHandlers.ofString());
        System.out.println("Status: " + resp.statusCode());
        System.out.println(resp.body());
    }
}
