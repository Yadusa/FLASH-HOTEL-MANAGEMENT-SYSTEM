// login.cpp (requires libcurl)
#include <iostream>
#include <string>
#include <curl/curl.h>

int main() {
    CURL *curl = curl_easy_init();
    if (!curl) {
        std::cerr << "curl init failed\n";
        return 1;
    }

    std::string url = "http://localhost:5000/admin/login";
    // form data
    std::string postFields = "username=admin&password=Admin%40123"; // url-encode in production

    curl_easy_setopt(curl, CURLOPT_URL, url.c_str());
    curl_easy_setopt(curl, CURLOPT_POSTFIELDS, postFields.c_str());
    CURLcode res = curl_easy_perform(curl);
    if(res != CURLE_OK)
        std::cerr << "curl error: " << curl_easy_strerror(res) << "\n";

    curl_easy_cleanup(curl);
    return 0;
}
