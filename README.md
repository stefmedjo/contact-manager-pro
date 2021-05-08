# Contact Manager Pro
Contact Manager Pro is a web app which helps you to create and save 
your contact informations (email, address)

# Prerequisities

## Environment
* PHP 7.4
* MySQL
* composer 2
* Windows 10
* [Symfony CLI](https://symfony.com/download)
* [Bootstrap V5](https://getbootstrap.com/)

## Prepare the project
First, you will need to get the project. Open a terminal where you want to create the
project and type:

```bash
git clone https://github.com/stefmedjo/contact-manager-pro.git <your-project-name>
```
You will have to replace <your-project-name> with your project name (e.g. contact, address).

After, you can get inside the project.
```bash
cd <your-project-name>
```

Secondly, you will need to install dependencies. 
```bash
composer install
```
Finally, you will need to install all assets dependencies using npm or yarn:
```bash
npm install
```
or

```bash
yarn install
```

Everything is all ready now.

## Database

In order to configure your database, you will need to configure the .env file. 
First, rename .env.official to .env. After, write the username, password and database name and
in the terminal, type:
```bash
symfony console doctrine:database:create
```
or if you don't have the symfony CLI installed

```bash
php bin/console doctrine:database:create
```
You will need now to update the database. You will have to create migrations and migrate them to
the database.

```bash
symfony console make:migration
```
```bash
symfony console d:m:m
```

## Features
The features of Contact Manager Pro are:

* Category
  * Create a category of contact
  * Edit a category of contact
  * View a category of contact
  * List a category of contact
  * Delete a category of contact

* Contact
  * Create a contact
  * Edit a contact
  * View a contact
  * Delete a contact
  * List all contacts

# Todo
* Create form, view and list template for category entity.
* Create form, view and list template for contact entity.