# **💰 Currency Exchange Rate Management API**  

## **📖 Introduction**  
This is a **Laravel 12** and **Vue 3**-based application that fetches, stores, and manages **USD to LKR exchange rates** using an external API. It supports:  
- **Automatic rate fetching & storage**
- **Manual rate entry**
- **Filtering/searching by date and currency**
- **Historical average rates for the last 7 days**
- **RESTful API for frontend integration**

---

## **⚙️ System Requirements**  
Ensure your system meets the following requirements before setting up the project:  

### **Backend (Laravel 12 / PHP 8.2)**
- **PHP**: 8.2 or higher  
- **Composer**: Latest version  
- **Laravel**: 12  
- **Database**: MySQL or PostgreSQL  
- **XAMPP/WAMP/Docker** (for local development)  
- **Postman** (for API testing)  

### **Frontend (Vue 3)**
- **Node.js**: 16+  
- **NPM or Yarn**: Latest version  
- **Vue CLI** (for development)  

---

## **🛠️ Getting Started**  
Follow these instructions to set up and run the project locally.  

### **📌 Prerequisites**  
Ensure you have the following installed:  
- **Git**  
- **PHP 8.2**  
- **Composer**  
- **Node.js & NPM**  
- **MySQL or PostgreSQL**  

---

## **💚 Cloning the Repository**  
Clone this repository to your local machine and navigate into the project directory:  

```sh
# Clone the repository
git git clone https://Realpixels_Mart@bitbucket.org/realpixelstech/currency-exchange-rate.git
cd currency-exchange-backend
```

---

## **⚙️ Backend Setup (Laravel 12)**  

### **1⃣ Configure the Database**  
Create a new database (e.g., **exchange_rates_db**).  

### **2⃣ Set Up Environment Variables**  
Copy the `.env.example` file and rename it to `.env`:  
```sh
cp .env.example .env
```

Update the **database credentials** in `.env`:  
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=exchange_db
DB_USERNAME=root
DB_PASSWORD=
```

### **3⃣ Install Dependencies**  
```sh
composer install
```

### **4⃣ Generate Application Key**  
```sh
php artisan key:generate
```

### **5⃣ Run Migrations & Seeders**  
```sh
php artisan migrate --seed
```

---

## **🚀 Running the Backend API**  

### **Start the Laravel Server**  
```sh
php artisan serve
```
By default, the API will be available at:  
**http://127.0.0.1:8000**  

---

## **🎨 Frontend Setup (Vue 3)**  

### **1⃣ Navigate to the frontend directory**  
```sh
cd frontend
```

### **2⃣ Install Dependencies**  
```sh
npm install
```

### **3⃣ Run the Development Server**  
```sh
npm run dev
```
By default, the frontend will be available at:  
**http://localhost:5173**  

---

## **🛠️ API Endpoints**  

### **1⃣ Fetch Latest Exchange Rates**  
**GET /api/current-rate** 
**GET /api/current-rate/{{currency}}?{{date}}** 
Fetches the most recent USD to LKR exchange rate.  

### **2⃣ Get Exchange Rate for the Last 7 Days**  
**GET /api/{{currency}}/last-seven-days**  
Fetches historical exchange rates from **7 days before** the selected date and wekkly average .  

### **3⃣ Store a Manual Exchange Rate**  
**POST /api/manual-rate**  
```json
{
    "base_currency": "USD",
    "target_currency": "LKR",
    "rate": 1.1200,
    "date": "2025-03-24"
}
```
### **1⃣ user register**  
**POST /api/register** 
```json
{
  "name": "John Doe",
  "email": "john@example.com"
}
```

### **1⃣ user login**  
**POST /api/login** 
```json
{
  "email": "john@example.com"
}
```

---

## **🧪 Running Tests**  

### **1⃣ Set Up a Test Database**  
In `.env.testing`, configure a separate database:  
```env
DB_CONNECTION=mysql
DB_DATABASE=exchange_test
DB_USERNAME=root
DB_PASSWORD=
```

### **2⃣ Run Migrations for Testing**  
```sh
php artisan migrate --env=testing
```

### **3⃣ Execute Feature Tests**  
```sh
php artisan test
```

---

## **📄 Useful Artisan Commands**  

| Command | Description |
|---------|-------------|
| `php artisan migrate:refresh` | Refresh migrations (drop & re-run) |
| `php artisan optimize:clear` | Clear cache, config, routes, and views |
| `php artisan cache:clear` | Clear application cache |

---

## **📖 API Documentation**  
Detailed API documentation is available in the **Postman Collection**.  
🔗 **[API Docs](https://documenter.postman.com/preview/24328222-f8ef6e52-3205-4da0-b900-3dd4993eac51?environment=24328222-91554aaf-20fa-484a-beb9-704ff4329fba&versionTag=latest&apiName=CURRENT&version=latest&documentationLayout=classic-double-column&documentationTheme=light&logo=https%3A%2F%2Fres.cloudinary.com%2Fpostman%2Fimage%2Fupload%2Ft_team_logo%2Fv1%2Fteam%2Fanonymous_team&logoDark=https%3A%2F%2Fres.cloudinary.com%2Fpostman%2Fimage%2Fupload%2Ft_team_logo%2Fv1%2Fteam%2Fanonymous_team&right-sidebar=303030&top-bar=FFFFFF&highlight=FF6C37&right-sidebar-dark=303030&top-bar-dark=212121&highlight-dark=FF6C37)**  

---

## **👨‍💻 Author**  
**Arun Pragash Alwar**  
---
Bitbucket: [](https://bitbucket.org/realpixelstech/currency-exchange-rate/src/main/)

