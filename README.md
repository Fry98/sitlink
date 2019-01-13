# SITLINK
**SITLINK** is a Slack-like chat app running on *jQuery* and *PHP*. It was written as my semestral project for **ZWA** on **CTU**. 

## Setup
Clone this repo into the *DocumentRoot* directory of your **Apache** server and then import the *sitlink_db.sql* schema into your local **MySQL** database. Lastly create *env.php* file in the ***/lib*** folder of this repository and inside of it declare variables **$MYSQL_DB**, **$MYSQL_USER**, **$MYSQL_PASSWD**, **$IMGUR_TOKEN** that store the name of your MySQL databse, your MySQL username, your MySQL password and your Imgur API token respectively.

## Credits
- **jQuery** - https://github.com/jquery/jquery
- **particles.js** - https://github.com/VincentGarreau/particles.js/

## License
**MIT**