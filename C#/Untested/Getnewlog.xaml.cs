using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Windows;
using System.Windows.Controls;
using System.Windows.Data;
using System.Windows.Documents;
using System.Windows.Input;
using System.Windows.Media;
using System.Windows.Media.Imaging;
using System.Windows.Navigation;
using System.Windows.Shapes;
using System.Runtime.InteropServices;
using MySql.Data.MySqlClient;

namespace VMSGet
{
    /// <summary>
    /// Interaction logic for MainWindow.xaml
    /// </summary>
    public partial class MainWindow : Window
    {
        DataBase db = new DataBase();
        Controller ctr = new Controller();

        public MainWindow()
        {
            InitializeComponent();

            IntPtr connectStatus = IntPtr.Zero;
            List<String> errorList = new List<String>();
            List<Log> logList = new List<Log>();

            //Read devices ip and devices id from database
            db.ReadDevice();

            //Looping for read log from each devices
            foreach(var item in db.deviceList)
            {
                connectStatus = ctr.ConnectController(connectStatus, item.deviceIp);

                if(connectStatus != IntPtr.Zero)
                {
                    logList = ctr.ReadLog(connectStatus);
                    db.InsertLog(item.deviceId, logList);
                }
                else
                {
                    errorList.Add($"Unable connect to controller : {item.deviceIp}");
                }

                connectStatus = IntPtr.Zero;
            }

            //Add error list
            if(errorList.Count > 0)
            {
                errorListBox.ItemsSource = errorList;
            }
            else
            {
                this.Close();
            }
        }

        private void alertClick(object sender, MouseButtonEventArgs e)
        {
            this.Close();
        }
    }

    class Device
    {
        public String deviceIp { get; set; }
        public String deviceId { get; set; }

        public Device(String deviceIp, String deviceId)
        {
            this.deviceIp = deviceIp;
            this.deviceId = deviceId;
        }
    }

    class Log
    {
        public String cardNo { get; set; }
        public String pin { get; set; }
        public String verified { get; set; }
        public String doorId { get; set; }
        public String eventType { get; set; }
        public String inOutState { get; set; }
        public String timeSecond { get; set; }

        public Log(String cardNo, String pin, String verified, String doorId, String eventType, String inOutState, String timeSecond)
        {
            this.cardNo = cardNo;
            this.pin = pin;
            this.verified = verified;
            this.doorId = doorId;
            this.eventType = eventType;
            this.inOutState = inOutState;
            this.timeSecond = timeSecond;
        }
    }

    class DataBase
    {
        MySqlConnection connectDb = new MySqlConnection();
        public List<Device> deviceList = new List<Device>();
        Controller ctr = new Controller();

        public DataBase()
        {
            connectDb.ConnectionString = "datasource=127.0.0.1;port=3306;username=root;password=otadmin;database=viovera_access_control;";
        }

        //Read device ip and device id
        public void ReadDevice()
        {
            MySqlCommand readCommand = new MySqlCommand();

            readCommand.Connection = connectDb;
            readCommand.CommandText = "select device_ip,device_id from devices order by device_id asc";

            connectDb.Open();
            MySqlDataReader execReadCommand = readCommand.ExecuteReader();

            while (execReadCommand.Read())
            {
                String deviceIp = execReadCommand["device_ip"].ToString();
                String deviceId = execReadCommand["device_id"].ToString();

                deviceList.Add(new Device(deviceIp, deviceId));
            }
            connectDb.Close();
        }

        //Insert log to database
        public void InsertLog(String deviceId, List<Log> logList)
        {
            MySqlCommand insertCommand = new MySqlCommand();
            insertCommand.Connection = connectDb;

            connectDb.Open();
            foreach (var item in logList)
            {
                insertCommand.CommandText = $"insert into device_log(card_no,pin,verified,device_id,door_id,event_type,in_out_state,time_second) values('{item.cardNo}',{item.pin},'{item.verified}',{deviceId},{item.doorId},{item.eventType},{item.inOutState},'{item.timeSecond}')";
                insertCommand.ExecuteNonQuery();
            }
            connectDb.Close();
        }
    }

    class Dll
    {
        //import dll connect
        [DllImport("plcommpro.dll", EntryPoint = "Connect")]
        public static extern IntPtr Connect(string Parameters);
        [DllImport("plcommpro.dll", EntryPoint = "PullLastError")]
        public static extern int PullLastError();

        //import dll get device data
        [DllImport("plcommpro.dll", EntryPoint = "GetDeviceData")]
        public static extern int GetDeviceData(IntPtr h, ref byte buffer, int buffersize, string tablename, string filename, string filter, string options);
    }

    class Controller : Dll
    {
        //Connect to controller function
        public IntPtr ConnectController(IntPtr connectStatus, String deviceIp)
        {
            connectStatus = Connect($"protocol=TCP,ipaddress={deviceIp},port=4370,timeout=2000,passwd=");
            return connectStatus;
        }

        //Read log from controller
        public List<Log> ReadLog(IntPtr connectStatus)
        {
            List<Log> logList = new List<Log>();
            int BUFFERSIZE = 8 * 1024 * 1024;
            byte[] buffer = new byte[BUFFERSIZE];

            GetDeviceData(connectStatus, ref buffer[0], BUFFERSIZE, "transaction", "*", "", "NewRecord");

            List<String> logArray = Encoding.Default.GetString(buffer).Split('\n').ToList();
            logArray.RemoveAt(0);
            logArray.RemoveAt(logArray.Count - 1);

            //Insert log to logList
            foreach(String item in logArray)
            {
                String[] items = item.Split(',');

                //Create date and time format
                Double dblDateTime = Double.Parse(items[6]);
                int year = Convert.ToInt32(Math.Floor((dblDateTime / 32140800) + 2000));
                int month = Convert.ToInt32(Math.Floor((dblDateTime / 2678400) % 12 + 1));
                int day = Convert.ToInt32(Math.Floor((dblDateTime / 86400) % 31 + 1));
                int hour = Convert.ToInt32((dblDateTime / 3600) % 24);
                int minute = Convert.ToInt32((dblDateTime / 60) % 60);
                int second = Convert.ToInt32(dblDateTime % 60);

                items[6] = $"{year}-{month}-{day} {hour}:{minute}:{second}";

                logList.Add(new Log(items[0], items[1], items[2], items[3], items[4], items[5], items[6]));
            }

            return logList;
        }
    }
}
