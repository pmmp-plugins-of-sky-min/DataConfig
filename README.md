# DataConfig
PMMP 4.0

Save `yaml or json or txt` files asynchronously

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
1. $this->db = $this->data->__get(mixed $key);
2. $this->db = $this->data->data;
```

## set data
```
1. $this->data->__set(mixed $key, mixed $value);
2, $this->data->data = $array;
```

### $type
```php
Data::YAML
Data::JSON
Data::LIST
```
