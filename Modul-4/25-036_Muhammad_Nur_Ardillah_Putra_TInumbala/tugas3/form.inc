<?php

function pesanError($errors, $nama)
{
    if (isset($errors[$nama]))
        echo ' <b>(' . htmlspecialchars($errors[$nama]) . ')</b>';
}
function terpilih($nama, $nilai) { return (isset($_POST[$nama]) && $_POST[$nama] === $nilai) ? ' selected' : ''; }
function tercentang($nama, $nilai) { return (isset($_POST[$nama]) && $_POST[$nama] === $nilai) ? ' checked' : ''; }

$daftarState = array(
    '1' => 'Australian Capital Territory', '2' => 'Queensland', '3' => 'Victoria',
    '4' => 'New South Wales', '5' => 'Tasmania', '6' => 'South Australia',
    '7' => 'Western Australia', '8' => 'Northern Territory'
);
?>
<form name="myForm" action="form.php" method="POST">
  <div>
    <label for="surname">Surname</label>
    <input type="text" id="surname" name="surname" size="31" value="<?php echo isian('surname'); ?>"/>
    <?php pesanError($errors, 'surname'); ?>
  </div>
  <div>
    <label for="email">Email address</label>
    <input type="text" id="email" name="email" size="31" value="<?php echo isian('email'); ?>"/>
    <?php pesanError($errors, 'email'); ?>
  </div>
  <div>
    <label for="passwd">Password</label>
    <input type="password" id="passwd" name="passwd" size="31"/>
    <?php pesanError($errors, 'passwd'); ?>
  </div>
  <div>
    <label for="address">Street Address</label>
    <textarea id="address" name="address" rows="3" cols="22"><?php echo isian('address'); ?></textarea>
    <?php pesanError($errors, 'address'); ?>
  </div>
  <div>
    <label for="state">State</label>
    <select id="state" name="state">
      <option value="-1">[choose your state]</option>
      <?php foreach ($daftarState as $kode => $nama)
          echo "<option value=\"$kode\"" . terpilih('state', (string)$kode) . ">$nama</option>\n      "; ?>
    </select>
    <?php pesanError($errors, 'state'); ?>
  </div>
  <div>
    <input type="hidden" name="country" value="Australia"/>
  </div>
  <div>
    <label for="sex">Gender</label>
    <input type="radio" name="sex" value="male"<?php echo tercentang('sex', 'male'); ?>/>Male
    <input type="radio" name="sex" value="female"<?php echo tercentang('sex', 'female'); ?>/>Female
    <?php pesanError($errors, 'sex'); ?>
  </div>
  <div>
    <label for="vegetarian">Vegetarian?</label>
    <input type="checkbox" id="vegetarian" name="vegetarian"<?php echo isset($_POST['vegetarian']) ? ' checked' : ''; ?>/>
  </div>
  <div>
    <label>&nbsp;</label>
    <input type="submit" value="Submit" name="submit"/><input type="reset" value="Reset" name="reset"/>
  </div>
</form>
