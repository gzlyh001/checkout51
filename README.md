# checkout51

https://github.com/checkout51/c51-coding-challenge

# demonstration

http://forwardsynergies.com/

# installation

1, import DB data from doc/checkout51.sql

2, configurate config/config_pdo.php

3, set public as web root directory

# ajax url

/api/getOffers.html

request offer data from tb_offer.

/api/getOffers.html?sort=1

/api/getOffers.html?sort=2

/api/getOffers.html?sort=3

/api/getOffers.html?sort=4

sort=1 : ORDER BY `name` ASC

sort=2 : ORDER BY `name` DESC

sort=3 : ORDER BY `cash_back` ASC

sort=4 : ORDER BY `cash_back` DESC
