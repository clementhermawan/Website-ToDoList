chrome.runtime.onMessage.addListener(function(request, sender, sendResponse) {
    if (request.action === "checkTaskStatus") {
      fetch('http://localhost/UKK/Clement/tasks-api.php') // URL API Anda
        .then(response => response.json())
        .then(data => {
          let taskStatus = data.status; // Misalnya API mengembalikan status tugas
          sendResponse({status: taskStatus});
        })
        .catch(error => {
          sendResponse({status: "Error fetching tasks"});
        });
      return true; // Untuk memberitahu bahwa respons akan datang secara asinkron
    }
  });
  