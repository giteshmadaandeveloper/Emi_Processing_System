# EMI-Processing-System

This is a Laravel-based EMI Processing System that helps manage and automate the processing of Equated Monthly Installments (EMIs). The system includes features for user authentication, EMI calculations.

## Setup Instructions

Follow the steps below to set up the project on your local environment:

### 1. Clone the Repository
```bash
git clone https://github.com/giteshmadaandeveloper/EMI-Processing-System.git
cd EMI-Processing-System
```

### 2. Install Dependencies
Run the following command to install all the required dependencies:
```bash
composer install
```

### 3. Configure Environment Variables
Copy the `.env.example` file to `.env`:
```bash
cp .env.example .env
```
Open the `.env` file and update the necessary database and application configurations.

### 4. Generate Application Key
Generate the application key using the following command:
```bash
php artisan key:generate
```

### 5. Run Migrations and Seeders
To set up the database schema and populate it with some initial data, run:
```bash
php artisan migrate:fresh --seed
```

### 6. Serve the Application
Finally, start the local development server:
```bash
php artisan serve
```

You can now access the application by visiting `http://127.0.0.1:8000` in your web browser.

## Additional Commands

- **Clear Cache:**
  ```bash
  php artisan cache:clear
  ```
  
- **Optimize Application:**
  ```bash
  php artisan optimize
  ```

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
