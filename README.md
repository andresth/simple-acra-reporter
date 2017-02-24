# A simple server backend for ACRA

## Requirements
Webserver with Apache and PHP.

## Installation
* Clone this repository to a directory on your webserver.
* Create a `.htpasswd` file with credentials for at least one user.  
  (i.e. `htpasswd -c -d .htpasswd username`)
* Change the `AuthUserFile` variable in `.htaccess` to match your `.htpasswd` file.

## Usage

### Report crashes
Setup ACRA according to the [basic setup instructions](https://github.com/ACRA/acra/wiki/BasicSetup) and use `http://your.domain/directory/report.php` as `formUri` parameter.

### View crash reports
Open `http://your.domain/directory/index.php` in your browser and login with the credentials created earlier.
