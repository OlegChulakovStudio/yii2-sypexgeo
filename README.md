Yii 2 Sypexgeo Component
===============================
На основе функционала http://sypexgeo.net. 

Sypex Geo - product for location by IP address. Obtaining the IP address, Sypex Geo outputs information 
about the location of the visitor - country, region, city, geographical coordinates and other in Russian 
and in English. Sypex Geo use local compact binary database file and works very quickly. For more 
information visit: https://sypexgeo.net/

Поставляется вместе со справочниками Sypexgeo https://sypexgeo.net/files/SxGeo_Info.zip от 18.05.2017. 
При изменении справочников следует вручную скачать их и поместить в папку `vendor/chulakov/yii2-sypexgeo/src/migrations/data`.
Перечень файлов:
```
city.tsv
region.tsv
country.tsv
```

Установка 
---------
+ Для подключения компонента необходимо добавить в `composer.json` следующие строки:
```
"require": {
    "oleg-chulakov-studio/yii2-sypexgeo": "~1.0.0"
}
```

+ Добавить новый контроллер в конфигурационный файл консольной части приложения.
```
'controllerMap' => [
    ...
    'sypexgeo' => [
        'class' => 'chulakov\sypexgeo\console\controllers\SypexgeoController',
        'importOptions' => [
            'sourceUrl' => 'https://sypexgeo.net/files',  // URL адрес сервера, на котором лежат .dat файлы
            'infoMode' => true,                           // Флаг необходимости вывода информационных сообщений в stdout
            'dataDir' => '@app/runtime/sypexgeo',     // Относительный путь к каталогу, куда сохранять файлы .dat
        ],
    ]
]
```

+ Выполнить миграции командой
```
php yii migrate --migrationPath=@vendor/oleg-chulakov-studio/yii2-sypexgeo/src/migrations
```

+ Выполнить импорт файлов .dat с помощью команды
```
php yii sypexgeo/import
```
В дальнейшем данную команду можно использовать для запуска в `cron`. Команда имеет необязательный параметр `type`, 
указав который можно скачать требуемый ZIP файл:
```
'country'     ->  'SxGeoCountry.zip'
'city_cp1251' ->  'SxGeoCity_cp1251.zip'
'city_utf8'   ->  'SxGeoCity_utf8.zip'
```

+ Добавить компонент в параметры приложения
```    
'components' => [
    ...
    'sypexgeo' => [
        'class' => 'chulakov\sypexgeo\Sypexgeo',
        'dataFile' => '@app/runtime/sypexgeo/SxGeoCity.dat',     // Относительный путь к файлу .dat
    ]
],
```
