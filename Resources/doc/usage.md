#Kali client usage

##Summary

 * [Dependencies](#dependencies)
 * [Sku](#sku)
    * [Allocate](#allocation-request-for-a-sku-code)
    * [Get a sku](#get-a-sku)
    * [Create a sku](#create-a-sku)
    * [Update a sku](#update-a-sku)
    * [Delete a sku](#delete-a-sku)
 
##Dependencies 

```php
$manager = $this->getContainer()->get('meup_kali_client.sku_manager');
```

###Allocation (request for a sku code)

```php
$sku = $manager->allocate($app_name); // $app_name is not required if defined in manager constructor
```

###Update a Sku

The update step has to be done after allocation and object persistance (to get object id).

```php
$sku
    ->setForeignId('object identifier')
    ->setForeignType('object type updated')
;
$updatedSku = $manager->update($sku);
```

###Get a Sku

```php
$sku = $manager->get($skuId);
```

###Create a Sku

```php
$sku = new Sku();
$sku
    ->setProject('my project')
    ->setForeignType('object type')
    ->setForeignId('object identifier')
;
$createdSku = $manager->create($sku);
```

###Delete a Sku

```php
$manager->delete($skuId);
```
