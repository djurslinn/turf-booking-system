# TurfZone

TurfZone is a web-based turf booking and management system. It provides a seamless platform for customers to browse, book, and manage sports turf slots, while allowing turf owners to list their venues, manage available slots, and track bookings. It also includes an admin dashboard for overall management.

## 🚀 Features

- **Multi-Role System:** Support for Customers, Turf Owners, and Admins.
- **Customer Portal:** Browse available turfs, book slots, view booking history, and manage profile.
- **Owner Dashboard:** Register as an owner, add and manage turf details, create time slots, and track customer bookings.
- **Admin Dashboard:** Centralized control over the platform's users and listings.
- **Google Authentication:** Users can easily sign up and log in using their Google account.
- **Email Notifications:** Automated email alerts for registration success, booking confirmations, and password resets (powered by PHPMailer).

## 🛠️ Tech Stack

- **Frontend:** HTML5, CSS3, Vanilla JavaScript
- **Backend:** PHP
- **Database:** MySQL

## ⚙️ Prerequisites

To run this project locally, you will need a local web server environment like [XAMPP](https://www.apachefriends.org/download.html), WAMP, or MAMP installed on your machine.

## 💻 Local Setup & Installation

Follow these steps to get your development environment running:

1. **Install XAMPP** (or your preferred local server) and start the **Apache** and **MySQL** services from the control panel.
2. **Clone or Download** this repository.
3. **Move the project** to your web server's root directory:
   - For XAMPP, move the `turf` folder into `C:\xampp\htdocs\`.
   - Your path should look like this: `C:\xampp\htdocs\turf`.
4. **Setup the Database:**
   - Open your web browser and go to `http://localhost/phpmyadmin`.
   - Create a new database named `turf`.
   - Select the newly created database and click the **Import** tab.
   - Choose the `turf (1).sql` file located in the root of this project and click **Import**.
5. **Database Configuration:**
   - Open `config.php` and verify your database connection settings. By default, it uses `localhost`, `root`, and no password.
   - *If your local MySQL setup has a password, update it here.*
6. **Run the Project:**
   - Open your browser and navigate to: `http://localhost/turf`

## 🔐 Configuring Email & Google Login

To use the Google Login and Email Notification features locally, you need to provide your own API credentials. 

**Email Configuration (PHPMailer):**
- In `register.php`, `owner_register.php`, and `forgot_password.php`, locate the PHPMailer settings.
- Replace `YOUR_EMAIL@gmail.com` with your actual Gmail address.
- Replace `YOUR_APP_PASSWORD` with a generated **Google App Password** (Do not use your standard email password). 
- *Note: Remember not to commit these credentials to GitHub!*

**Google Login Configuration:**
- In `google-callback.php`, replace `YOUR_GOOGLE_CLIENT_ID` and `YOUR_GOOGLE_CLIENT_SECRET` with credentials generated from your Google Cloud Console.

## 🤝 Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.
