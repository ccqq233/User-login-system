# User-login-system
Implement user login system using PHP and manage it using session mechanism.

---

1. db_config. php is used to establish a connection with MySQL, login.php is used to create forms for login pages, process_rogin.php is used to process login information, and welcome. php is used to display the interface after successful login and handle concurrent login.

2. The database user_System contains a total of sessions and users tables. The sessions table is used to store session information, with the id column set as the primary key and automatically incremented. Each time a session is created, a unique session id is generated, and the ip_ address is used to store the IP address of the current user, so that the user can know the IP address of the ongoing session during concurrent access. The users table is used to store user information, where the visit_count column is used to store the number of user visits, with an initial value of 0. Before use, the database and tables should be created correctly.

<p align="center">
  <img src="https://github.com/user-attachments/assets/71215641-df69-4be4-8f0a-6036782ee194" width="600">
</p>
<p style="text-align:center; font-size:12px;">
  Table sessions
</p>
<p style="text-align:center">![image2](https://github.com/user-attachments/assets/0ecf4fec-332f-4b6a-960d-272ac122701b)</p>
<p style="text-align:center">Table users</p>

3. After the user logs in, the login information entered by the user is compared with the account password in the users table. If it passes, the session ID, username, creation date, expiration date (one minute later), last access date, and current IP address are set, and redirected to welcome. php. The welcome page displays the number of times the user has visited this page. When the user clicks the Refresh button, it checks if the session has expired and reloads the page.

4. Every time the welcome page is loaded, search for session information for all current users and check if the expired_date is greater than the current time. If so, output the IP addresses where the current user has logged in and ask if they want to end their session; Otherwise, it indicates that the session has expired and will be deleted from the database. Then process other ongoing sessions based on the user's selection. If the user chooses to terminate their session, the record for that session will be deleted from the sessions table in the database.
<p style="text-align:center">![image3](https://github.com/user-attachments/assets/eb78e225-4dd3-413e-9045-eb08f13e35bb)</p>
<p style="text-align:center">Welcome page</p>
<p style="text-align:center">![image4](https://github.com/user-attachments/assets/69b7aee9-560a-4d33-9e73-949ea503efb8)</p>
<p style="text-align:center">Expired page</p>
