#Kali client usage

##Summary

 * [Dependencies](#dependencies)
 * [Sku](#sku)
    * [Get a sku](#get-a-sku)
    * [Create a sku](#create-a-sku)
    * [Update a sku](#update-a-sku)
    * [Delete a sku](#delete-a-sku)
 
##Dependencies 

```php
$manager = $this->getContainer()->get('meup_kali_client.sku_manager');
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

###Get a Sku

```php
$sku = $manager->get($skuId);
```

###Update a Sku

```php
$sku = $manager->get($skuId);
$sku
    ->setForeignType('object type updated')
;
$updatedSku = $manager->update($sku);
```

###Delete a Sku

```php
$manager->delete($skuId);
```
