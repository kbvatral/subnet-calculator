<?php
  $ip = array($_GET["ip1"], $_GET["ip2"], $_GET["ip3"], $_GET["ip4"]);
  $subnets = $_GET["subnets"];
  $hosts = $_GET["hosts"];

  $ip_class = "";
  $remaining_bits = 0;

  //Calculate default subnet
  $default_subnet = array(255, 255, 255, 255);
  if($ip[0] == 0){
    //theres some problem
    echo "Theres a problem with the ip";
  }else if($ip[1] == 0){
    $default_subnet[1] = 0;
    $default_subnet[2] = 0;
    $default_subnet[3] = 0;
    $remaining_bits = 8*3;
  }else if($ip[2] == 0){
    $default_subnet[2] = 0;
    $default_subnet[3] = 0;
    $remaining_bits = 8*2;
  }else if($ip[3] == 0){
    $default_subnet[3] = 0;
    $remaining_bits = 8;
  }

  //echo $ip[0],".",$ip[1],".",$ip[2],".",$ip[3];

    //Calculate ip class
  if($ip[0] <= 126){
    $ip_class = "A";
  }else if($ip[0] >= 128 && $ip[0] < 192){
    $ip_class = "B";
  }else if($ip[0] >= 192 && $ip[0] < 224){
    $ip_class = "C";
  }else if($ip[0] >= 224 && $ip[0] < 240){
    $ip_class = "D";
  }else if($ip[0] >= 240 && $ip[0] <= 254){
    $ip_class = "E";
  }else{
    //theres a problem
    echo "Theres a problem with the ip";
  }

  $return_values = subnet_via_subnet($subnets, $remaining_bits, $default_subnet);

  $required_bits = $return_values[0];
  $network_bits = $return_values[1];
  $custom_subnet = $return_values[2];

  //Calculate subnet based on number of required hosts
  function subnet_via_host($hosts, $remaining_bits, $default_subnet){
    //calculate number of bits required for the hosts
    $required_bits = ceil(log(($hosts+2), 2));
    $network_bits = $remaining_bits - $required_bits;

    if($network_bits < 0){
      echo "There are not enough bits for your hosts";
    }

    $custom_subnet = calculate_custom_subnet($network_bits, $required_bits, $default_subnet);

    return array($required_bits, $network_bits, $custom_subnet);

  }
  function subnet_via_subnet($subnets, $remaining_bits, $default_subnet){
    //calculate number of bits required for given number of subnets
    $network_bits = ceil(log($subnets, 2));
    $required_bits = $remaining_bits - $network_bits;

    if($required_bits < 0){
      echo "There are not enough bits for your hosts";
    }

    $custom_subnet = calculate_custom_subnet($network_bits, $required_bits, $default_subnet);

    return array($required_bits, $network_bits, $custom_subnet);

  }
  function calculate_custom_subnet($network_bits, $required_bits, $default_subnet){
    $custom_subnet = $default_subnet;
    if($network_bits > 7 && $network_bits < 16){ //fills one 0 category
      for($i=0; $i<4; $i++){
        if($custom_subnet[$i] == 0){
          $custom_subnet[$i] = 255;
          break;
        }
        $network_bits -= 8;
      }
    }else if($network_bits > 15){
      $custom_subnet = array(255, 255, 255, 0);
      $network_bits -= 16;
    }
    if($network_bits != 0){ //if it is 0, the last ip category is all for hosts
      //echo $network_bits;
      $sum = 0;
      $power = 7;
      for($i=0; $i<$network_bits; $i++){
        $sum += pow(2, $power);
        $power--;
      }
      for($i=0; $i<4; $i++){
        if($custom_subnet[$i] == 0){
          $custom_subnet[$i] = $sum;
          break;
        }
      }
    }
    //echo $custom_subnet[0],".",$custom_subnet[1],".",$custom_subnet[2],".",$custom_subnet[3];
    return $custom_subnet;
  }
?>
<html>
<head>
  <title>Subnet Calculator</title>
</head>
<body>

Network Address: <?php echo $ip[0],".",$ip[1],".",$ip[2],".",$ip[3]; ?><br />
Address Class: <?php echo $ip_class; ?><br />
Default Subnet Mask: <?php echo $default_subnet[0],".",$default_subnet[1],".",$default_subnet[2],".",$default_subnet[3]; ?><br />
Custom Subnet Mask: <?php echo $custom_subnet[0],".",$custom_subnet[1],".",$custom_subnet[2],".",$custom_subnet[3]; ?><br />
Total Number of Subnets: <?php echo pow(2, $network_bits); ?><br />
Total Number of Host Addresses: <?php echo pow(2, $required_bits); ?><br />
Number of Usable Host Addresses: <?php echo (pow(2, $required_bits)-2); ?><br />
Number of Bits Borrowed: <?php echo $network_bits; ?><br />

<form action="subnet.php" method="get">
Ip Address: <input type="text" name="ip1"> . <input type="text" name="ip2"> . <input type="text" name="ip3"> . <input type="text" name="ip4"><br>
Number of subnets: <input type="text" name="subnets"><br>
Number of hosts: <input type="text" name="hosts"><br>
<input type="submit">
</form>

</body>
</html>
