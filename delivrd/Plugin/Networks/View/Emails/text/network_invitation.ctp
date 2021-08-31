Hi,
<?php echo (($network['CreatedByUser']['firstname'])? $network['CreatedByUser']['firstname'] .' '. $network['CreatedByUser']['lastname'] .' &lt;'. $network['CreatedByUser']['email'] .'&gt;': $network['CreatedByUser']['email']) ; ?> sent you an invitation to join him on Delivrd inventory &amp; order management software.
You will join his <?php echo $network['Network']['name']; ?> network.
Click the link below to sign up to Delivrd and join his network.
If you are already a Delivrd user, you can accept the invitation after you log in to Delivrd.
Invitation link: <?php echo $this->Html->url(array('controller' => 'networks', 'action' => 'signup', $hash), true); ?>

The Delivrd team.
delivrd.com