<?php
    class stadus {
        private $stadushost;
        private $staduslogin;
        private $staduspass;
        private $stadusname;
        public function conn($dbhost, $dblogin, $dbpass,$dbname) {
            $this->stadushost = $dbhost;
            $this->staduslogin = $dblogin;
            $this->staduspass = $dbpass;
            $this->stadusname = $dbname;
        }
        public function stat($logtxt,$logip)
        {
            $link = mysqli_connect($this->stadushost, $this->staduslogin, $this->staduspass);
            mysqli_select_db($link, $this->stadusname);
            $ip = md5($_SERVER["REMOTE_ADDR"]);
            if ($logip == TRUE)
            {
                $sqlIPtable = "CREATE TABLE IF NOT EXISTS `stadus_ip` ( `id` INT NOT NULL AUTO_INCREMENT , `ip` VARCHAR(32) NOT NULL , PRIMARY KEY (`id`))";
                mysqli_query($link, $sqlIPtable);
                $sqlIPcheck = "SELECT * FROM stadus_ip";
                $result = mysqli_query($link,$sqlIPcheck);
                if (mysqli_num_rows($result) == 0)
                {
                    $sqlIP = "INSERT INTO stadus_ip(id, ip) VALUES (null, '$ip')";
                    mysqli_query($link,$sqlIP);
                }
            }
            $sql0 = "SELECT * FROM stadus_days ORDER BY id DESC LIMIT 1";
            $sqlTable = "CREATE TABLE IF NOT EXISTS `stadus_days` ( `id` INT NOT NULL AUTO_INCREMENT , `day` VARCHAR(10) NOT NULL , `count` INT(11) NOT NULL , PRIMARY KEY (`id`))";
            mysqli_query($link, $sqlTable);
            $nDate = date('o-m-d');
            $result = mysqli_query($link, $sql0);
            if(mysqli_num_rows($result) == 0)
            {
                $sqlZeroRows = "INSERT INTO stadus_days (id, day, count) VALUES (null, '$nDate', '1')";
                mysqli_query($link,$sqlZeroRows);
                $result = mysqli_query($link, $sql0);
            }
            while($line=mysqli_fetch_assoc($result)){
                $id = $line['id'];
                $day = $line['day'];
                $count = $line['count'];
            }
            if ($day !== $nDate)
            {
                $sql = "INSERT INTO stadus_days (id, day, count) VALUES (null, '$nDate', '1')";
                mysqli_query($link, $sql);
                if($logip == TRUE)
                {
                $sql2 = "DROP TABLE stadus_ip";
                mysqli_query($link, $sql2);
                $sql3 = "CREATE TABLE IF NOT EXISTS `stadus_ip` ( `id` INT NOT NULL AUTO_INCREMENT , `ip` VARCHAR(32) NOT NULL , PRIMARY KEY (`id`))";
                mysqli_query($link,$sql3);
                }
            }
            $domain = $_SERVER['HTTP_HOST'];
            $nDateMD5 = md5($nDate);
            $result = mysqli_query($link, $sql0);

            while($line=mysqli_fetch_assoc($result)){
                $id = $line['id'];
                $count = $line['count'];
            }
            if($logip == TRUE)
            {
                if (!isset($_COOKIE['stadus']))
                {
                    setcookie('stadus', $nDateMD5, time() + 86400, "/", $domain);
                    $count = (int)$count;
                    $cookieip = "SELECT * FROM stadus_ip";
                    $rs = mysqli_query($link, $cookieip);
                    $cookip = FALSE;
                    while($nline=mysqli_fetch_assoc($rs)){
                        if($nline['ip'] == $ip)
                        {
                            $cookip = TRUE;
                        }
                    }
                    if($cookip == FALSE)
                    {
                        $count++;
                    }
                    $sql = "UPDATE stadus_days SET count='$count' WHERE id=$id";
                    mysqli_query($link, $sql);
                    if($logtxt == TRUE)
                    {
                        $file = fopen('logUsers.txt','a');
                        $txt = $_SERVER['HTTP_USER_AGENT'].":".$nDate."\r\n";
                        fwrite($file, $txt);
                        fclose($file);
                    }
                }
            }else{
                if (!isset($_COOKIE['stadus']))
                {
                    setcookie('stadus', $nDateMD5, time() + 86400, "/", $domain);
                    $count = (int)$count;
                    $count++;
                    $sql = "UPDATE stadus_days SET count='$count' WHERE id=$id";
                    mysqli_query($link, $sql);
                    if($logtxt == TRUE)
                    {
                        $file = fopen('logUsers.txt','a');
                        $txt = $_SERVER['HTTP_USER_AGENT'].":".$nDate."\r\n";
                        fwrite($file, $txt);
                        fclose($file);
                    }
                }
            }
            if ($logip == TRUE)
            {
                $sqlipadd = "SELECT * FROM stadus_ip";
                $result = mysqli_query($link,$sqlipadd);
                if (mysqli_num_rows($result) == 0)
                {
                    $ipadd = "INSERT INTO stadus_ip(id, ip) VALUES (null, '$ip')";
                    mysqli_query($link,$ipadd);
                }else{
                    $ipsearch = FALSE;
                    while($line=mysqli_fetch_assoc($result)){
                        if($line['ip'] == $ip)
                        {
                            $ipsearch = TRUE;
                        }
                    }
                    if($ipsearch == FALSE)
                    {
                        $ipadd = "INSERT INTO stadus_ip(id, ip) VALUES (null, '$ip')";
                        mysqli_query($link, $ipadd);
                    }
                }
            }
        }
        public function printStat(){
            $link = mysqli_connect($this->stadushost, $this->staduslogin, $this->staduspass);
            mysqli_select_db($link, $this->stadusname);
            $sqlStat = 'SELECT * FROM stadus_days ORDER BY id DESC LIMIT 1';
            $result = mysqli_query($link,$sqlStat);
            if (mysqli_num_rows($result) == 0)
            {
                $dataArray = array('No data','No data');
            }else{
                while($line=mysqli_fetch_assoc($result)){
                    $dataArray = array($line['day'],$line['count']);
                }
            }
            return $dataArray;
        }
    }