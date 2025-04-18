chrome.runtime.sendMessage({ action: "checkTaskStatus" }, (response) => {
    if (response && response.status && Array.isArray(response.status)) {
        const taskListDiv = document.getElementById("taskList");
        taskListDiv.innerHTML = ""; // Hapus data sebelumnya

        response.status.forEach((task) => {
            // Buat elemen list untuk setiap task
            const taskElement = document.createElement("li");
            taskElement.classList.add("task-item");

            // Wrapper untuk header task
            const taskHeader = document.createElement("div");
            taskHeader.classList.add("task-header");

            // Nama tugas
            const taskName = document.createElement("h3");
            taskName.classList.add("task-name");
            taskName.textContent = task.task_name;

            // Status tugas
            const taskStatus = document.createElement("span");
            taskStatus.classList.add("task-status", task.status.toLowerCase()); // Tambahkan class "completed" atau "pending"
            taskStatus.textContent = task.status;

            // Gabungkan nama dan status di header
            taskHeader.appendChild(taskName);
            taskHeader.appendChild(taskStatus);

            // Deskripsi tugas
            const taskDesc = document.createElement("p");
            taskDesc.classList.add("task-desc");
            taskDesc.textContent = task.description || "Tidak ada deskripsi";

            // Deadline tugas
            const taskDeadline = document.createElement("p");
            taskDeadline.classList.add("task-deadline");
            taskDeadline.textContent = `🕒 ${task.deadline}`;

            // Gabungkan semua elemen ke dalam taskElement
            taskElement.appendChild(taskHeader);
            taskElement.appendChild(taskDesc);
            taskElement.appendChild(taskDeadline);

            // Masukkan ke daftar tugas
            taskListDiv.appendChild(taskElement);
        });
    } else {
        alert("Error fetching tasks or no tasks available");
    }
});
