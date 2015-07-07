#File Host - A web based file hosting.

A small website project previously written using CodeIgniter Framework for school project.  
Now is written using Laravel 4 Framework.

## Installation
1. Install laravel 4 using composer  
  ```
  composer create-project laravel/laravel [project-name] 4.2 --prefer-dist
  ```
2. Download the project to your local machine.
3. Extract it to your project and replace the files.
4. Configure laravel database config accordingly. Since laravel provides database migration you can choose between databases which is supported by laravel.
5. Do the migration using 'php artisan' by running this command  
  ```
  php artisan migrate  
  php artisan db:seed  
  ```
6. To start the server you can use this command  
  ```
  php artisan serve
  ```
  or using your own php server (e.g. xampp)
7. Then it should running a simple website which provide file hosting service.

## Features
1. Admin section which manage users and files.
2. Anonymous file upload, it will generate a link to download the file.
3. Registered user can do upload and look at the list of their files.
4. The user can edit his files currently only editing the file name.
5. Downloading the file simply using a link.

## To be added
1. File revision system
2. User groups to share files within (seems still a long way go xD)
3. File-folder system.

If you found any bugs please feel free to contact me at fendy.fendy95@gmail.com

## Changelog
v0.2 Added file revision feature (keeping the same link)
v0.1 first-release
