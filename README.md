# Invoice Management System

The Invoice Management System simplifies the creation, tracking, and oversight of invoices, providing businesses with a comprehensive toolset to manage permissions and roles effectively.

## Overview

Invoice Management System simplifies the creation, tracking, and oversight of invoices, providing businesses with a comprehensive toolset to manage permissions and roles effectively. Users can generate PDF invoices, export invoice data to Excel tables, and archive invoices for easy access. The system also includes robust reporting capabilities and notification features to keep users informed about important updates and milestones.

## Features

- **Invoice Generation**: Easily create professional PDF invoices.
- **Export Functionality**: Export invoice data to Excel tables for further analysis.
- **Archiving**: Archive invoices for easy access and reference.
- **Permission Management**: Robust role-based permissions to control access to features and data.
- **Reporting**: Generate detailed reports for better insights into invoice activities.
- **Notifications**: Stay informed about important updates and milestones through notification features.

## Usage

### Installation

1. Clone the repository:

   ```bash
   git clone https://github.com/omarhaitham19/invoice-management-system.git
   ```

2. Install dependencies:

   ```bash
   composer install
   npm install
   ```

3. Configure your environment variables:

   - Copy the `.env.example` file to `.env` and configure it with your database credentials and other settings.

4. Run migrations:

   ```bash
   php artisan migrate
   ```

5. Run database seeders:

   ```bash
   php artisan db:seed
   ```

6. Create a symbolic link for storage:

    ```bash
   php artisan storage:link
   ```

7. Compile assets:

   ```bash
   npm run dev
   ```

8. Start the server:

   ```bash
   
   php artisan serve
   ```

9. Demo credentials :
   email: omar@gmail.com
   password: 12345678
   
