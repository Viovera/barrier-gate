using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
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
        public MainWindow()
        {
            InitializeComponent();
        }

        private void main_Loaded(object sender, RoutedEventArgs e)
        {
            DataBase db = new DataBase();
            Controller ctrlr = new Controller();

            //get device ip and device id
            db.GetDeviceIp();

            //connect to controller
            ctrlr.ConnectController(db.device_ip);

            //skip when cant connect to controller
            if (ctrlr.h != IntPtr.Zero)
            {
                //get log from controller
                ctrlr.GetLog();

                //convert log to log array
                ctrlr.CreateArray();

                //insert log to database
                db.InsertDatabase(ctrlr.log);

                this.Close();
            }
            else
            {
                alert alrt = new alert();
                alrt.Show();
                this.Close();
            }
        }

        class Controller
        {
            //declarate public variable
            public IntPtr h = IntPtr.Zero;
            public String[,] log = new String[,] { { } };

            //declarate private variable
            byte[] buffer = { };

            //import dll connect
            [DllImport("plcommpro.dll", EntryPoint = "Connect")]
            public static extern IntPtr Connect(string Parameters);
            [DllImport("plcommpro.dll", EntryPoint = "PullLastError")]
            public static extern int PullLastError();

            //import dll get device data
            [DllImport("plcommpro.dll", EntryPoint = "GetDeviceData")]
            public static extern int GetDeviceData(IntPtr h, ref byte buffer, int buffersize, string tablename, string filename, string filter, string options);

            //connect to controller return h
            public void ConnectController(String device_ip)
            {
                string param;
                param = $"protocol=TCP,ipaddress={device_ip},port=4370,timeout=2000,passwd=";
                if (IntPtr.Zero == h)
                {
                    h = Connect(param);
                }
            }

            //get device data return buffer
            public void GetLog()
            {
                String devtablename = "transaction";
                String devdatfilter = "";

                int ret = 0;
                string str = "*";
                int BUFFERSIZE = 8 * 1024 * 1024;
                buffer = new byte[BUFFERSIZE];
                String options = "";

                if (IntPtr.Zero != h)
                {
                    ret = GetDeviceData(h, ref buffer[0], BUFFERSIZE, devtablename, str, devdatfilter, options);
                }
            }

            //create log array from GetLog()
            public void CreateArray()
            {
                String device_log = Encoding.Default.GetString(buffer);
                String[] device_log_array = device_log.Split('\n');
                log = new string[device_log_array.Length, 8];


                Double dbl_datetime;
                int year;
                int month;
                int day;
                int hour;
                int minute;
                int second;
                //String event_type = "";
                //String verified = "";
                //String in_out_state = "";


                if (device_log_array.Length > 2)
                {
                    for (int x = 0; x < device_log_array.Length - 1; x++)
                    {
                        if (x > 0)
                        {
                            for (int i = 0; i < 7; i++)
                            {
                                log[x, i] = device_log_array[x].Split(',')[i];
                                String b = log[x, i];

                                if (log[x, 6] != null)
                                {
                                    log[x, 6] = log[x, 6].Replace("\r", "");
                                }
                            }

                            //get date and time format
                            dbl_datetime = Double.Parse(log[x, 6]);
                            year = Convert.ToInt32(Math.Floor((dbl_datetime / 32140800) + 2000));
                            month = Convert.ToInt32(Math.Floor((dbl_datetime / 2678400) % 12 + 1));
                            day = Convert.ToInt32(Math.Floor((dbl_datetime / 86400) % 31 + 1));
                            hour = Convert.ToInt32((dbl_datetime / 3600) % 24) - 1;
                            minute = Convert.ToInt32((dbl_datetime / 60) % 60);
                            second = Convert.ToInt32(dbl_datetime % 60);

                            //change date and time format
                            log[x, 6] = $"{year}-{month}-{day} {hour}:{minute}:{second}";


                            //fungsi di bawah ini dinonaktifkan biar cepet insert ke databasenya
                            /*
                            //in out state
                            switch (log[x, 5])
                            {
                                case "0":
                                    in_out_state = "In";
                                    break;
                                case "1":
                                    in_out_state = "Out";
                                    break;
                                case "2":
                                    in_out_state = "Push Button";
                                    break;
                            }

                            //event type
                            switch (log[x, 4])
                            {
                                case "0":
                                    event_type = "Normal Punch Open";
                                    break;
                                case "1":
                                    event_type = "Punch during Normal Open Time Zone";
                                    break;
                                case "2":
                                    event_type = "First Card Normal Open (Punch Card)";
                                    break;
                                case "3":
                                    event_type = "Multi-Card Open (Punching Card)";
                                    break;
                                case "4":
                                    event_type = "Emergency Password Open";
                                    break;
                                case "5":
                                    event_type = "OPen during Normal Open Time Zone";
                                    break;
                                case "6":
                                    event_type = "Linkage Event Triggered";
                                    break;
                                case "7":
                                    event_type = "Cancel Alarm";
                                    break;
                                case "8":
                                    event_type = "Remote Opening";
                                    break;
                                case "9":
                                    event_type = "Remote Closing";
                                    break;
                                case "10":
                                    event_type = "Disable Intraday Normal Open Time Zone";
                                    break;
                                case "11":
                                    event_type = "Enable Intraday Normal Open Time Zone";
                                    break;
                                case "12":
                                    event_type = "Open Auxiliary Output";
                                    break;
                                case "13":
                                    event_type = "Close Auxiliary Output";
                                    break;
                                case "14":
                                    event_type = "Press Fingerprint Open";
                                    break;
                                case "15":
                                    event_type = "Multi-Card Open (Press Fingerprint)";
                                    break;
                                case "16":
                                    event_type = "Press Fingerprint during Normal Open Time Zone";
                                    break;
                                case "17":
                                    event_type = "Card plus Fingerprint Open";
                                    break;
                                case "18":
                                    event_type = "First Card Normal Open (Press Plus Fingerprint)";
                                    break;
                                case "19":
                                    event_type = "First Card Normal Open (Card Plus Fingerprint)";
                                    break;
                                case "20":
                                    event_type = "To Short Punch Interval";
                                    break;
                                case "21":
                                    event_type = "Door Inactive Time Zone (Punch Card)";
                                    break;
                                case "22":
                                    event_type = "Illegal Time Zone";
                                    break;
                                case "23":
                                    event_type = "Access Denied";
                                    break;
                                case "24":
                                    event_type = "Anti-Passback";
                                    break;
                                case "25":
                                    event_type = "Interlock";
                                    break;
                                case "26":
                                    event_type = "Multi-Card Authentication (Punching Card)";
                                    break;
                                case "27":
                                    event_type = "Unregistered Card";
                                    break;
                                case "28":
                                    event_type = "Opening Timeout";
                                    break;
                                case "29":
                                    event_type = "Card Expired";
                                    break;
                                case "30":
                                    event_type = "Password Error";
                                    break;
                                case "31":
                                    event_type = "To Short Fingerprint Pressing Interval";
                                    break;
                                case "32":
                                    event_type = "Multi-Card Authentication (Press Fingerprint)";
                                    break;
                                case "33":
                                    event_type = "Fingerprint Expired";
                                    break;
                                case "34":
                                    event_type = "Unregistered Fingerprint";
                                    break;
                                case "35":
                                    event_type = "Door Inactive Time Zone (Press Fingerprint)";
                                    break;
                                case "36":
                                    event_type = "Door Inactive Time Zone (Exit Button)";
                                    break;
                                case "37":
                                    event_type = "Failed to Close during Normal Open Time Zone";
                                    break;
                                case "101":
                                    event_type = "Duress Password Open";
                                    break;
                                case "102":
                                    event_type = "Opened Accidentrally";
                                    break;
                                case "103":
                                    event_type = "Duress Fingerprint Open";
                                    break;
                                case "200":
                                    event_type = "Door Opened Correctly";
                                    break;
                                case "201":
                                    event_type = "Door Closed Correctly";
                                    break;
                                case "202":
                                    event_type = "Exit Button Open";
                                    break;
                                case "203":
                                    event_type = "Multi-Card Open (Card Plus Fingerprint)";
                                    break;
                                case "204":
                                    event_type = "Normal Open Time Zone Over";
                                    break;
                                case "205":
                                    event_type = "Remote Normal Opening";
                                    break;
                                case "206":
                                    event_type = "Device Start";
                                    break;
                                case "220":
                                    event_type = "Auxiliary Input Disconnected";
                                    break;
                                case "221":
                                    event_type = "Auxiliary Input Shorted";
                                    break;
                                case "255":
                                    event_type = "Actually that obtain Door Status and Alarm Status";
                                    break;
                            }

                            //verified type
                            switch (log[x, 2])
                            {
                                case "1":
                                    verified = "Only Finger";
                                    break;
                                case "3":
                                    verified = "Only Password";
                                    break;
                                case "4":
                                    verified = "Only Card";
                                    break;
                                case "11":
                                    verified = "Card And Password";
                                    break;
                                case "200":
                                    verified = "Others";
                                    break;
                            }

                            log[x, 5] = in_out_state;
                            log[x, 4] = event_type;
                            log[x, 2] = verified;
                            */
                        }
                    }
                }
            }
        }

        class DataBase
        {
            //declarate public variable
            public String device_ip = "";
            public String device_id = "";

            //declarate private variable
            MySqlConnection koneksi = new MySqlConnection();

            public DataBase()
            {
                koneksi.ConnectionString = "datasource=127.0.0.1;port=3306;username=root;password=otadmin;database=viovera_access_control;";
                String[] device = new String[2];
            }

            //function count dinonaktifkan karena sudaqh menggunakana New Record
            /*
            //get count from database for device log validation
            public int count(String card_no, String time_second, String device_id)
            {
                MySqlCommand get_c = new MySqlCommand();
                get_c.Connection = koneksi;
                get_c.CommandType = CommandType.Text;
                get_c.CommandText = $"select count(id) from device_log where card_no='{card_no}' and time_second='{time_second}' and device_id='{device_id}'";
                koneksi.Open();
                int count = Convert.ToInt32(get_c.ExecuteScalar());
                koneksi.Close();
                return count;
            }
            */

            //get device ip from database
            public void GetDeviceIp()
            {
                MySqlCommand perintah_ambil = new MySqlCommand();
                perintah_ambil.Connection = koneksi;
                perintah_ambil.CommandText = "select device_ip,device_id from devices";
                String[] device = new String[2];
                koneksi.Open();
                MySqlDataReader execute_ambil = perintah_ambil.ExecuteReader();
                while (execute_ambil.Read())
                {
                    device[0] = execute_ambil["device_ip"].ToString();
                    device[1] = execute_ambil["device_id"].ToString();
                }
                koneksi.Close();
                device_ip = device[0];
                device_id = device[1];
            }

            //insert log to database
            public void InsertDatabase(String[,] dev_log)
            {
                MySqlCommand perintah = new MySqlCommand();
                perintah.Connection = koneksi;
                perintah.CommandText = "insert into device_log (card_no,pin,verified,device_id,door_id,event_type,in_out_state,time_second) values (@card_no,@pin,@verified,@device_id,@door_id,@event_type,@in_out_state,@time_second)";

                koneksi.Open();
                perintah.Parameters.AddWithValue("@card_no", "");
                perintah.Parameters.AddWithValue("@pin", "");
                perintah.Parameters.AddWithValue("@verified", "");
                perintah.Parameters.AddWithValue("@device_id", "");
                perintah.Parameters.AddWithValue("@door_id", "");
                perintah.Parameters.AddWithValue("@event_type", "");
                perintah.Parameters.AddWithValue("@in_out_state", "");
                perintah.Parameters.AddWithValue("@time_second", "");

                for (int x = 0; x < dev_log.GetLength(0) - 1; x++)
                {
                    String card_no = dev_log[x, 0];
                    String time_second = dev_log[x, 6];

                    perintah.Parameters["@card_no"].Value = dev_log[x, 0];
                    perintah.Parameters["@pin"].Value = dev_log[x, 1];
                    perintah.Parameters["@verified"].Value = dev_log[x, 2];
                    perintah.Parameters["@door_id"].Value = dev_log[x, 3];
                    perintah.Parameters["@event_type"].Value = dev_log[x, 4];
                    perintah.Parameters["@in_out_state"].Value = dev_log[x, 5];
                    perintah.Parameters["@time_second"].Value = dev_log[x, 6];
                    perintah.Parameters["@device_id"].Value = device_id;
                    perintah.ExecuteNonQueryAsync();
                }
                koneksi.Close();
            }
        }
    }
}
