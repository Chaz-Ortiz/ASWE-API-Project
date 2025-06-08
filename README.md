# Inventory Management Website and RESTful API

[Visit the Website](https://ec2-3-145-77-233.us-east-2.compute.amazonaws.com/web/index.php)

This project is a full-stack **Inventory Management System**, consisting of a responsive web interface and a RESTful API. It is designed to help users **track, search, and manage inventory assets**.

Built using **HTML/CSS/JavaScript, PHP, and MySQL**, the project is split into two core directories:

- `/web` — the front-end website for users
- `/API` — the back-end API used by the website and other services

---

## Screenshots

![Search by Manufacturer - local](https://github.com/user-attachments/assets/6042c7d3-2739-46ec-afff-5d9a66a2aad1)
![Modify Device - local](https://github.com/user-attachments/assets/bc25d8ca-572a-4d22-9424-25db7c6d74e3)
![Add Equipment - local](https://github.com/user-attachments/assets/90e77312-6ff0-4d67-a0fa-fe173a64e0ff)

---

## Website Features (`/web`)

The web interface provides a platform for users to interact with the database without direct SQL access.

### Search Equipment

- Search inventory records by device type, manufacturer, or serial number
- DataTable integration for sorting, searching, and pagination
- Filters out inactive devices

### Add Equipment

- Input forms to add new devices to the database
- Validates entries before submission

### Responsive Design

- Bootstrap-based layout
- Navigation bar for easy access to all tools

---

## API Features (`/API`)

The backend is a custom-built RESTful API written in PHP, designed to handle all database operations securely and modularly.

### Endpoints Overview

- **GET /device/get.php** – Fetch all active devices or by specific criteria
- **POST /device/add.php** – Add new inventory items
- **PUT /device/update.php** – Update existing device details
- **DELETE /device/delete.php** – Mark a device as inactive or delete from system

### Core Design Principles

- **Modular endpoint design** with gateway routing
- **Security-first approach**: prepared statements, input sanitization
- **MySQL backend** using MySQL

---

## Getting Started

### Prerequisites

Ensure the following are installed and properly configured on your server or development machine:

- [PHP 7.4+](https://www.php.net/)
- [MySQL 5.7+](https://www.mysql.com/)
- [Nginx](https://www.nginx.com/)
- [phpMyAdmin](https://www.phpmyadmin.net/) (optional, for database administration)

### Setup

1. **Clone the repository**
   ```bash
   git clone https://github.com/your-username/inventory-management-system.git
   cd inventory-management-system

---

## Author

**Chaz Ortiz**
[GitHub](https://github.com/Chaz-Ortiz) · [LinkedIn](https://www.linkedin.com/in/chaz-ortiz-615863270/) 


