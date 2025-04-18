chrome.runtime.onMessage.addListener(function(request, sender, sendResponse) {
    if (request.action === "checkTaskStatus") {
      fetch('http://localhost/UKK/Clement/tasks.php') // Ubah ke URL yang sesuai
        .then(response => response.json())
        .then(data => {
          console.log("API Response:", data); // Log respons API
          sendResponse({status: data});
        })
        .catch(error => {
          console.error("Error fetching tasks:", error); // Log error jika terjadi masalah saat mengambil data
          sendResponse({status: "Error fetching tasks"});
        });
      return true; // Untuk memberitahu bahwa respons akan datang secara asinkron
    }
  });
  