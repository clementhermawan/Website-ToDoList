# 📝 To-Do List Web App with Google Extension Support

A powerful and simple web-based To-Do List application built using **HTML, CSS, JavaScript, PHP**, and **MySQL**. This application helps users efficiently manage their daily tasks, categorize them, and receive timely reminders — all accessible directly via a **Google Chrome Extension**.

---

## 🚀 Features

- ✅ **Add Tasks** — Quickly add new to-dos to your list.  
- 🛠️ **Edit & Delete Tasks** — Modify or remove tasks at any time.  
- 📅 **Task Reminders** — Set reminders so you never miss an important task.  
- 🗂️ **Task Categories** — Organize tasks into categories for better clarity.  
- 🔍 **Search Functionality** — Easily find tasks by keyword or category.  
- 🧩 **Google Chrome Extension Integration** — View today’s tasks without opening the full website.  

---

## 🛠️ Technologies Used

- **Frontend**: HTML, CSS, JavaScript  
- **Backend**: PHP  
- **Database**: MySQL  
- **Browser Extension**: Google Chrome Extension (Manifest v3)  

---

## 🌐 How It Works

1. **Web Application**  
   Users interact with the main to-do list platform through a clean and intuitive interface where they can manage all tasks, reminders, and categories.

2. **Chrome Extension**  
   A lightweight Chrome extension displays today’s tasks instantly, allowing users to check or review their tasks straight from the browser — no need to open the full website.

---

## 📂 Folder Structure

📁 todo-list-app/  
├── 📁 css/              → Styling files  
├── 📁 js/               → JavaScript for frontend behavior  
├── 📁 php/              → PHP backend scripts (CRUD operations, etc.)  
├── 📁 extension/        → Chrome extension files  
│   ├── manifest.json  
│   ├── popup.html  
│   ├── popup.js  
│   └── style.css  
├── 📁 assets/           → Images, icons, etc.  
├── 📄 index.html        → Homepage of the to-do list  
├── 📄 db_config.php     → Database configuration  
├── 📄 database.sql      → Sample SQL file to create and populate the DB  
└── 📄 README.md         → Project documentation  

---

## ⚙️ Installation & Setup

### 1. Clone this repository

```bash
git clone https://github.com/clementhermawan/Website-ToDoList.git
```

### 2. Import the database

- Open `phpMyAdmin` or your preferred MySQL client  
- Create a new database (e.g., `todo_app`)  
- Import `database.sql` file into the database  

### 3. Configure database connection

Open `db_config.php` and edit the MySQL credentials:

```php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "todo_app";
```

### 4. Run on local server

- Use tools like **XAMPP**, **MAMP**, or **WAMP**  
- Place the project folder in the `htdocs` (or equivalent) directory  
- Open browser and visit: `http://localhost/todo-list-app/`  

### 5. Install Chrome Extension

- Open Google Chrome  
- Go to: `chrome://extensions`  
- Enable **Developer Mode**  
- Click **Load unpacked**  
- Select the `extension/` folder from the project directory  

---

## 🧪 Example Tasks

| ID | Task Name        | Category  | Due Date   | Reminder  |
|----|------------------|-----------|------------|-----------|
| 1  | Finish homework  | School    | 2025-04-20 | 09:00 AM  |
| 2  | Pay electricity  | Bills     | 2025-04-18 | 07:00 PM  |
| 3  | Meeting with Bob | Work      | 2025-04-19 | 03:00 PM  |

---

## 📸 Screenshots

_Add screenshots here to showcase the web UI and Chrome Extension interface._

---

## 📌 Future Improvements

- [ ] User authentication / login  
- [ ] Task notifications via email  
- [ ] Dark mode support  
- [ ] Task sharing with others  

---

## 🙋‍♂️ Author

**Your Name**  
GitHub: [@clementhermawan](https://github.com/clementhermawan)

---

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---
