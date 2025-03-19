# Bibliotech - Book Management System

## Description
Bibliotech is a web application that allows users to browse books, view book details, manage user accounts, and administer the system. The app integrates with Google Books API to fetch and display books.

## Routes

### Public Routes

- `/` - **Home Page**
  - Displays 30 books fetched from the Google Books API.

- `/book/{id}` - **Book Details**
  - Shows detailed information about a single book.

### Authentication Routes (Handled by Symfony)

- `/login` - **Login Page**
  - Allows users to log in.

- `/logout` - **Logout**
  - Logs the user out and redirects to the login page.

- `/register` - **User Registration**
  - Allows new users to create an account.

### User Routes

- `/user/{id}/profile` - **User Profile**
  - Displays the profile of the logged-in user.

- `/user/{id}/loans` - **User Loans**
  - Lists all the books borrowed by the logged-in user.
 
- `/user/{id}/profile` - **User Profile**
  - User Profile page.

- `/loan/return/{id]` - **Return a book**
  - Used to return a book.

- `/user/request/{id}` - **Loan a book**
  - Used to loan a book.
    
- `/user/{id}/pdf` - **Book PDF**
  - Download link of the pdf.


### Admin Routes (Restricted to Admin Users)

- `/admin` - **Admin Dashboard**
  - Only accessible to users with the `ROLE_ADMIN` role.
  - Provides tools for managing books, users, and other system settings.

## Technologies Used
- Symfony (PHP Framework)
- Tailwind CSS for UI Styling
- Google Books API for book data
- Internal database for users, loans and progressive books

## Installation & Setup
1. Clone the repository:
   ```sh
   git clone https://github.com/Ewillian9/bibliotech.git
   cd bibliotech
   ```
2. Install dependencies:
   ```sh
   composer install
   ```
3. Configure environment variables:
   ```sh
   .env.local .env
   ```
4. Run the application:
   ```sh
   symfony serve
   ```

## License
oui
