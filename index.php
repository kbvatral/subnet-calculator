<?php
  include 'subnet.php';
?>
<!DOCTYPE html>
<html>
<title>Subnet Calculator</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="css/main.css" />
<body class="w3-content" style="max-width:100%">

  <div class="w3-row w3-dark-grey" id="content">
    <div class="w3-half w3-dark-grey w3-container w3-center" id="content">
      <div class="w3-padding-16">
        <h1>Network Information</h1>
        <form class="w3-container w3-card-2 w3-padding-32 w3-white" action="index.php" method="get">
          <div class="w3-section">
            <label>IP Address</label><br />
            <input class="v-input" style="width:18%;" type="text" name="ip1" maxlength="3" required> .
            <input class="v-input" style="width:18%;" type="text" name="ip2" maxlength="3" required> .
            <input class="v-input" style="width:18%;" type="text" name="ip3" maxlength="3" required> .
            <input class="v-input" style="width:18%;" type="text" name="ip4" maxlength="3" required>
          </div>
          <div class="w3-section">
            <label>Number of Subnets</label>
            <input id="sub_field" class="w3-input" style="width:100%;" type="text" name="subnets">
          </div>
          <div class="w3-section">
            <label>Number of Hosts</label>
            <input id="host_field" class="w3-input" style="width:100%;" type="text" name="hosts">
          </div>
          <div class="w3-section">
            <label>Subnet of Interest</label>
            <input class="w3-input" style="width:100%;" type="text" required name="subnet_of_interest">
          </div>
          <div class="w3-section">
            <input id="sub_radio" class="w3-radio" type="radio" name="calc" value="sub" checked onclick="changeRequired()">
            <label>Number of Subnets</label>
            <input id="host_radio" class="w3-radio" type="radio" name="calc" value="host" onclick="changeRequired()">
            <label>Number of Hosts</label>
          </div>
          <button type="submit" class="w3-button w3-teal w3-right">Calculate</button>
        </form>
      </div>

    </div>
    <div class="w3-half w3-teal w3-container" id="content">
      <div class="w3-padding-16 w3-padding-large">
        <h1>Results</h1>
        <div class="w3-padding-16">
          <table class="w3-table w3-bordered">
            <tr>
              <td>Network Address</td>
              <td><?php if(isset($ip)){print_ip($ip);} ?></td>
            </tr>
            <tr>
              <td>Address Class</td>
              <td><?php if(isset($ip_class)){echo $ip_class;} ?></td>
            </tr>
            <tr>
              <td>Default Subnet Mask</td>
              <td><?php if(isset($default_subnet)){print_ip($default_subnet);} ?></td>
            </tr>
            <tr>
              <td>Custom Subnet Mask</td>
              <td><?php if(isset($custom_subnet)){print_ip($custom_subnet);} ?></td>
            </tr>
            <tr>
              <td>Total Number of Subnets</td>
              <td><?php if(isset($num_subnets)){echo $num_subnets;} ?></td>
            </tr>
            <tr>
              <td>Total Number of Host Addresses</td>
              <td><?php if(isset($num_host)){echo $num_host;} ?></td>
            </tr>
            <tr>
              <td>Number of Usable Host Addresses</td>
              <td><?php if(isset($num_usable)){echo $num_usable;}  ?></td>
            </tr>
            <tr>
              <td>Number of Bits Borrowed</td>
              <td><?php if(isset($network_bits)){echo $network_bits;}  ?></td>
            </tr>

            <tr>
              <td>Subnet Network</td>
              <td><?php if(isset($subnet_network)){print_ip($subnet_network);} ?></td>
            </tr>
            <tr>
              <td>Subnet Start</td>
              <td><?php if(isset($subnet_start)){print_ip($subnet_start);} ?></td>
            </tr>
            <tr>
              <td>Subnet End</td>
              <td><?php if(isset($subnet_end)){print_ip($subnet_end);} ?></td>
            </tr>
            <tr>
              <td>Subnet Broadcast</td>
              <td><?php if(isset($subnet_broadcast)){print_ip($subnet_broadcast);} ?></td>
            </tr>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer class="w3-container w3-black w3-padding-16">
    <p class="w3-left w3-padding">Designed by <a href="https://github.com/kbvatral" target="_blank">Caleb Vatral</a> and <a href="https://github.com/JJCallahan" target="_blank">James Callahan</a></p>
    <p class="w3-right w3-padding">
      <a href="https://github.com/kbvatral/subnet-calculator" target="_blank" class="w3-button w3-dark-grey w3-hover-teal">View Source</a>
    </p>
  </footer>

</body>

<script>
changeRequired();

function changeRequired(){
  var radioButtonValue = document.getElementsByName("calc");
  var sub_field = document.getElementsByName("subnets");
  var hosts_field = document.getElementsByName("hosts");

  if(radioButtonValue[0].checked){
    //set required
    sub_field[0].setAttribute("required", "");
    //remove required
    hosts_field[0].removeAttribute("required");
  }else{
    //set required
    hosts_field[0].setAttribute("required", "");
    //remove required
    sub_field[0].removeAttribute("required");
  }

}
</script>
</html>
