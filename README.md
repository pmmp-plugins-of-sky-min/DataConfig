# DataConfig
PMMP 4.0

Save `yaml or json or txt or ini` files asynchronously

# How to use

## load Data
```php
public Data $data;

$this->data = new Data(string $fileName, $type, array $default);
```

## save data
```php
$this->data->save();
```

## get data
```php
$this->data->getAll();
$this->data->{key:magic method}
```

## set data
```
$this->data->setAll($array);
$this->data->{key:magic method} = $value;
```

### $type
```php
Data::AUTO
Data::YAML
Data::JSON
Data::LIST
Data::INI
```
