<?php
  $ip = array($_GET["ip1"], $_GET["ip2"], $_GET["ip3"], $_GET["ip4"]);
  $subnets = $_GET["subnets"];
  $hosts = $_GET["hosts"];
  $subnet_of_interest = $_GET["subnet_of_interest"];
  $calc_based_on = $_GET["calc"];

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

  if($calc_based_on == "sub"){
    $return_values = subnet_via_subnet($subnets, $remaining_bits, $default_subnet);
  }else{
    $return_values = subnet_via_host($hosts, $remaining_bits, $default_subnet);
  }

  $required_bits = $return_values[0];
  $network_bits = $return_values[1];
  $custom_subnet = $return_values[2];


  $subnet_return = get_subnet($subnet_of_interest, $ip, $required_bits);

  $subnet_network = $subnet_return[0];
  $subnet_start = $subnet_return[1];
  $subnet_end = $subnet_return[2];
  $subnet_braodcast = $subnet_return[3];

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
      }
      $network_bits -= 8;
    }else if($network_bits > 15){
      $custom_subnet = array(255, 255, 255, 0);
      $network_bits -= 16;
    }
    if($network_bits != 0){ //if it is 0, the last ip category is all for hosts
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
    return $custom_subnet;
  }
  function get_subnet($subnet_of_interest, $ip, $required_bits){
    //the number of hosts per subnet
    $hosts = pow(2,$required_bits);

    //initialize variables
    $subnet_ip = $ip; //the working ip address
    $subnet_network = $ip; //address of each subnet
    $subnet_start = $ip; //the first subnet usable address
    $subnet_end = $ip; //the last subnet usable address

    //we are looping through the ip's in each subnet utill we reach the end of the subnet of interest
    for($subnet_number=1;$subnet_number<=$subnet_of_interest;$subnet_number++){
      //have we reached the subnet of interest?
      if($subnet_number == $subnet_of_interest){
        //if so, the address is now the network address
        $subnet_network = $subnet_ip;
      }
      for($i=0;$i<$hosts;$i++){
        $subnet_ip[3]++;

        //check to see if each 8 bit set is overflowing
        if($subnet_ip[3] == 256){
          $subnet_ip[2]++;
          $subnet_ip[3]=0;
        }
        if($subnet_ip[2] == 256){
          $subnet_ip[1]++;
          $subnet_ip[2] = 0;
        }

        //if we are at the subnet of interest we store the second and second to last ip
        if($subnet_number == $subnet_of_interest){
          if($i==0){
            $subnet_start = $subnet_ip;
          }else if($i==($hosts-3)){
            $subnet_end = $subnet_ip;
          }else if($i==($hosts-2)){
            $subnet_braodcast = $subnet_ip;
          }
        }
      }
    }
    //now subnet_ip is broadcast of subnet we are interested in
    //$subnet_braodcast = $subnet_ip;

    return array($subnet_network, $subnet_start, $subnet_end, $subnet_braodcast);

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
Number of Bits Borrowed: <?php echo $network_bits; ?><br /><br  />

Subnet Network: <?php echo $subnet_network[0],".",$subnet_network[1],".",$subnet_network[2],".",$subnet_network[3]; ?><br />
Subnet Start: <?php echo $subnet_start[0],".",$subnet_start[1],".",$subnet_start[2],".",$subnet_start[3]; ?><br />
Subnet End: <?php echo $subnet_end[0],".",$subnet_end[1],".",$subnet_end[2],".",$subnet_end[3]; ?><br />
Subnet Broadcast: <?php echo $subnet_braodcast[0],".",$subnet_braodcast[1],".",$subnet_braodcast[2],".",$subnet_braodcast[3];; ?><br />

</body>
</html>
