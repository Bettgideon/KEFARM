# KEFARM -SDG 2   Smart Farming & Food Security Platform (Kenya 🇰🇪)



**KEFARM** is a personal project developed to support **SDG 2: Zero Hunger** by leveraging technology to enhance **agricultural productivity, food distribution**, and **resource management** for farmers across Kenya. This project was built as part of an **individual learning journey assisted by PLP (Power Learn Project)**.

KEFARM provides a modern digital platform for farmers and agribusiness stakeholders to manage farm operations, track inventory, process orders and sales, and generate actionable reports — all in one responsive and user-friendly dashboard.




## 🚀 Key Features

- 📊 **Dashboard** with real-time summary stats
- 🌿 **Farm Management** module (crops, livestock, schedules)
- 📦 **Inventory Management** with:
  - Add/Edit/Delete items
  - Low stock alerts
  - Filtering, sorting, and pagination
- 🛒 **Orders & Sales** tracking module
- 👥 **User Management** (Role-based: Admin / User)
- 📈 **Reports** module with data summaries
- ⚙️ **Settings** & **FAQ**
- 🌗 **Time-based dynamic greeting**
- 📱 **Fully responsive UI** with mobile toggle navigation

*Also on process integrating an AI Chat assistant 
---

## 🛠️ Built With

- **PHP** – Backend development
- **MySQL** – Database storage
- **HTML5/CSS3** – Web structure & styling
- **JavaScript** – Frontend interactivity
- **MAMP/XAMPP** – Local testing environment

---

## 📁 Project Structure


KEFARM-Dashboard/
├── index.php
├── farm_management.php
├── orders_sales.php
├── inventory.php
├── reports.php
├── user_management.php
├── settings.php
├── faq.php
│
├── assets/
│ ├── styles.css
│ └── script.js
│
├── includes/
│ ├── header.php
│ ├── sidebar.php
│ └── footer.php
│
├── login.php
├── register.php
└── README.md


---

## 🔐 Role-Based Access

| Role        | Description                          |
|-------------|--------------------------------------|
| **Admin**   | Full access to all modules           |
| **User**    | Access to dashboard overview only    |

---

## 🧱 Sample Database Table: `users`

| Column     | Type      | Description             |
|------------|-----------|-------------------------|
| id         | INT       | Primary key             |
| name       | VARCHAR   | Full name               |
| email      | VARCHAR   | Email address           |
| password   | VARCHAR   | Hashed password         |
| role       | ENUM      | 'admin' or 'user'       |
| created_at | TIMESTAMP | Date of registration    |

---

## 🔮 Planned Improvements

- AI-based farming suggestions (climate-smart insights)
- SMS alerts for low stock or task reminders
- M-Pesa integration for order payments
- Graphical analytics (with Power BI / Chart.js)
- Multi-language support (English / Kiswahili)

---

## 👨‍💻 Developer

**Gideon Kiprotich Bett**  
Full-Stack Developer | ICT Enthusiast  
Built during the **Power Learn Project (PLP)** training journey  
📧 kiprotichgideonbett@gmail.com  
🌐 [My Portfolio](https://bettgideon-github-io.vercel.app)

---

## 📜 License

Open-source for learning and non-commercial development use.



