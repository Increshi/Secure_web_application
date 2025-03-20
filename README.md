# Project TransactiWar – Battle for Security, Compete for Supremacy

## CS6903: Network Security, 2024-25
### Department of Computer Science and Engineering

## Team Members
- Rushikeshwar Reddy Payasam - CS24MTECH11018
- Yedla Jagadish Kumar - CS24MTECH11022
- Madhan V - CS24MTECH11021
- Vignesh S - CS24MTECH12007
- Sriram Dharmarajan - CS25MTECH02002

## Setup Instructions

Docker is configured to create containers for front-end, back-end and database.

1. Run the compose yaml file to start the containers using docker command:
```
docker-compose up --build -d
```

2. To check the database, open a new terminal and use command:
```
docker exec -it mysql_db mysql -u rushi -p
```


It will open the SQL interface. Then run:
```
use app_db;
```

3. To shut down the application:
```
docker-compose down
```

## Access Information
- Access the front-end application: `https://localhost:443/`
- Access the back-end application: `localhost:8080`

## Resources Used
1. GeeksForGeeks
2. Udemy
3. https://owasp.org/www-community/attacks
4. ChatGPT

## Individual Contributions
- **Rushikeshwar Reddy Payasam**: Developed front-end and back-end for login page and dashboard page and testing
- **Yedla Jagadish Kumar**: Developed front-end and back-end for login page and dashboard page and testing
- **Madhan V**: Developed front-end and back-end for profile page and transfer money page and testing
- **Vignesh S**: Developed front-end and back-end for profile page and transfer money page and testing
- **Sriram Dharmarajan**: Developed front-end and back-end for profile page and transfer money page and testing
