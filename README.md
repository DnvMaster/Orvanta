# 📦 Orvanta CRM - Развивайся Управляй Достигай
---

## Web Разработка

* **Николай Демко** ([@github_dnmaster](https://github.com/dnvmaster)

---

### 🛠️ Стек технологий
* **Backend:** PHP 8.3 + Laravel 13
* **Frontend:** Blade, Bootstrap 5.3, Js
* **Окружение:** Open Server (OSPanel), MySQL / phpMyAdmin

---

### 🚀 Быстрый старт (Инструкция по развертыванию)
* **Выполните эти шаги последовательно, чтобы запустить проект локально в среде Open Server:

---

#### 1. Клонирование репозитория
* **Откройте терминал в папке с вашими сайтами Open Server и выполните:

* **git clone https://github.com/DnvMaster/Orvanta.git

---

#### 2. Установка зависимостей PHP
* **composer install
*  **npm run build

---

#### 3. Настройка окружения
* **Создайте файл .env и скопируйте весь код из файла .env.example и вставте в файл .env
* **Создайте базу данных в phpmyadmin
* **Укажите в созданном файле (.env) имя базы данных, тип соединения, хост, имя пользователя и пароль 

---

#### 4. Генерация ключа приложения
** *Выполните в консоле Open Server команду php artisan key:generate

---

#### 5. Накат миграций и наполнение базы
** *Выполните в консоле Open Server команду php artisan migrate

