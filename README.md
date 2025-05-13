🏗️ Project Goal: PHP Web App with EC2 + RDS + Load Balancer (ALB)
📌 Architecture Overview:
csharp
Copy
Edit
[Browser]
   ↓
[Application Load Balancer]
   ↓
[EC2 Instance(s) running PHP/Apache]
   ↓
[RDS MySQL Database]
✅ Step-by-Step Guide to Deploy LAMP App with EC2 + RDS + Load Balancer
🔹 PHASE 1: Prep – Launch the Core AWS Resources
1. Create RDS MySQL Database
Go to RDS → Create Database

Choose:

Engine: MySQL

Use case: Production (or Free Tier)

DB instance class: db.t3.micro (Free Tier)

DB name: testdb

Master username: admin

Master password: yourpassword

Enable public access (just for now)

In VPC security group, allow port 3306 from EC2 security group

Wait until DB is available

Note the RDS Endpoint (e.g., mydb.xxxxx.rds.amazonaws.com)

2. Create EC2 Ubuntu Instance
Go to EC2 → Launch instance

AMI: Ubuntu 22.04

Instance type: t2.micro

Key pair: Create/download

Security Group: Allow ports 22 (SSH) and 80 (HTTP)

Launch instance and note the public IP

3. Install LAMP Stack on EC2 (via iTerm)
SSH into the EC2 from iTerm:

bash
Copy
Edit
chmod 400 /path/to/key.pem
ssh -i /path/to/key.pem ubuntu@<EC2-IP>
Then run this script:

bash
Copy
Edit
sudo apt update
sudo apt install apache2 -y
sudo apt install mysql-client -y
sudo apt install php libapache2-mod-php php-mysql -y
sudo systemctl enable apache2
🔹 PHASE 2: Connect App to RDS
4. Create PHP Web App
bash
Copy
Edit
sudo nano /var/www/html/index.php
Paste this code (update DB creds):

<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (isset($_POST['submit'])) {
  $host = "your-rds-endpoint.rds.amazonaws.com"; // ✅ REPLACE THIS
  $user = "admin"; // ✅ Your RDS username
  $pass = "yourpassword"; // ✅ Your RDS password
  $db   = "testdb"; // ✅ Your database name

  // Establish connection
  $conn = new mysqli($host, $user, $pass, $db);

  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  $name = $_POST['name'];
  $stmt = $conn->prepare("INSERT INTO users (name) VALUES (?)");
  $stmt->bind_param("s", $name);
  $stmt->execute();

  echo "Saved!";
  $stmt->close();
  $conn->close();
}
?>

<!DOCTYPE html>
<html>
<body>
  <form method="post">
    Name: <input type="text" name="name"/>
    <input type="submit" name="submit"/>
  </form>
</body>
</html>



5. Create MySQL Table in RDS
From your EC2 terminal:

bash
Copy
Edit
mysql -h <RDS-ENDPOINT> -u admin -p
Then inside MySQL:

sql
Copy
Edit
CREATE DATABASE testdb;
USE testdb;
CREATE TABLE users (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100));
EXIT;
✅ Your app is now using RDS instead of local MySQL

🔹 PHASE 3: Add Load Balancer (ALB)
6. Create an Application Load Balancer
Go to EC2 → Load Balancers → Create Load Balancer

Choose Application Load Balancer

Name: lamp-alb

Scheme: Internet-facing

Listener: HTTP (port 80)

Select same VPC and Availability Zones

Create a Target Group:

Type: Instance

Protocol: HTTP

Port: 80

Register your EC2 instance

Finish Load Balancer setup

7. Test in Browser
Open:

pgsql
Copy
Edit
http://<Load-Balancer-DNS>/index.php
✅ You should see the form, and data will go into RDS.

🚀 Summary
Component	Role
EC2	Hosts Apache + PHP app
RDS	Remote database
ALB	Distributes traffic to EC2
iTerm	Used to SSH and manage EC2


