import mysql.connector
from sense_hat import SenseHat
import datetime
import time



sense = SenseHat()


# Connect to database
mydb = mysql.connector.connect(
    host="192.168.178.146", # Om te testen voer eigen IP-adres in
    user="pi",
    password="temperatuursensor",
    port="3306",
    database="nerdygadgets"
)

# Meten van de temp, database update.
cursor = mydb.cursor();
while True:
    temp = round(sense.get_temperature() - 32, 2);
    now = datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S");

    sql = 'UPDATE coldroomtemperatures SET RecordedWhen = %s, Temperature = %s, ValidFrom = %s WHERE coldRoomSensorNumber = 5';
    val = (now, temp, now)

    cursor.execute(sql, val)
    mydb.commit()

    print(temp)
    time.sleep(3)
