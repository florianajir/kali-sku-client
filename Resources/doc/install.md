#Kali client configuration

To use the kali client you have to configure your application.

## Installation

---

Install the package with [Composer](http://getcomposer.org/) :

### Composer

```bash
composer config repositories.kali-client git 1001Pharmacies/kali-client
composer require 1001pharmacies/kali-client
```

Or update the `composer.json` file :

```json
#...
"repositories": [
    {
        "type": "git",
        "url": "http://github.com/1001Pharmacies/kali-client"
    }
],
#...
"require": {
    "1001pharmacies/kali-client": "dev-master"
}
#...
```

Update `app/AppKernel.php` :
        
```php
$bundles = array(
    // ...
    new Meup\Bundle\KaliClientBundle\MeupKaliClientBundle(),
);
```

---

## Config

`config.yml` :

```yml
meup_kali_client:
    app_name: marketplace
    kali_server: https://kali.1001pharmacies.com
    kali_public_key: your_public_key
    kali_secret_key: your_secret_key
    kali_ssl_cert: false|true|ssl_cert_filepath
```